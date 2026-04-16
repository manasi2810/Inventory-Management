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

use DB;

class PurchaseController extends Controller
{
    /**
     * SHOW PURCHASE LIST
     */
    public function index()
    {
        $purchases = Purchase::with('vendor')
            ->orderBy('id', 'desc')
            ->get();
        return view('admin.purchase.index', compact('purchases'));
    } 
    /**
     * SHOW CREATE FORM
     */
    public function create()
    {
        $vendors = Vendor::all();
        $products = Product::all();
        return view('admin.purchase.create', compact('vendors', 'products'));
    } 
    /**
     * STORE PURCHASE + ITEMS
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try {
        
            $year = date('Y');
            $lastPurchase = Purchase::whereYear('created_at', $year)
                ->orderBy('id', 'desc')
                ->first();
            if ($lastPurchase && $lastPurchase->invoice_no) {
                $lastNumber = (int) substr($lastPurchase->invoice_no, -4);
                $newNumber = $lastNumber + 1;
            } else {
                $newNumber = 1;
            }
            $poNo = 'PO' . $year . '-' . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
            $purchase = Purchase::create([
                'vendor_id' => $request->vendor_id,
                'invoice_no' => $poNo, // PO NUMBER
                'purchase_date' => $request->purchase_date,
                'total_amount' => 0,
                'status' => 'pending',
            ]);
            $totalAmount = 0;
            if ($request->has('items')) {

                foreach ($request->items as $item) {

                    $qty = $item['qty'] ?? 0;
                    $price = $item['price'] ?? 0;

                    $itemTotal = $qty * $price;
                    $totalAmount += $itemTotal;
                    PurchaseItem::create([
                        'purchase_id' => $purchase->id,
                        'product_id' => $item['product_id'],
                        'qty' => $qty,
                        'price' => $price,
                        'total' => $itemTotal,
                    ]);
                }
            } 
            $purchase->update([
                'total_amount' => $totalAmount
            ]);
            DB::commit();
            return redirect()->route('purchases.index')
                ->with('success', 'Purchase Order created successfully');
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }
    /**
     * SHOW SINGLE PURCHASE
     */
        public function show($id)
        {
            $purchase = Purchase::with('vendor', 'items.product')
                ->findOrFail($id);

            return view('admin.purchase.show', compact('purchase'));
        }

    /**
     * RECEIVE PURCHASE (STOCK IN ENTRY POINT)
     */
        public function receive($id)
        {
            $purchase = Purchase::with('items.product', 'vendor')
                ->findOrFail($id);

            if ($purchase->status == 'received') {
                return back()->with('error', 'Already received');
            }

            foreach ($purchase->items as $item) {

                // FIXED QUERY (through relation)
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

public function storeReceive(Request $request, $id)
{
    DB::beginTransaction();

    try {

        $purchase = Purchase::findOrFail($id);

        $isFullyReceived = true;

        // GRN HEADER
        $receive = PurchaseReceive::create([
            'purchase_id'  => $purchase->id,
            'receive_date' => now(),
            'status'       => 'completed',
        ]);

        foreach ($request->items as $item) {

            $productId   = $item['product_id'];
            $ordered     = (int) $item['ordered_qty'];
            $receiveNow  = (int) $item['received_qty'];

            if ($receiveNow <= 0) {
                continue; // skip empty rows
            }

            // total already received from DB
            $alreadyReceived = PurchaseReceiveItem::whereHas('receive', function ($q) use ($id) {
                    $q->where('purchase_id', $id);
                })
                ->where('product_id', $productId)
                ->sum('received_qty');

            // remaining before this entry
            $remaining = $ordered - $alreadyReceived;

            // safety check
            if ($receiveNow > $remaining) {
                throw new \Exception("Cannot receive more than remaining quantity for product ID: $productId");
            }

            // final total received
            $totalReceived = $alreadyReceived + $receiveNow;

            // FINAL SHORT QTY (THIS IS WHAT YOU WANT TO STORE)
            $shortQty = $ordered - $totalReceived;

            if ($shortQty < 0) {
                $shortQty = 0;
            }

            // check full receive status
            if ($shortQty > 0) {
                $isFullyReceived = false;
            }

            // SAVE RECEIVE ITEM (ONLY IMPORTANT DATA)
            PurchaseReceiveItem::create([
                'purchase_receive_id' => $receive->id,
                'product_id'          => $productId,
                'ordered_qty'         => $ordered,
                'received_qty'        => $receiveNow,
                'short_qty'           => $shortQty,
                'price'               => $item['price'] ?? 0,
            ]);

            // STOCK UPDATE
            Product::where('id', $productId)
                ->increment('opening_stock', $receiveNow);

            // STOCK LEDGER
            StockIn::create([
                'product_id'   => $productId,
                'qty'          => $receiveNow,
                'type'         => 'purchase',
                'reference_id' => $receive->id,
                'created_by'   => auth()->id(),
            ]);
        }

        // UPDATE PURCHASE STATUS
        $purchase->update([
            'status' => $isFullyReceived ? 'received' : 'partial'
        ]);

        DB::commit();

        return redirect()->route('Purchase')
            ->with('success', 'Purchase received successfully');

    } catch (\Exception $e) {

        DB::rollback();

        return back()->with('error', $e->getMessage());
    }
}


}