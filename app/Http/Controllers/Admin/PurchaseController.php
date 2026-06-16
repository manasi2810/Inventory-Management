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
use App\Models\VendorLedger;
use App\Models\StockLedger;
use Exception;
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
                        $purchase = Purchase::with('items.product', 'vendor')
                            ->findOrFail($id);

                        // Prevent receive on closed PO
                        if (in_array($purchase->status, ['received', 'short_closed'])) {
                            return back()->with(
                                'error',
                                'This Purchase Order cannot be received.'
                            );
                        }

                        foreach ($purchase->items as $item) {

                            $alreadyReceived = PurchaseReceiveItem::whereHas(
                                'receive',
                                function ($q) use ($id) {
                                    $q->where('purchase_id', $id);
                                }
                            )
                            ->where('product_id', $item->product_id)
                            ->sum('received_qty');

                            $item->already_received = $alreadyReceived;

                            $item->remaining_qty =
                                max(0, $item->qty - $alreadyReceived);
                        }

                        return view(
                            'admin.purchase.receive',
                            compact('purchase')
                        );
                    }


             /**
             * Save New Purchase Order.
             */ 
            public function store(Request $request)
                {
                    DB::beginTransaction(); 
                    try { 

                    $productIds = [];

                        foreach ($request->items as $item) {

                            if (in_array($item['product_id'], $productIds)) {
                                throw new \Exception(
                                    'Duplicate products are not allowed in same PO.'
                                );
                            }

                            $productIds[] = $item['product_id'];
                        }
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

        $purchase = Purchase::with('vendor')->findOrFail($id);

        $vendor = $purchase->vendor;

        /*
        |-----------------------------------------
        | 1. CHECK PO STATUS
        |-----------------------------------------
        */
        if (in_array($purchase->status, ['received', 'short_closed'])) {
            throw new Exception('This PO is already closed.');
        }

        /*
        |-----------------------------------------
        | 2. VALIDATE AT LEAST ONE ITEM
        |-----------------------------------------
        */
        $hasReceiveQty = false;

        foreach ($request->items as $item) {
            if ((float)$item['received_qty'] > 0) {
                $hasReceiveQty = true;
                break;
            }
        }

        if (!$hasReceiveQty) {
            throw new Exception('Please enter at least one receive quantity.');
        }

        /*
        |-----------------------------------------
        | 3. CALCULATE TOTAL RECEIVE AMOUNT (ERP)
        |-----------------------------------------
        */
        $totalReceiveAmount = 0;

        foreach ($request->items as $item) {
            if ((float)$item['received_qty'] > 0) {
                $totalReceiveAmount += $item['received_qty'] * $item['price'];
            }
        }

        /*
        |-----------------------------------------
        | 4. GET OUTSTANDING (MODEL BASED)
        |-----------------------------------------
        */
        $currentOutstanding = $vendor->getOutstandingAmount();

        $availableCredit = $vendor->credit_limit - $currentOutstanding;

        /*
        |-----------------------------------------
        | 5. CREDIT LIMIT CHECK (GLOBAL ERP RULE)
        |-----------------------------------------
        */
        if (($currentOutstanding + $totalReceiveAmount) > $vendor->credit_limit) {

            throw new Exception(
                'Credit limit exceeded. Available Credit: ₹' . number_format($availableCredit, 2)
            );
        }

        /*
        |-----------------------------------------
        | 6. CREATE RECEIVE MASTER
        |-----------------------------------------
        */
        $receive = PurchaseReceive::create([
            'purchase_id'  => $purchase->id,
            'receive_date' => now(),
            'status'       => 'completed',
        ]);

        /*
        |-----------------------------------------
        | 7. PROCESS ITEMS
        |-----------------------------------------
        */
        foreach ($request->items as $item) {

            $productId   = $item['product_id'];
            $orderedQty  = (float)$item['ordered_qty'];
            $receiveQty  = (float)$item['received_qty'];
            $price       = (float)$item['price'];

            if ($receiveQty <= 0) {
                continue;
            }

            $product = Product::find($productId);

            if (!$product) {
                throw new Exception('Invalid product selected.');
            }

            if ($receiveQty < 0) {
                throw new Exception('Receive quantity cannot be negative.');
            }

            if ($price <= 0) {
                throw new Exception('Price must be greater than zero.');
            }

            /*
            |-----------------------------------------
            | ALREADY RECEIVED CHECK
            |-----------------------------------------
            */
            $alreadyReceived = PurchaseReceiveItem::whereHas('receive', function ($q) use ($purchase) {
                $q->where('purchase_id', $purchase->id);
            })
            ->where('product_id', $productId)
            ->sum('received_qty');

            $remainingQty = $orderedQty - $alreadyReceived;

            if ($receiveQty > $remainingQty) {
                throw new Exception(
                    "Receive Qty ({$receiveQty}) cannot exceed Pending Qty ({$remainingQty})"
                );
            }

            $totalReceived = $alreadyReceived + $receiveQty;

            $shortQty = max(0, $orderedQty - $totalReceived);

            /*
            |-----------------------------------------
            | STOCK IN
            |-----------------------------------------
            */
            StockIn::create([
                'product_id'   => $productId,
                'qty'          => $receiveQty,
                'type'         => 'purchase',
                'reference_id' => $purchase->id,
                'po_no'        => $purchase->invoice_no,
                'created_by'   => auth()->id(),
            ]);

            /*
            |-----------------------------------------
            | STOCK LEDGER
            |-----------------------------------------
            */
            $lastStock = StockLedger::where('product_id', $productId)
                ->latest('id')
                ->value('balance_after') ?? 0;

            StockLedger::create([
                'product_id'     => $productId,
                'movement_type'  => 'IN',
                'qty'            => $receiveQty,
                'reference_type' => 'PURCHASE_RECEIVE',
                'reference_id'   => $purchase->id,
                'balance_after'  => $lastStock + $receiveQty,
                'created_by'     => auth()->id(),
            ]);

            /*
            |-----------------------------------------
            | INVENTORY LOG
            |-----------------------------------------
            */
            InventoryLog::create([
                'purchase_id' => $purchase->id,
                'product_id'  => $productId,
                'action_type' => 'receive',
                'qty'         => $receiveQty,
                'remarks'     => 'Stock received',
                'created_by'  => auth()->id(),
            ]);

            /*
            |-----------------------------------------
            | PURCHASE RECEIVE ITEM
            |-----------------------------------------
            */
            PurchaseReceiveItem::create([
                'purchase_receive_id' => $receive->id,
                'product_id'          => $productId,
                'ordered_qty'         => $orderedQty,
                'received_qty'        => $receiveQty,
                'short_qty'           => $shortQty,
                'price'               => $price,
            ]);

            /*
            |-----------------------------------------
            | VENDOR LEDGER (CREDIT)
            |-----------------------------------------
            */
            $receiveAmount = $receiveQty * $price;

            $newBalance = $vendor->getOutstandingAmount() + $receiveAmount;

            VendorLedger::create([
                'vendor_id'      => $vendor->id,
                'entry_type'     => 'CREDIT',
                'amount'         => $receiveAmount,
                'reference_type' => 'PURCHASE_RECEIVE',
                'reference_id'   => $purchase->id,
                'balance_after'  => $newBalance,
                'note'           => 'Stock received for PO ' . $purchase->invoice_no,
                'created_by'     => auth()->id(),
            ]);
        }

        /*
        |-----------------------------------------
        | 8. UPDATE PURCHASE STATUS
        |-----------------------------------------
        */
        $totalOrdered = PurchaseItem::where('purchase_id', $purchase->id)->sum('qty');

        $totalReceived = PurchaseReceiveItem::whereHas('receive', function ($q) use ($purchase) {
            $q->where('purchase_id', $purchase->id);
        })->sum('received_qty');

        $purchase->update([
            'status' => ($totalReceived >= $totalOrdered) ? 'received' : 'partial'
        ]);

        DB::commit();

        return redirect()
            ->route('Purchase')
            ->with('success', 'Purchase received successfully.');

    } catch (Exception $e) {

        DB::rollBack();

        return back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}
                    /**
                     * Short Close Remaining Purchase Quantity.
                     */
                    public function shortClose($id)
                {
                    $purchase = Purchase::findOrFail($id);

                    $totalOrdered = PurchaseItem::where(
                        'purchase_id',
                        $purchase->id
                    )->sum('qty');

                    $totalReceived = PurchaseReceiveItem::whereHas(
                        'receive',
                        function ($q) use ($purchase) {
                            $q->where(
                                'purchase_id',
                                $purchase->id
                            );
                        }
                    )->sum('received_qty');

                    if ($totalReceived >= $totalOrdered) {
                        return back()->with(
                            'error',
                            'Fully received PO cannot be short closed.'
                        );
                    }

                    if ($purchase->status == 'short_closed') {
                        return back()->with(
                            'error',
                            'PO is already short closed.'
                        );
                    }

                    DB::beginTransaction();

                    try {

                        $oldStatus = $purchase->status;

                        $items = PurchaseItem::where(
                            'purchase_id',
                            $purchase->id
                        )->get();

                        foreach ($items as $item) {

                            $receivedQty = PurchaseReceiveItem::whereHas(
                                'receive',
                                function ($q) use ($purchase) {
                                    $q->where(
                                        'purchase_id',
                                        $purchase->id
                                    );
                                }
                            )
                            ->where(
                                'product_id',
                                $item->product_id
                            )
                            ->sum('received_qty');

                            $remainingQty =
                                $item->qty - $receivedQty;

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

                        $purchase->update([
                            'status' => 'short_closed'
                        ]);

                        DB::commit();

                        return redirect()
                            ->route('Purchase')
                            ->with(
                                'success',
                                'PO short closed successfully.'
                            );

                    } catch (\Exception $e) {

                        DB::rollBack();

                        return back()->with(
                            'error',
                            $e->getMessage()
                        );
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