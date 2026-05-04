<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\DeliveryChallan;
use App\Models\DcReturn;
use App\Models\DcReturnItem;
use App\Services\StockService;
use App\Models\DeliveryChallanItem;
use App\Models\StockLedger; 
use DB;

class DcReturnController extends Controller
{
     
public function create($id)
{
    $dc = DeliveryChallan::with('items.product')->findOrFail($id);

    $dc->all_returned = true;

    foreach ($dc->items as $item) {

        $returnItems = DcReturnItem::where('product_id', $item->product_id)
            ->whereHas('dcReturn', function ($q) use ($id) {
                $q->where('delivery_challan_id', $id);
            })
            ->get();

        $item->delivered_qty = $item->qty;
 
        $item->return_breakdown = $returnItems
            ->groupBy('condition')
            ->map(fn($g) => $g->sum('return_qty'));
 
        $item->returned_qty = $returnItems->sum('return_qty');

        $item->remaining_qty = max(0, $item->qty - $item->returned_qty);
 
        $item->can_return = $item->remaining_qty > 0;

        if ($item->remaining_qty > 0) {
            $dc->all_returned = false;
        }
    }

    return view('admin.delivery_challan.return', compact('dc'));
}
    


public function store(Request $request)
    {
        $request->validate([
            'delivery_challan_id' => 'required|exists:delivery_challans,id',
            'return_qty' => 'required|array',
            'product_id' => 'required|array',
        ]);

        DB::beginTransaction();

        try {

            $stockService = new StockService(); 
            $dc = DeliveryChallan::with('items')->findOrFail($request->delivery_challan_id); 
            $dcReturn = DcReturn::create([
                'delivery_challan_id' => $dc->id,
                'customer_id' => $dc->customer_id,
                'return_no' => 'DCR-' . time(),
                'return_date' => now(),
                'notes' => $request->notes ?? null,
                'created_by' => auth()->id(),
            ]);

            $hasAnyReturn = false; 
            foreach ($request->return_qty as $itemId => $qty) {

                $qty = (float) $qty;

                if ($qty <= 0) continue;

                $hasAnyReturn = true;

                $dcItem = DeliveryChallanItem::findOrFail($itemId);

                $productId = $dcItem->product_id;

                $condition = $request->condition[$itemId] ?? 'good';
 
                $alreadyReturned = DcReturnItem::where('dc_item_id', $itemId)
                    ->sum('return_qty');

                $remaining = $dcItem->qty - $alreadyReturned;

                if ($qty > $remaining) {
                    throw new \Exception("Return qty exceeds remaining for {$dcItem->product->name}");
                }
 
                DcReturnItem::create([
                    'dc_return_id' => $dcReturn->id,
                    'dc_item_id'   => $itemId,
                    'product_id'   => $productId,
                    'return_qty'   => $qty,
                    'condition'    => $condition,
                ]);
 
                if ($condition === 'good') {

                    $stockService->increaseStock($productId, $qty, [
                        'type' => 'dc_return_good',
                        'id' => $dcReturn->id,
                        'user_id' => auth()->id()
                    ]);

                } else {

                    StockLedger::create([
                        'product_id' => $productId,
                        'movement_type' => 'IN',
                        'qty' => $qty,
                        'reference_type' => 'DC_RETURN_' . strtoupper($condition),
                        'reference_id' => $dcReturn->id,
                        'balance_after' => $stockService->getStock($productId),
                        'created_by' => auth()->id(),
                    ]);
                }
            }

            if (!$hasAnyReturn) {
                throw new \Exception("Please enter at least one return quantity.");
            }
 
            $totalQty = 0;
            $returnedQty = 0;

            foreach ($dc->items as $item) {

                $itemReturned = DcReturnItem::where('dc_item_id', $item->id)
                    ->sum('return_qty');

                $totalQty += $item->qty;
                $returnedQty += $itemReturned;
            }

            if ($returnedQty == 0) {
                $dc->status = 'dispatched';
            } elseif ($returnedQty < $totalQty) {
                $dc->status = 'partially_returned';
            } else {
                $dc->status = 'closed';
            }

            $dc->save();

            DB::commit();

            return redirect()
                ->route('Delivery_challan')
                ->with('success', 'DC Return saved successfully');

        } catch (\Exception $e) {

            DB::rollBack();

            return back()
                ->withInput()
                ->with('error', $e->getMessage());
        }
    }
}