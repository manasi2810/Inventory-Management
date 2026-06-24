<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\PurchaseReturn;
use App\Models\PurchaseReturnItem;
use App\Models\PurchaseReceiveItem;
use App\Models\StockOut;
use App\Models\VendorLedger;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use App\Models\StockIn; 

class PurchaseReturnController extends Controller
{
      public function create($id)
            {
                $purchase = Purchase::with('items.product')
                    ->findOrFail($id);
            
                if ($purchase->status == 'short_closed') {
                    return back()->with(
                        'error',
                        'Purchase return is not allowed for Short Closed PO.'
                    );
                }
            
               foreach ($purchase->items as $item) {

                    // ✔ ACTUAL RECEIVED FROM GRN
                    $received = PurchaseReceiveItem::whereHas('receive', function ($q) use ($id) {
                            $q->where('purchase_id', $id);
                        })
                        ->where('product_id', $item->product_id)
                        ->sum('received_qty');
                
                    // ✔ ACTUAL RETURNED
                    $returned = PurchaseReturnItem::whereHas('purchaseReturn', function ($q) use ($id) {
                            $q->where('purchase_id', $id);
                        })
                        ->where('product_id', $item->product_id)
                        ->sum('qty');
                
                    // ✔ AVAILABLE = RECEIVED - RETURNED
                    $available = max(0, $received - $returned);
                
                    $item->received_qty = $received;
                    $item->returned_qty = $returned;
                    $item->available_return = $available;
                }
            
                return view('Admin.Purchase.return', compact('purchase'));
            }
              

      public function store(Request $request, $id)
            {
                DB::beginTransaction();
            
                try {
            
                    $purchase = Purchase::with('items.product', 'vendor')
                        ->findOrFail($id);
            
                    if ($purchase->status == 'short_closed') {
                        throw new \Exception('Purchase return not allowed for Short Closed PO.');
                    }
            
                    if (!in_array($purchase->status, ['partial', 'received'])) {
                        throw new \Exception('Purchase return allowed only for Partial or Received PO.');
                    }
            
                    $return = PurchaseReturn::create([
                        'purchase_id'  => $purchase->id,
                        'vendor_id'    => $purchase->vendor_id,
                        'return_date'  => now(),
                        'total_amount' => 0,
                        'note'         => 'Purchase Return Against PO : ' . $purchase->invoice_no,
                    ]);
            
                    $totalReturn = 0;
            
                    foreach ($request->items as $item) {
            
                        $returnQty = (float) ($item['return_qty'] ?? 0);
            
                        if ($returnQty <= 0) {
                            continue;
                        }
            
                        $purchaseItem = PurchaseItem::where('purchase_id', $purchase->id)
                            ->where('product_id', $item['product_id'])
                            ->first();
            
                        if (!$purchaseItem) {
                            continue;
                        }
            
                        /*
                        |-----------------------------------------
                        | ERP CORRECT LOGIC (NO SUM TABLES)
                        |-----------------------------------------
                        */
                        $availableQty = $purchaseItem->qty_received - $purchaseItem->qty_returned;
            
                        if ($returnQty > $availableQty) {
                            throw new \Exception(
                                "Return Qty ({$returnQty}) exceeds available qty ({$availableQty}) for Product : {$purchaseItem->product->name}"
                            );
                        }
            
                        /*
                        |-----------------------------------------
                        | STOCK CHECK
                        |-----------------------------------------
                        */
                        $currentStock =
                            StockIn::where('product_id', $item['product_id'])->sum('qty')
                            -
                            StockOut::where('product_id', $item['product_id'])->sum('qty');
            
                        if ($returnQty > $currentStock) {
                            throw new \Exception("Only {$currentStock} stock available for return.");
                        }
            
                        $price = (float) $item['price'];
                        $subtotal = $returnQty * $price;
            
                        $totalReturn += $subtotal;
            
                        /*
                        |-----------------------------------------
                        | RETURN ITEM (HISTORY)
                        |-----------------------------------------
                        */
                        PurchaseReturnItem::create([
                            'purchase_return_id' => $return->id,
                            'product_id'         => $item['product_id'],
                            'qty'                => $returnQty,
                            'price'              => $price,
                            'subtotal'           => $subtotal,
                        ]);
            
                        /*
                        |-----------------------------------------
                        | STOCK OUT
                        |-----------------------------------------
                        */
                        StockOut::create([
                            'product_id'   => $item['product_id'],
                            'qty'          => $returnQty,
                            'type'         => 'purchase_return',
                            'reference_id' => $return->id,
                            'reason'       => 'Purchase Return - ' . $purchase->invoice_no,
                            'created_by'   => Auth::id(),
                        ]);
            
                        /*
                        |-----------------------------------------
                        | UPDATE PURCHASE ITEM (FINAL TRUTH)
                        |-----------------------------------------
                        */
                        $purchaseItem->qty_returned += $returnQty;
                        $purchaseItem->save();
                    }
            
                    if ($totalReturn <= 0) {
                        throw new \Exception('Please enter at least one Return Quantity.');
                    }
            
                    $return->update([
                        'total_amount' => $totalReturn
                    ]);
            
                    /*
                    |-----------------------------------------
                    | VENDOR LEDGER
                    |-----------------------------------------
                    */
                    $lastBalance = VendorLedger::where('vendor_id', $purchase->vendor_id)
                        ->latest('id')
                        ->value('balance_after') ?? $purchase->vendor->opening_balance;
            
                    $newBalance = $lastBalance - $totalReturn;
            
                    if ($newBalance < 0) {
                        throw new \Exception('Vendor outstanding cannot go negative.');
                    }
            
                    VendorLedger::create([
                        'vendor_id'      => $purchase->vendor_id,
                        'entry_type'     => 'DEBIT',
                        'amount'         => $totalReturn,
                        'reference_type' => 'PURCHASE_RETURN',
                        'reference_id'   => $return->id,
                        'balance_after'  => $newBalance,
                        'note'           => 'Purchase Return Against PO ' . $purchase->invoice_no,
                        'created_by'     => Auth::id(),
                    ]);
            
                    DB::commit();
            
                    return redirect()->route('Purchase')
                        ->with('success', 'Purchase Return Completed Successfully.');
            
                } catch (\Exception $e) {
            
                    DB::rollBack();
            
                    return back()
                        ->withInput()
                        ->with('error', $e->getMessage());
                }
            }

    } 
