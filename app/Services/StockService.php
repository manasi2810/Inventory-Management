<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockIn;
use App\Models\StockOut;
use App\Models\StockLedger;
use Illuminate\Support\Facades\DB;

class StockService
{
    /**
     * Get live stock of a product
     */
    public function getStock($productId)
    {
        $product = Product::findOrFail($productId);

        return $product->stock_quantity;
    }

    /**
     * Increase stock (Purchase / Return / Stock In)
     */
   public function increaseStock($productId, $qty, $reference = null)
        {
            return DB::transaction(function () use ($productId, $qty, $reference) {

                $product = Product::where('id', $productId)
                    ->lockForUpdate()
                    ->first();

                $product->stock_quantity = ($product->stock_quantity ?? 0) + $qty;
                $product->save();

              
            StockIn::create([
            'product_id' => $productId,
            'qty'        => $qty,
            'reference'  => is_array($reference) ? json_encode($reference) : $reference,
            'created_by' => $reference['user_id'] ?? auth()->id(),
        ]);
                // STOCK LEDGER
                StockLedger::create([
                'product_id' => $productId,
                'movement_type' => 'IN',
                'qty' => $qty,
                'reference_type' => is_array($reference) ? ($reference['type'] ?? null) : null,
                'reference_id' => is_array($reference) ? ($reference['id'] ?? null) : null,
                'balance_after' => $product->stock_quantity,
                'created_by' => is_array($reference) 
                ? ($reference['user_id'] ?? 1) 
                : 1,
            ]);
            });
        }

    /**
     * Decrease stock (Delivery Challan / Sale / Damage)
     */

    
    public function decreaseStock($productId, $qty, $reference = null)
        {
            return DB::transaction(function () use ($productId, $qty, $reference) {

                $product = Product::where('id', $productId)
                    ->lockForUpdate()
                    ->first();

                $currentStock = $product->stock_quantity ?? 0;

                if ($currentStock < $qty) {
                    throw new \Exception("Insufficient stock for product: " . $product->name);
                }

                $product->stock_quantity -= $qty;
                $product->save();

                StockOut::create([
                    'product_id' => $productId,
                    'qty'        => $qty, 
                    'reference_type' => is_array($reference) ? ($reference['type'] ?? null) : null,
                    'reference_id'   => is_array($reference) ? ($reference['id'] ?? null) : null, 
                    'created_by' => is_array($reference)
                        ? ($reference['user_id'] ?? auth()->id())
                        : auth()->id(),
                ]);

                //  STOCK LEDGER
                StockLedger::create([
                    'product_id' => $productId,
                    'movement_type' => 'OUT',
                    'qty' => $qty,
                    'reference_type' => $reference['type'] ?? null,
                    'reference_id' => $reference['id'] ?? null,
                    'balance_after' => $product->stock_quantity,
                    'created_by' => $reference['user_id'] ?? auth()->id() ?? null,
                ]);
            });
        }

    /**
     * Validate stock before issuing (safe check)
     */
    public function updateStatus(Request $request, $id)
{
    DB::beginTransaction();

    try {

        $challan = DeliveryChallan::with('items.product')->findOrFail($id);
        $stockService = new StockService();
        $userId = auth()->id();

        $newStatus = $request->status;
        $oldStatus = $challan->status;

        // 🚚 DISPATCH LOGIC (STOCK OUT)
        if ($newStatus === 'dispatched' && $oldStatus !== 'dispatched') {

            foreach ($challan->items as $item) {

                if (!$stockService->hasStock($item->product_id, $item->qty)) {
                    throw new \Exception("Stock not available for {$item->product->name}");
                }

                $stockService->decreaseStock(
                    $item->product_id,
                    $item->qty,
                    [
                        'type' => 'delivery_challan',
                        'id' => $challan->id,
                        'user_id' => $userId
                    ]
                );
            }
        }

        // 🔄 CANCEL AFTER DISPATCH (STOCK RETURN)
        if ($newStatus === 'cancelled' && $oldStatus === 'dispatched') {

            foreach ($challan->items as $item) {

                $stockService->increaseStock(
                    $item->product_id,
                    $item->qty,
                    [
                        'type' => 'dc_cancel',
                        'id' => $challan->id,
                        'user_id' => $userId
                    ]
                );
            }
        }

        $challan->status = $newStatus;
        $challan->save();

        DB::commit();

        return back()->with('success', 'Status updated successfully');

    } catch (\Exception $e) {

        DB::rollback();

        return back()->with('error', $e->getMessage());
    }
}


    public function hasStock($productId, $qty)
    {
        $product = Product::findOrFail($productId);

return ($product->stock_quantity ?? 0) >= $qty;    }
}