<?php

namespace App\Http\Controllers; 
use Illuminate\Http\Request;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Vendor;
use App\Models\Product;
use DB;

class PurchaseController extends Controller
{
    /**
     * LIST PURCHASES
     */
    public function index()
    {
        $purchases = Purchase::with('vendor')
            ->orderBy('id', 'desc')
            ->get();

        return view('purchases.index', compact('purchases'));
    } 
    /**
     * CREATE FORM
     */
    public function create()
    {
        $vendors = Vendor::all();
        $products = Product::all();  
        return view('purchases.create', compact('vendors', 'products'));
    } 
    /**
     * STORE PURCHASE
     */
    public function store(Request $request)
    {
        DB::beginTransaction();
        try { 
            $purchase = Purchase::create([
                'vendor_id' => $request->vendor_id,
                'invoice_no' => $request->invoice_no,
                'purchase_date' => $request->purchase_date,
                'total_amount' => 0,
                'status' => 'pending',
            ]);
            $total = 0; 
            foreach ($request->items as $item) {

                $itemTotal = $item['qty'] * $item['price'];
                $total += $itemTotal;

                PurchaseItem::create([
                    'purchase_id' => $purchase->id,
                    'product_id' => $item['product_id'],
                    'qty' => $item['qty'],
                    'price' => $item['price'],
                    'total' => $itemTotal,
                ]);
            }  
            $purchase->update([
                'total_amount' => $total
            ]); 
            DB::commit(); 
            return redirect()->route('purchases.index')
                ->with('success', 'Purchase created successfully'); 
        } catch (\Exception $e) {
            DB::rollback();
            return back()->with('error', $e->getMessage());
        }
    }

    /**
     * SHOW PURCHASE
     */
    public function show($id)
    {
        $purchase = Purchase::with('items.product', 'vendor')
            ->findOrFail($id); 
        return view('purchases.show', compact('purchase'));
    }

    /**
     * RECEIVE PURCHASE (THIS WILL CREATE STOCK IN LATER)
     */
    public function receive($id)
    {
        $purchase = Purchase::findOrFail($id);  
        if ($purchase->status == 'received') {
            return back()->with('error', 'Already received');
        }   
        $purchase->update([
            'status' => 'received'
        ]); 
        return back()->with('success', 'Purchase received successfully');
    }
}