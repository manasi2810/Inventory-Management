<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

use App\Models\DeliveryChallan;
use App\Models\DeliveryChallanItem;
use App\Models\Product;
use App\Models\Customer;

use App\Services\StockService;

class DeliveryChallanController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:delivery.view')->only(['index', 'show']);
        $this->middleware('permission:delivery.create')->only(['create', 'store']);
        $this->middleware('permission:delivery.edit')->only(['edit', 'update']);
        $this->middleware('permission:delivery.delete')->only(['destroy']);
        $this->middleware('permission:delivery.print')->only(['print']);
        $this->middleware('permission:delivery.bulk-print')->only(['bulkPrint']);
        $this->middleware('permission:delivery.approve')->only(['approve']);
        $this->middleware('permission:delivery.dispatch')->only(['dispatch']);
        $this->middleware('permission:delivery.restore')->only(['restore']);
        $this->middleware('permission:delivery.force-delete')->only(['forceDelete']);
        $this->middleware('permission:delivery.trashed')->only(['trashed']);
    }

    // ================= INDEX =================
    public function index()
    {
        $challans = DeliveryChallan::withTrashed()
            ->with(['customer', 'items', 'approver', 'dispatcher'])
            ->latest()
            ->get();

        return view('Admin.Delivery_challan.index', compact('challans'));
    }

    // ================= CREATE =================
    public function create()
    {
        $customers = Customer::all();

        $stockService = app(StockService::class);

        $products = Product::select('id', 'name', 'price')
    ->get()
            ->map(function ($product) use ($stockService) {
                $product->stock = $stockService->getStock($product->id);
                return $product;
            });

        $last = DeliveryChallan::latest('id')->first();

        $year = date('Y');
        $newNumber = '0001';

        if ($last && preg_match('/(\d{4})$/', $last->challan_no, $m)) {
            $newNumber = str_pad(((int)$m[1] + 1), 4, '0', STR_PAD_LEFT);
        }

        $challan_no = "DC{$year}-{$newNumber}";

        return view('admin.Delivery_challan.create', compact(
            'customers',
            'products',
            'challan_no'
        ));
    }

    // ================= STORE =================
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

            $stockService = app(StockService::class);

            $userId = auth()->id();
            $subTotal = 0;
            $totalQty = 0;

            foreach ($request->items as $item) {

                if (empty($item['product_id']) || (int)$item['qty'] <= 0) {
                    continue;
                }

                $qty = (int)$item['qty'];
                $rate = (float)$item['rate'];

                $available = $stockService->getStock($item['product_id']);

                if ($available < $qty) {
                    throw new \Exception("Insufficient stock for Product ID {$item['product_id']}");
                }

                $subTotal += $qty * $rate;
                $totalQty += $qty;
            }

            $gstRate = config('erp.gst_rate', 18);
            $gstAmount = ($subTotal * $gstRate) / 100;
            $grandTotal = $subTotal + $gstAmount;

            $challan = DeliveryChallan::create([
                'challan_no'     => $request->challan_no,
                'customer_id'    => $request->customer_id,
                'challan_date'   => $request->challan_date,
                'status'         => 'draft',

                'transport_mode' => $request->transport_mode,
                'vehicle_no'     => $request->vehicle_no,
                'lr_no'          => $request->lr_no,
                'dispatch_from'  => $request->dispatch_from ?? 'Main Warehouse',
                'delivery_to'    => $request->delivery_to,
                'notes'          => $request->notes,

                'total_qty'      => $totalQty,
                'sub_total'      => $subTotal,
                'gst_amount'     => $gstAmount,
                'total_amount'   => $grandTotal,

                'created_by'     => $userId,
            ]);

            foreach ($request->items as $item) {

                if (empty($item['product_id']) || (int)$item['qty'] <= 0) {
                    continue;
                }

                DeliveryChallanItem::create([
                    'delivery_challan_id' => $challan->id,
                    'product_id'          => $item['product_id'],
                    'qty'                 => (int)$item['qty'],
                    'rate'                => (float)$item['rate'],
                    'total'               => (int)$item['qty'] * (float)$item['rate'],
                ]);
            }

            DB::commit();

            return redirect()->route('Delivery_challan')
                ->with('success', 'Delivery Challan created successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->withInput()
                ->with('error', $e->getMessage());
        }
    }

    // ================= EDIT =================
    public function edit($id)
    {
        $challan = DeliveryChallan::with('items.product')->findOrFail($id);

        $stockService = app(StockService::class);

        $products = Product::all()->map(function ($product) use ($stockService, $challan) {

            $available = $stockService->getStock($product->id);

            $usedQty = $challan->items
                ->where('product_id', $product->id)
                ->sum('qty');

            $product->stock = $available + $usedQty;

            return $product;
        });

        return view('Admin.Delivery_challan.edit', compact('challan', 'products'));
    }

    // ================= UPDATE =================
    public function update(Request $request, $id)
    {
        DB::beginTransaction();

        try {
            $challan = DeliveryChallan::with('items')->findOrFail($id);

            if ($challan->status === 'dispatched') {
                throw new \Exception("Cannot edit dispatched challan");
            }

            $subTotal = 0;
            $totalQty = 0;

            foreach ($request->items as $item) {

                $qty = (int)$item['qty'];
                $rate = (float)$item['rate'];

                $subTotal += $qty * $rate;
                $totalQty += $qty;
            }

            $challan->update([
                'challan_date'   => $request->challan_date,
                'status'         => $request->status,
                'transport_mode' => $request->transport_mode,
                'vehicle_no'     => $request->vehicle_no,
                'lr_no'          => $request->lr_no,
                'delivery_to'    => $request->delivery_to,
                'total_qty'      => $totalQty,
                'sub_total'      => $subTotal,
                'gst_amount'     => ($subTotal * 18) / 100,
                'total_amount'   => $subTotal + ($subTotal * 18) / 100,
            ]);

            DeliveryChallanItem::where('delivery_challan_id', $challan->id)->delete();

            foreach ($request->items as $item) {

                DeliveryChallanItem::create([
                    'delivery_challan_id' => $challan->id,
                    'product_id'          => $item['product_id'],
                    'qty'                 => (int)$item['qty'],
                    'rate'                => (float)$item['rate'],
                    'total'               => (int)$item['qty'] * (float)$item['rate'],
                ]);
            }

            DB::commit();

            return redirect()->route('Delivery_challan')
                ->with('success', 'Updated successfully');

        } catch (\Exception $e) {
            DB::rollBack();

            return back()->with('error', $e->getMessage());
        }
    }

    // ================= DISPATCH =================
    public function dispatch($id)
    {
        DB::beginTransaction();

        try {
            $challan = DeliveryChallan::with('items')->findOrFail($id);

            if ($challan->status === 'dispatched') {
                throw new \Exception("Already dispatched");
            }

            if ($challan->status !== 'approved') {
                throw new \Exception("Only approved challan can be dispatched");
            }

            $stockService = app(StockService::class);

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

    // ================= REST OF FUNCTIONS =================
    public function approve($id)
    {
        $challan = DeliveryChallan::findOrFail($id);

        if ($challan->status !== 'draft') {
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
        $challan = DeliveryChallan::with(['items.product', 'customer'])->findOrFail($id);
        return view('Admin.Delivery_challan.show', compact('challan'));
    }

    public function destroy($id)
    {
        $challan = DeliveryChallan::findOrFail($id);

        if ($challan->status !== 'draft') {
            return back()->with('error', 'Only draft can delete');
        }

        $challan->delete();

        return back()->with('success', 'Moved to trash');
    }

    public function trashed()
    {
        $challans = DeliveryChallan::onlyTrashed()->with('customer')->get();
        return view('Admin.Delivery_challan.trashed', compact('challans'));
    }

    public function restore($id)
    {
        $challan = DeliveryChallan::onlyTrashed()->findOrFail($id);
        $challan->restore();

        return back()->with('success', 'Restored');
    }

    public function forceDelete($id)
    {
        $challan = DeliveryChallan::onlyTrashed()->findOrFail($id);

        DeliveryChallanItem::where('delivery_challan_id', $challan->id)->delete();

        $challan->forceDelete();

        return back()->with('success', 'Permanently deleted');
    }
}