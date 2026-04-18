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

class DeliveryChallanController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
{
    $challans = DeliveryChallan::with('customer', 'items')
                    ->latest()
                    ->get();

    return view('admin.Delivery_challan.index', compact('challans'));
}
    /**
     * Show the form for creating a new resource.
     */
  
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

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
{
    DB::beginTransaction();

    try {

        $userId = auth()->id(); 

        $subTotal = 0;
        $totalQty = 0;

        
        foreach ($request->items as $item) {

            $qty = (int) $item['qty'];
            $rate = (float) $item['rate'];

            $subTotal += $qty * $rate;
            $totalQty += $qty;
        }

        
        $gstRate = 18;
        $gstAmount = ($subTotal * $gstRate) / 100;
        $grandTotal = $subTotal + $gstAmount;

                $challan = DeliveryChallan::create([
                'challan_no'     => $request->challan_no,
                'customer_id'    => $request->customer_id,
                'challan_date'   => $request->challan_date,
                'status'         => $request->status,
                'transport_mode' => $request->transport_mode,
                'vehicle_no'     => $request->vehicle_no,
                'lr_no'          => $request->lr_no,
                'dispatch_from'  => $request->dispatch_from,
                'delivery_to'    => $request->delivery_to,
                'notes'          => $request->notes,
 
                'total_qty'      => $totalQty,
                'sub_total'      => $subTotal,
                'gst_amount'     => $gstAmount,
                'total_amount'   => $grandTotal,

                'created_by'     => auth()->id(),
            ]);

        
        foreach ($request->items as $item) {

            $product = Product::findOrFail($item['product_id']);
            $qty = (int) $item['qty'];
            $rate = (float) $item['rate'];

           
            $availableStock =
                ($product->opening_stock ?? 0)
                + StockIn::where('product_id', $product->id)->sum('qty')
                - StockOut::where('product_id', $product->id)->sum('qty');

            if ($qty > $availableStock) {
                throw new \Exception("Stock not available for {$product->name}");
            }

           
            DeliveryChallanItem::create([
                'delivery_challan_id' => $challan->id,
                'product_id'          => $product->id,
                'qty'                 => $qty,
                'rate'                => $rate,
                'total'               => $qty * $rate,
            ]);

            
            StockOut::create([
                'product_id' => $product->id,
                'qty'        => $qty,
                'reference'  => 'DC-' . $challan->challan_no,
                'created_by' => $userId,
            ]);

            
            InventoryLog::create([
                'delivery_challan_id' => $challan->id,
                'product_id'          => $product->id,
                'action_type'         => 'delivery_challan',
                'status_from'         => 'available',
                'status_to'           => 'issued',
                'qty'                 => $qty,
                'remarks'             => 'Stock issued via Delivery Challan',
                'created_by'          => $userId,
            ]);
        }

        DB::commit();

        return redirect()
            ->route('Delivery_challan')
            ->with('success', 'Delivery Challan created successfully');

    } catch (\Exception $e) {

        DB::rollback();

        return back()
            ->withInput()
            ->with('error', $e->getMessage());
    }
}

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        //
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

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        //
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
}
