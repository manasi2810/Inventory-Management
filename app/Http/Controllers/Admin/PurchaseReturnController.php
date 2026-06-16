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

                return view('admin.purchase.return', compact('purchase'));
            }
  

        public function store(Request $request, $id)
            {
                DB::beginTransaction();

                try {

                    $purchase = Purchase::with('items.product', 'vendor')
                        ->findOrFail($id);
                if ($purchase->status == 'short_closed') {
                            throw new \Exception(
                                'Purchase return is not allowed for Short Closed PO.'
                            );
                        }

                        if (!in_array($purchase->status, ['partial', 'received'])) {
                            throw new \Exception(
                                'Purchase return is allowed only for Partial or Received PO.'
                            );
                        }
                    $return = PurchaseReturn::create([
                        'purchase_id' => $purchase->id,
                        'vendor_id'   => $purchase->vendor_id,
                        'return_date' => now(),
                        'total_amount' => 0,
                        'note' => 'Purchase Return Against PO : ' . $purchase->invoice_no,
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

                        // Ordered / Received Qty
                        $receivedQty = PurchaseReceiveItem::where(
                        'product_id',
                        $item['product_id']
                    )
                    ->whereHas('receive', function ($q) use ($purchase) {
                        $q->where('purchase_id', $purchase->id);
                    })
                    ->sum('received_qty');

                        // Previous Returned Qty
                        $returnedQty = PurchaseReturnItem::where(
                            'product_id',
                            $item['product_id']
                        )
                        ->whereHas('purchaseReturn', function ($query) use ($purchase) {
                            $query->where('purchase_id', $purchase->id);
                        })
                        ->sum('qty');

                        $availableQty = $receivedQty - $returnedQty;

                        if ($returnQty > $availableQty) {

                            throw new \Exception(
                                "Return Qty ({$returnQty}) cannot exceed Available Qty ({$availableQty}) for Product : {$purchaseItem->product->name}"
                            );
                        }
                        $currentStock =
                            \App\Models\StockIn::where('product_id', $item['product_id'])
                                ->sum('qty')
                            -
                            \App\Models\StockOut::where('product_id', $item['product_id'])
                                ->sum('qty');

                        if ($returnQty > $currentStock) {
                            throw new \Exception(
                                "Only {$currentStock} stock available for return."
                            );
                        }

                        $price = (float) $item['price'];

                        $subtotal = $returnQty * $price;

                        $totalReturn += $subtotal;

                        // Return Item
                        PurchaseReturnItem::create([
                            'purchase_return_id' => $return->id,
                            'product_id' => $item['product_id'],
                            'qty' => $returnQty,
                            'price' => $price,
                            'subtotal' => $subtotal,
                        ]); 
                            // Stock Out
                        StockOut::create([
                            'product_id' => $item['product_id'],
                            'qty' => $returnQty,
                            'type' => 'purchase_return',
                            'reference_id' => $return->id,
                            'reason' => 'Purchase Return - ' . $purchase->invoice_no,
                            'created_by' => Auth::id(),
                        ]);
                    }

                    if ($totalReturn <= 0) {

                        throw new \Exception(
                            'Please enter at least one Return Quantity.'
                        );
                    }

                    // Update Return Master
                    $return->update([
                        'total_amount' => $totalReturn
                    ]);

                    /*
                    |--------------------------------------------------------------------------
                    | Vendor Ledger Entry
                    |--------------------------------------------------------------------------
                    */

                    $lastBalance = VendorLedger::where(
                        'vendor_id',
                        $purchase->vendor_id
                    )->latest('id')->value('balance_after');

                    if ($lastBalance === null) {
                        $lastBalance = $purchase->vendor->opening_balance;
                    }

                    $newBalance = $lastBalance - $totalReturn;

                    if ($newBalance < 0) {
                        throw new \Exception(
                            'Purchase return cannot make vendor outstanding negative.'
                        );
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

                    return redirect()
                        ->route('Purchase')
                        ->with(
                            'success',
                            'Purchase Return Completed Successfully.'
                        );

                } catch (\Exception $e) {

                    DB::rollBack();

                    return redirect()
                        ->back()
                        ->withInput()
                        ->with(
                            'error',
                            $e->getMessage()
                        );
                }
            }

    } 
