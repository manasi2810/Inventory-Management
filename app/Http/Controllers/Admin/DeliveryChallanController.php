<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryChallan;
use App\Models\Product;
use App\Models\Customer;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Support\Facades\DB;
use App\Models\DeliveryChallanItem;
use App\Models\InventoryLog;
use App\Services\StockService;
use App\Models\User;

class DeliveryChallanController extends Controller
{
     
    public function index()
        {
            $challans = DeliveryChallan::withTrashed()   
                ->with(['customer', 'items', 'approver', 'dispatcher'])
                ->orderBy('id', 'desc')
                ->get();  
            return view('admin.Delivery_challan.index', compact('challans'));
        }
     
    public function create()
        {
            $customers = Customer::all(); 
            $products = Product::all()->map(function ($product) { 
                $openingStock = $product->opening_stock ?? 0; 
                $poReceived = StockIn::where('product_id', $product->id)
                                ->sum('qty');

                $stockOut = StockOut::where('product_id', $product->id)
                                ->sum('qty');

                $product->stock = $openingStock - $stockOut; 
                return $product;
            }); 

            $lastChallan = DeliveryChallan::orderBy('id', 'desc')->first(); 
            $year = date('Y'); 
            if ($lastChallan) {

                preg_match('/(\d{4})$/', $lastChallan->challan_no, $matches);

                $lastNumber = isset($matches[1]) ? (int) $matches[1] : 0;
                $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT);

            } else {
                $newNumber = '0001';
            } 
            $challan_no = 'DC' . $year . '-' . $newNumber; 
            return view('admin.Delivery_challan.create', compact(
                'customers',
                'products',
                'challan_no'
            ));
        }

    
   public function store(Request $request)
{
    DB::beginTransaction();

    try {

        $request->validate([
            'challan_no'   => 'required',
            'customer_id'  => 'required',
            'challan_date' => 'required|date',
            'items'        => 'required|array|min:1',
        ]);

        $userId = auth()->id();

        $subTotal = 0;
        $totalQty = 0;

        foreach ($request->items as $item) {

            $qty  = (int) ($item['qty'] ?? 0);
            $rate = (float) ($item['rate'] ?? 0);

            $subTotal += $qty * $rate;
            $totalQty += $qty;
        }

        $gstAmount  = $subTotal * 0.18;
        $grandTotal = $subTotal + $gstAmount;

        $challan = DeliveryChallan::create([
            'challan_no'     => $request->challan_no,
            'customer_id'    => $request->customer_id,
            'challan_date'   => $request->challan_date,
            'status'         => 'draft',

            // SAFE INPUT MAPPING (IMPORTANT FIX)
            'transport_mode' => $request->input('transport_mode'),
            'vehicle_no'     => $request->input('vehicle_no'),
            'lr_no'          => $request->input('lr_no'),
            'dispatch_from'  => $request->input('dispatch_from', 'Main Warehouse'),
            'delivery_to'    => $request->input('delivery_to'),
            'notes'          => $request->input('notes'),

            'total_qty'      => $totalQty,
            'sub_total'      => $subTotal,
            'gst_amount'     => $gstAmount,
            'total_amount'   => $grandTotal,

            'created_by'     => $userId,
        ]);

        foreach ($request->items as $item) {

            if (!isset($item['product_id'])) continue;

            $product = Product::find($item['product_id']);

            if (!$product) continue;

            DeliveryChallanItem::create([
                'delivery_challan_id' => $challan->id,
                'product_id'          => $product->id,
                'qty'                 => (int) $item['qty'],
                'rate'                => (float) $item['rate'],
                'total'               => (int)$item['qty'] * (float)$item['rate'],
            ]);
        }

        DB::commit();

        return redirect()
            ->route('Delivery_challan')
            ->with('success', 'Delivery Challan created successfully');

    } catch (\Exception $e) {

        DB::rollBack();

        return back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}

           

        public function dispatch($id)
            {
                // dd('DISPATCH HIT');
                DB::beginTransaction();

                try {

                    $challan = DeliveryChallan::with('items')->findOrFail($id);

                    if ($challan->status === 'dispatched') {
                        throw new \Exception('Already dispatched');
                    } 
                    if ($challan->status !== 'approved') {
                        throw new \Exception('Only approved challan can be dispatched');
                    }  
                    $stockService = new StockService(); 
                    foreach ($challan->items as $item) { 
                       $stockService->decreaseStock(
                        $item->product_id,
                        $item->qty,
                        'OUT',
                        [
                            'type' => 'delivery_challan',
                            'id' => $challan->id,
                            'user_id' => auth()->id()
                        ]
                    );
                    }  
                    $challan->update([
                        'status' => 'dispatched',
                        'dispatched_by' => auth()->id(),
                        'dispatched_at' => now(),
                    ]); 
                    DB::commit(); 
                    return back()->with('success', 'Dispatched successfully'); 
                } catch (\Exception $e) {
                    DB::rollBack();
                    return back()->with('error', $e->getMessage());
                }
            }



        public function approve($id)
            {
                // dd('APPROVE HIT');
                $challan = DeliveryChallan::findOrFail($id);
                
                if ($challan->status != 'draft') {
                    return back()->with('error', 'Only draft can be approved');
                } 
                $challan->update([
                    'status' => 'approved',
                    'approved_by' => auth()->id(),  
                ]); 
                return back()->with('success', 'Approved successfully');
            }



        public function show($id)
            {
                $challan = DeliveryChallan::with(['items.product', 'customer'])
                    ->findOrFail($id); 
                return view('admin.Delivery_challan.show', compact('challan'));
            }
 


        public function edit($id)
            {
                $challan = DeliveryChallan::with('items.product')->findOrFail($id); 
                $products = Product::all()->map(function ($product) { 
                    $product->stock = $product->stock_quantity;   
                    return $product;
                }); 
                return view('admin.Delivery_challan.edit', compact(
                    'challan',
                    'products'
                ));
            }



        public function print($id)
            {
                $challan = DeliveryChallan::with(['items.product', 'customer'])
                    ->findOrFail($id); 
                return view('Admin.Delivery_challan.print', compact('challan'));
            }



        public function bulkPrint(Request $request)
            {
                try {

                    if (!$request->ids) {
                        return back()->with('error', 'No challans selected');
                    } 
                    $ids = explode(',', $request->ids);
                    $challans = DeliveryChallan::with(['customer', 'items.product'])
                        ->whereIn('id', $ids)
                        ->get();
                    return view('Admin.Delivery_challan.bulk_print', compact('challans'));
                } catch (\Exception $e) {
                    return back()->with('error', $e->getMessage());
                }
            }
  


        public function update(Request $request, $id)
                {
                    DB::beginTransaction();

                    try {

                        $challan = DeliveryChallan::with('items')->findOrFail($id);

                        if ($challan->status == 'dispatched') {
                            throw new \Exception("Cannot edit dispatched challan");
                        } 
                        $userId = auth()->id(); 
                        $subTotal = 0;
                        $totalQty = 0; 
                        $challan->update([
                            'challan_date'   => $request->challan_date,
                            'status'         => $request->status,
                            'transport_mode' => $request->transport_mode,
                            'vehicle_no'     => $request->vehicle_no,
                            'lr_no'          => $request->lr_no,
                            'delivery_to'    => $request->delivery_to,
                        ]);
                
                        DeliveryChallanItem::where('delivery_challan_id', $challan->id)->delete();
    
                        foreach ($request->items as $item) {

                            $product = Product::findOrFail($item['product_id']);
                            $qty = (int) $item['qty'];
                            $rate = (float) $item['rate'];

                            $subTotal += $qty * $rate;
                            $totalQty += $qty;

                            DeliveryChallanItem::create([
                                'delivery_challan_id' => $challan->id,
                                'product_id'          => $product->id,
                                'qty'                 => $qty,
                                'rate'                => $rate,
                                'total'               => $qty * $rate,
                            ]);
                        }
    
                        $gstAmount = ($subTotal * 18) / 100;
                        $grandTotal = $subTotal + $gstAmount;

                        $challan->update([
                            'total_qty'    => $totalQty,
                            'sub_total'    => $subTotal,
                            'gst_amount'   => $gstAmount,
                            'total_amount' => $grandTotal,
                        ]);

                        DB::commit();

                        return redirect()
                            ->route('Delivery_challan')
                            ->with('success', 'Challan updated successfully');

                    } catch (\Exception $e) {

                        DB::rollBack();

                        return back()->with('error', $e->getMessage());
                    }
                } 

     

        public function destroy(string $id)
                {
                    DB::beginTransaction();

                    try {

                        $challan = DeliveryChallan::with('items')->findOrFail($id);

                        if ($challan->status !== 'draft') {
                            return back()->with('error', 'Only draft challan can be deleted');
                        }
                
                        $challan->delete();

                        DB::commit();

                        return back()->with('success', 'Challan moved to trash successfully');

                    } catch (\Exception $e) {

                        DB::rollBack();

                        return back()->with('error', $e->getMessage());
                    }
                }
    


        public function trashed()
            {
                $challans = DeliveryChallan::onlyTrashed()->with('customer')->get();

                return view('admin.Delivery_challan.trashed', compact('challans'));
            }



        public function restore($id)
            {
                if (auth()->user()->role !== 'admin') {
                    return back()->with('error', 'Only admin can restore');
                }

                $challan = DeliveryChallan::onlyTrashed()->findOrFail($id);

                $challan->restore();

                return back()->with('success', 'Challan restored successfully');
            }



        public function forceDelete($id)
            {
                if (auth()->user()->role !== 'admin') {
                    return back()->with('error', 'Only admin can permanently delete');
                }

                $challan = DeliveryChallan::onlyTrashed()->findOrFail($id);

                DeliveryChallanItem::where('delivery_challan_id', $challan->id)->delete();

                $challan->forceDelete();

                return back()->with('success', 'Permanently deleted');
            }


    }
