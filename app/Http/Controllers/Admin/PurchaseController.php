<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller; 
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Vendor;
use App\Models\Product;
use App\Models\StockIn;
use App\Models\PurchaseReceive;
use App\Models\PurchaseReceiveItem;
use App\Models\InventoryLog;
use DB;

class PurchaseController extends Controller
{

            public function __construct()
                {
                    $this->middleware('permission:purchase.view')
                        ->only(['index', 'show']);

                    $this->middleware('permission:purchase.create')
                        ->only(['create', 'store']);

                    $this->middleware('permission:purchase.edit')
                        ->only(['edit', 'update']);

                    $this->middleware('permission:purchase.delete')
                        ->only(['destroy']);

                    $this->middleware('permission:purchase.approve')
                        ->only(['approve']);

                    $this->middleware('permission:purchase.print')
                        ->only(['print', 'multiPrint']);

                    $this->middleware('permission:purchase.receive')
                        ->only(['receive', 'storeReceive']);

                    $this->middleware('permission:purchase.short-close')
                        ->only(['shortClose']);
                }

            /**
             * Display all Purchase Orders.
             */
            public function index()
                {
                    $purchases = Purchase::with('vendor')
                        ->orderBy('id', 'desc')
                        ->get();
                    return view('admin.purchase.index', compact('purchases'));
                } 

            /**
             * Show Create Purchase Order Form.
             */
            public function create()
                {
                    $vendors = Vendor::all();
                    $products = Product::all();
                    $year = date('Y');
                    $lastPurchase = Purchase::whereYear('created_at', $year)->orderBy('id', 'desc')->first();
                    $newNumber = $lastPurchase && $lastPurchase->invoice_no
                        ? ((int) substr($lastPurchase->invoice_no, -4)) + 1
                        : 1;
                    $invoice_no = 'PO' . $year . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
                    return view('admin.purchase.create', compact('vendors', 'products', 'invoice_no'));
                }

            /**
             * Display Purchase Order Details.
             */
            public function show($id)
                {
                    $purchase = Purchase::with('vendor', 'items.product')->findOrFail($id);
                    return view('admin.purchase.show', compact('purchase'));
                }


            /**
             * Open Purchase Receive Page.
             */
            public function receive($id)
                {
                    $purchase = Purchase::with('items.product', 'vendor')->findOrFail($id);
                    if (in_array($purchase->status, ['received', 'short_closed'])) {
                    return back()->with('error', 'This PO cannot be received.');
                    }
                    foreach ($purchase->items as $item) {
                        $receivedQty = PurchaseReceiveItem::whereHas('receive', function ($q) use ($id) {
                        $q->where('purchase_id', $id);
                        })
                        ->where('product_id', $item->product_id)
                        ->sum('received_qty');
                        $item->already_received = $receivedQty;
                        $item->remaining_qty = $item->qty - $receivedQty;
                    }
                    return view('admin.purchase.receive', compact('purchase'));
                }


             /**
             * Save New Purchase Order.
             */ 
            public function store(Request $request)
                {
                    DB::beginTransaction(); 
                    try { 
                        $purchase = Purchase::create([
                            'vendor_id'     => $request->vendor_id,
                            'invoice_no'    => $request->invoice_no,
                            'purchase_date' => $request->purchase_date,
                            'total_amount'  => 0,
                            'status'        => 'pending',
                        ]); 
                        $grandTotal = 0; 
                        foreach ($request->items as $item) { 
                            $total = $item['qty'] * $item['price']; 
                            PurchaseItem::create([
                                'purchase_id' => $purchase->id,
                                'product_id'  => $item['product_id'],
                                'qty'         => $item['qty'],
                                'price'       => $item['price'],
                                'total'       => $total,
                            ]);

                            $grandTotal += $total;
                        }

                        $purchase->update([
                            'total_amount' => $grandTotal
                        ]);

                        DB::commit();

                        return redirect()
                            ->route('Purchase')
                            ->with('success', 'Purchase Order Created Successfully');

                    } catch (\Exception $e) {

                        DB::rollback();

                        return back()->with('error', $e->getMessage());
                    }
                }


            /**
             * Save Received Items and Update Stock.
             */
            public function storeReceive(Request $request, $id)
                    {
                        DB::beginTransaction();  
                        try { 
                            $purchase = Purchase::findOrFail($id); 
                            if (in_array($purchase->status, ['received', 'short_closed'])) {
                                throw new \Exception("This PO is already closed.");
                            } 
                            $oldStatus = $purchase->status;
                            $isFullyReceived = true; 
                            $receive = PurchaseReceive::create([
                                'purchase_id'  => $purchase->id,
                                'receive_date' => now(),
                                'status'       => 'completed',
                            ]); 
                            foreach ($request->items as $item) { 
                                $productId  = $item['product_id'];
                                $ordered    = (int) $item['ordered_qty'];
                                $receiveNow = (int) $item['received_qty']; 
                                if ($receiveNow <= 0) continue; 
                                $alreadyReceived = PurchaseReceiveItem::whereHas('receive', function ($q) use ($id) {
                                        $q->where('purchase_id', $id);
                                    })
                                    ->where('product_id', $productId)
                                    ->sum('received_qty');

                                $remaining = $ordered - $alreadyReceived; 
                                if ($receiveNow > $remaining) {
                                    throw new \Exception("Over receiving not allowed");
                                }

                                $totalReceived = $alreadyReceived + $receiveNow;
                                $shortQty = max(0, $ordered - $totalReceived);

                                if ($shortQty > 0) $isFullyReceived = false;

                                PurchaseReceiveItem::create([
                                    'purchase_receive_id' => $receive->id,
                                    'product_id' => $productId,
                                    'ordered_qty' => $ordered,
                                    'received_qty' => $receiveNow,
                                    'short_qty' => $shortQty,
                                    'price' => $item['price'] ?? 0,
                                ]);
        
                                // Product::where('id', $productId)->increment('opening_stock', $receiveNow);
                                    $lastBalance = \App\Models\StockLedger::where('product_id', $productId)
                                    ->orderBy('id', 'desc')
                                        ->value('balance_after') ?? 0; 
                                    $newBalance = $lastBalance + $receiveNow;

                                    \App\Models\StockLedger::create([
                                        'product_id'    => $productId,
                                        'movement_type' => 'IN',
                                        'qty'           => $receiveNow,
                                        'reference_type'=> 'purchase',
                                        'reference_id'  => $purchase->id,
                                        'balance_after' => $newBalance,
                                        'created_by'    => auth()->id(),
                                    ]);

                                    StockIn::create([
                                    'product_id'   => $productId,
                                    'qty'          => $receiveNow,
                                    'type'         => 'purchase',
                                    'reference_id' => $purchase->id,
                                    'po_no'        => $purchase->invoice_no,  
                                    'created_by'   => auth()->id(),
                                ]);
        
                                InventoryLog::create([
                                    'purchase_id' => $purchase->id,
                                    'product_id' => $productId,
                                    'action_type' => 'receive',
                                    'qty' => $receiveNow,
                                    'remarks' => 'Stock received',
                                    'created_by' => auth()->id(),
                                ]);
                            } 
                            $newStatus = $isFullyReceived ? 'received' : 'partial'; 
                            $purchase->update(['status' => $newStatus]); 
                            InventoryLog::create([
                                'purchase_id' => $purchase->id,
                                'action_type' => 'status_change',
                                'status_from' => $oldStatus,
                                'status_to' => $newStatus,
                                'remarks' => 'PO status updated',
                                'created_by' => auth()->id(),
                            ]); 
                            DB::commit(); 
                            return redirect()->route('Purchase')->with('success', 'Purchase received successfully');

                        } catch (\Exception $e) {
                            DB::rollback();
                            return back()->with('error', $e->getMessage());
                        }
                    } 
 
                    /**
                     * Short Close Remaining Purchase Quantity.
                     */
                    public function shortClose($id)
                        {
                            $purchase = Purchase::findOrFail($id);
                            if (in_array($purchase->status, ['received', 'short_closed'])) {
                                return back()->with('error', 'Cannot short close');
                            }  
                            $oldStatus = $purchase->status; 
                            DB::beginTransaction(); 
                            try { 
                            $items = PurchaseItem::where('purchase_id', $purchase->id)->get();  
                            foreach ($items as $item) { 
                            $receivedQty = PurchaseReceiveItem::whereHas('receive', function ($q) use ($purchase) {
                                    $q->where('purchase_id', $purchase->id);
                                })
                                ->where('product_id', $item->product_id)
                                ->sum('received_qty'); 
                            $remainingQty = $item->qty - $receivedQty; 
                            if ($remainingQty > 0) { 
                                InventoryLog::create([
                                    'purchase_id' => $purchase->id,
                                    'product_id' => $item->product_id,   
                                    'action_type' => 'short_close',
                                    'qty' => $remainingQty,              
                                    'amount' => $item->price * $remainingQty,
                                    'status_from' => $oldStatus,
                                    'status_to' => 'short_closed',
                                    'remarks' => 'Remaining qty cancelled on short close',
                                    'created_by' => auth()->id(),
                                ]);
                            }
                        } 
                        $purchase->update(['status' => 'short_closed']); 
                        DB::commit(); 
                        return redirect()->route('Purchase')->with('success', 'PO short closed'); 
                    } catch (\Exception $e) { 
                        DB::rollback();
                        dd($e->getMessage());
                    }
                }
  
            /**
             * Print Single Purchase Order.
             */
            public function print($id)
                {
                    $purchase = Purchase::with('vendor', 'items.product')
                        ->findOrFail($id); 
                    return view('admin.purchase.print', compact('purchase'));
                }

            /**
             * Print Multiple Purchase Orders.
             */
            public function multiPrint(Request $request)
                {
                    $ids = explode(',', $request->ids); 
                    $purchases = Purchase::with('vendor', 'items.product')
                        ->whereIn('id', $ids)
                        ->get(); 
                    return view('admin.purchase.multi-print', compact('purchases'));
                }

}