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
use App\Services\CustomerLedgerService;
use App\Models\Dispatch;
use App\Models\DispatchItem;
use Exception;
use App\Events\DispatchCompleted;

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
        $this->middleware('permission:delivery.dispatch')->only(['dispatchPage', 'dispatchStore']);
        $this->middleware('permission:delivery.restore')->only(['restore']);
        $this->middleware('permission:delivery.force-delete')->only(['forceDelete']);
        $this->middleware('permission:delivery.trashed')->only(['trashed']);
    }

    // ================= INDEX ===================
    public function index()
        {
        $challans = DeliveryChallan::withTrashed()
            ->with(['customer', 'items', 'approver', 'dispatcher'])
            ->orderBy('id', 'desc')
            ->paginate(25);

        return view('Admin.Delivery_challan.index', compact('challans'));
    }

    // ================= CREATE ==================
    public function create()
        {
        $customers = Customer::all();

        $stockService = app(StockService::class);

        // Bulk load all product IDs first, then fetch stock in one call if StockService supports it,
        // otherwise this is the best we can do without modifying StockService.
        $products = Product::select('id', 'name', 'price')->get()
            ->map(function ($product) use ($stockService) {
                $product->stock = $stockService->getStock($product->id);
                return $product;
            });

        // Only used as a display preview — real number is generated in store()
        $challan_no = $this->previewChallanNo();

        return view('Admin.Delivery_challan.create', compact(
            'customers',
            'products',
            'challan_no'
        ));
    }

    // ================= STORE ===================
    public function store(Request $request)
        {
        DB::beginTransaction();

        try {
            $request->validate([
                'customer_id'  => 'required|exists:customers,id',
                'challan_date' => 'required|date',
                'items'        => 'required|array|min:1',
            ]);

            $stockService = app(StockService::class);

            $subTotal = 0;
            $totalQty = 0;

            // STEP 1: STOCK VALIDATION
            foreach ($request->items as $item) {
                if (empty($item['product_id']) || (int)$item['qty'] <= 0) {
                    continue;
                }

                $qty  = (int)$item['qty'];
                $rate = (float)$item['rate'];

                if (!$stockService->hasStock($item['product_id'], $qty)) {
                    throw new Exception("Insufficient stock for Product ID {$item['product_id']}");
                }

                $subTotal += $qty * $rate;
                $totalQty += $qty;
            }

            if ($totalQty === 0) {
                throw new Exception('No valid items provided.');
            }

            $gstRate   = config('erp.gst_rate', 18);
            $gstAmount = round(($subTotal * $gstRate) / 100, 2);
            $grandTotal = round($subTotal + $gstAmount, 2);

            // STEP 2: GENERATE CHALLAN NO SAFELY INSIDE TRANSACTION
            // lockForUpdate prevents concurrent requests from getting the same number
            $last = DeliveryChallan::lockForUpdate()->latest('id')->first();
            $year       = date('Y');
            $newNumber  = '0001';

            if ($last && preg_match('/(\d{4})$/', $last->challan_no, $m)) {
                $newNumber = str_pad(((int)$m[1] + 1), 4, '0', STR_PAD_LEFT);
            }

            $challan_no = "DC{$year}-{$newNumber}";

            // Guard against duplicates at application level
            if (DeliveryChallan::where('challan_no', $challan_no)->exists()) {
                throw new Exception("Challan number {$challan_no} already exists. Please try again.");
            }

            // STEP 3: CREATE CHALLAN
            $challan = DeliveryChallan::create([
                'challan_no'     => $challan_no,
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
                'created_by'     => auth()->id(),
            ]);

            // STEP 4: SAVE ITEMS (reuse shared method)
            $this->saveItems($challan, $request->items);

            DB::commit();

            return redirect()->route('Delivery_challan')
                ->with('success', 'Delivery Challan created successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ================= EDIT ====================
    public function edit($id)
        {
            $challan = DeliveryChallan::with('items.product')->findOrFail($id);
        
            $stockService = app(StockService::class);
        
            // Build a map of product_id => used qty in this challan
            $usedQtyMap = $challan->items
                ->groupBy('product_id')
                ->map(function ($rows) {
                    return $rows->sum('qty');
                });
        
            $products = Product::all()->map(function ($product) use ($stockService, $usedQtyMap) {
                $available = $stockService->getStock($product->id);
                $usedQty = $usedQtyMap->get($product->id, 0);
                $product->stock = $available + $usedQty;
                return $product;
            });
        
            return view('Admin.Delivery_challan.edit', compact('challan', 'products'));
        }

    // ================= UPDATE ==================
    public function update(Request $request, $id)
        {
        DB::beginTransaction();

        try {
            // Lock the challan row for the duration of this transaction
            $challan = DeliveryChallan::with('items')->lockForUpdate()->findOrFail($id);

            if (in_array($challan->status, ['approved', 'dispatched', 'partially_returned', 'closed'])) {
                throw new Exception('Approved or dispatched Delivery Challan cannot be edited.');
            }

            $request->validate([
                'challan_date' => 'required|date',
                'items'        => 'required|array|min:1',
            ]);

            $stockService = app(StockService::class);

            $subTotal = 0;
            $totalQty = 0;

            // Build old qty map once to avoid repeated collection filters
          $oldQtyMap = $challan->items->groupBy('product_id')->map(function ($rows) {
                return $rows->sum('qty');
            });

            foreach ($request->items as $item) {
                if (empty($item['product_id']) || (int)$item['qty'] <= 0) {
                    continue;
                }

                $productId = $item['product_id'];
                $qty       = (int)$item['qty'];
                $rate      = (float)$item['rate'];

                $oldQty         = $oldQtyMap->get($productId, 0);
                $availableStock = $stockService->getStock($productId) + $oldQty;

                if ($qty > $availableStock) {
                    $product = Product::findOrFail($productId);
                    throw new Exception("Insufficient stock for {$product->name}");
                }

                $subTotal += $qty * $rate;
                $totalQty += $qty;
            }

            if ($totalQty === 0) {
                throw new Exception('No valid items provided.');
            }

            $gstRate   = config('erp.gst_rate', 18);
            $gstAmount = round(($subTotal * $gstRate) / 100, 2);

            $challan->update([
                'challan_date'   => $request->challan_date,
                'transport_mode' => $request->transport_mode,
                'vehicle_no'     => $request->vehicle_no,
                'lr_no'          => $request->lr_no,
                'delivery_to'    => $request->delivery_to,
                'notes'          => $request->notes,
                'total_qty'      => $totalQty,
                'sub_total'      => $subTotal,
                'gst_amount'     => $gstAmount,
                'total_amount'   => round($subTotal + $gstAmount, 2),
            ]);

            // Delete old items and re-save (reuse shared method)
            DeliveryChallanItem::where('delivery_challan_id', $challan->id)->delete();
            $this->saveItems($challan, $request->items);

            DB::commit();

            return redirect()->route('Delivery_challan')
                ->with('success', 'Delivery Challan updated successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ============ APPROVAL =====================
    public function approve($id)
        {
        DB::beginTransaction();

        try {
            $challan = DeliveryChallan::with('items.product')
                ->lockForUpdate()
                ->findOrFail($id);

            if ($challan->status !== 'draft') {
                throw new Exception('Only Draft Delivery Challan can be approved.');
            }

            if ($challan->items->isEmpty()) {
                throw new Exception('Delivery Challan has no items.');
            }

            // Re-validate stock at approval time
            $stockService = app(StockService::class);

            foreach ($challan->items as $item) {
                if (!$stockService->hasStock($item->product_id, $item->qty)) {
                    throw new Exception("Insufficient stock for {$item->product->name} at time of approval.");
                }
            }

            $challan->update([
                'status'      => 'approved',
                'approved_by' => auth()->id(),
                'approved_at' => now(),
            ]);

            DB::commit();

            return back()->with('success', 'Delivery Challan approved successfully.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // ============ SHOW =========================
    public function show($id)
        {
        $challan = DeliveryChallan::with(['items.product', 'customer'])->findOrFail($id);

        foreach ($challan->items as $item) {
            $item->pending_qty = max(0, $item->qty - $item->dispatched_qty);
        }

      $dispatches = Dispatch::with([
        'items.product',
        'invoice'
    ])
    ->where('delivery_challan_id', $id)
    ->select(
        'id',
        'dispatch_no',
        'dispatch_date',
        'status',
        'packing_slip_pdf',
        'delivery_challan_id',
        'customer_id'
    )
    ->latest()
    ->get();

        return view('Admin.Delivery_challan.show', compact('challan', 'dispatches'));
    }

    // ============ DESTROY ======================
    public function destroy($id)
        {
        $challan = DeliveryChallan::findOrFail($id);

        if ($challan->status !== 'draft') {
            return back()->with('error', 'Only Draft Delivery Challan can be deleted.');
        }

        $challan->delete();

        return back()->with('success', 'Delivery Challan moved to trash.');
    }

    // ============ TRASHED ======================
    public function trashed()
        {
        $challans = DeliveryChallan::onlyTrashed()->with('customer')->paginate(25);

        return view('Admin.Delivery_challan.trashed', compact('challans'));
    }

    // ============ RESTORE ======================
    public function restore($id)
        {
        $challan = DeliveryChallan::onlyTrashed()->findOrFail($id);
        $challan->restore();

        return back()->with('success', 'Delivery Challan restored successfully.');
    }

    // ============ FORCE DELETE =================
    public function forceDelete($id)
        {
        DB::beginTransaction();

        try {
            $challan = DeliveryChallan::onlyTrashed()
                ->lockForUpdate()
                ->findOrFail($id);

            if ($challan->status !== 'draft') {
                throw new Exception('Only Draft Delivery Challan can be permanently deleted.');
            }

            // Use withTrashed in case items were soft-deleted before the challan was trashed
            DeliveryChallanItem::withTrashed()
                ->where('delivery_challan_id', $challan->id)
                ->forceDelete();

            $challan->forceDelete();

            DB::commit();

            return back()->with('success', 'Delivery Challan permanently deleted.');

        } catch (Exception $e) {
            DB::rollBack();
            return back()->with('error', $e->getMessage());
        }
    }

    // ============ DISPATCH PAGE ================
    public function dispatchPage($id)
        {
        $challan = DeliveryChallan::with('items.product')->findOrFail($id);

        return view('Admin.Delivery_challan.dispatch', compact('challan'));
    }

    // ============ DISPATCH STORE ===============
   
    public function dispatchStore(Request $request, $id)
        {  
            \Log::info('DISPATCH REQUEST START', [
            'time' => microtime(true),
            'ip' => request()->ip(),
            'url' => request()->fullUrl(),
            ]);
            
            
        DB::beginTransaction(); 
        try {
            $challan = DeliveryChallan::with(['items.product', 'customer'])
                ->lockForUpdate()
                ->findOrFail($id); 
            if (in_array($challan->status, ['dispatched', 'delivered', 'cancelled'])) {
                throw new Exception('This Delivery Challan cannot be dispatched.');
            }

            $stockService = app(StockService::class);
            $gstRate      = config('erp.gst_rate', 18);

            // Generate dispatch_no safely with a lock
            $maxId      = Dispatch::lockForUpdate()->max('id') ?? 0;
            $dispatchNo = 'DSP' . now()->format('Y') . '-' . str_pad($maxId + 1, 4, '0', STR_PAD_LEFT);

            $dispatch                      = new Dispatch();
            $dispatch->dispatch_no         = $dispatchNo;
            $dispatch->delivery_challan_id = $challan->id;
            $dispatch->customer_id         = $challan->customer_id;
            $dispatch->dispatch_date       = now()->format('Y-m-d');
            $dispatch->status              = 'completed';
            $dispatch->remarks             = $request->remarks;
            $dispatch->created_by          = auth()->id();
            $dispatch->save();

            if (!$dispatch->id) {
                throw new Exception('Dispatch creation failed.');
            }

            $hasDispatch      = false;
            $dispatchSubTotal = 0;
            $dispatchGst      = 0;
            $dispatchTotal    = 0;

            foreach ($request->items ?? [] as $data) {
                // Lock the item row to prevent concurrent dispatch of the same item
                $item = DeliveryChallanItem::with('product')
                    ->where('id', $data['item_id'])
                    ->lockForUpdate()
                    ->first();

                if (!$item) continue;

                $dispatchQty = (float)($data['qty'] ?? 0);

                if ($dispatchQty <= 0) continue;

                $hasDispatch = true;

                $pending = $item->qty - $item->dispatched_qty;

                if ($dispatchQty > $pending) {
                    throw new Exception(
                        "Cannot dispatch more than pending quantity for {$item->product->name}"
                    );
                }

                // FIX: check stock immediately before decrement (adjacent, not separated by other logic)
                if (!$stockService->hasStock($item->product_id, $dispatchQty)) {
                    throw new Exception("Insufficient stock for {$item->product->name}");
                }

                // Amount calculation using config GST rate
                $lineSubTotal = round($dispatchQty * $item->rate, 2);
                $lineGst      = round($lineSubTotal * $gstRate / 100, 2);
                $lineTotal    = round($lineSubTotal + $lineGst, 2);

                $dispatchSubTotal += $lineSubTotal;
                $dispatchGst      += $lineGst;
                $dispatchTotal    += $lineTotal;

                // Save dispatch item
                $dispatchItem                           = new DispatchItem();
                $dispatchItem->dispatch_id              = $dispatch->id;
                $dispatchItem->delivery_challan_item_id = $item->id;
                $dispatchItem->product_id               = $item->product_id;
                $dispatchItem->quantity                 = $dispatchQty;
                $dispatchItem->rate                     = $item->rate;
                $dispatchItem->gst_percent              = $gstRate;
                $dispatchItem->gst_amount               = $lineGst;
                $dispatchItem->amount                   = $lineTotal;
                $dispatchItem->save();

                // Decrease stock immediately after the check (atomic as possible within transaction)
                $stockService->decreaseStock(
                    $item->product_id,
                    $dispatchQty,
                    [
                        'type'           => 'dispatch',
                        'reference_type' => 'dispatch',
                        'reference_id'   => $dispatch->id,
                        'reference_no'   => $dispatch->dispatch_no,
                        'reason'         => 'Dispatch ' . $dispatch->dispatch_no,
                        'user_id'        => auth()->id(),
                    ]
                );

                // Update dispatched qty on the challan item
                $item->increment('dispatched_qty', $dispatchQty);
            }

            if (!$hasDispatch) {
                throw new Exception('Please enter dispatch quantity.');
            }

            // Update challan status
            $challan->load('items');

            $totalQty      = $challan->items->sum('qty');
            $dispatchedQty = $challan->items->sum('dispatched_qty');

            $status = 'approved';

            if ($dispatchedQty > 0 && $dispatchedQty < $totalQty) {
                $status = 'partially_dispatched';
            } elseif ($dispatchedQty >= $totalQty) {
                $status = 'dispatched';
            }

            $challan->update([
                'status'        => $status,
                'dispatched_by' => auth()->id(),
                'dispatched_at' => now(),
            ]);

            // Customer ledger entry
            app(CustomerLedgerService::class)->addEntry(
                $dispatch->customer_id,
                'dispatch',
                $dispatch->id,
                $dispatch->dispatch_no,
                'DISPATCH',
                $dispatchTotal,
                0,
                'Material Dispatched',
                $dispatchSubTotal,
                $dispatchGst,
                $dispatchTotal
            );

          DB::commit();

        \Log::info('===== Before Event =====');
         
    event(new DispatchCompleted($dispatch));
        
        \Log::info('===== After Event =====');
        
        return redirect()->route('Delivery_challan')
    ->with('success', 'Material dispatched successfully.');

        } catch (\Throwable $e) {
            DB::rollBack();
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    // ============ PRIVATE HELPERS ==============
    private function previewChallanNo(): string
        {
        $last      = DeliveryChallan::latest('id')->first();
        $year      = date('Y');
        $newNumber = '0001';

        if ($last && preg_match('/(\d{4})$/', $last->challan_no, $m)) {
            $newNumber = str_pad(((int)$m[1] + 1), 4, '0', STR_PAD_LEFT);
        }

        return "DC{$year}-{$newNumber}";
    }

    private function saveItems(DeliveryChallan $challan, array $items): void
        {
        foreach ($items as $item) {
            if (empty($item['product_id']) || (int)$item['qty'] <= 0) {
                continue;
            }

            $qty  = (int)$item['qty'];
            $rate = (float)$item['rate'];

            DeliveryChallanItem::create([
                'delivery_challan_id' => $challan->id,
                'product_id'          => $item['product_id'],
                'qty'                 => $qty,
                'rate'                => $rate,
                'total'               => round($qty * $rate, 2),
            ]);
        }
    }
}