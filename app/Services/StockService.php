<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockLedger;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Support\Facades\DB;

class StockService
{
     
    public function getStock($productId)
        {
            return Product::findOrFail($productId)->stock_quantity;
        }

     
    public function hasStock($productId, $qty)
        {
            $product = Product::findOrFail($productId);
            return ($product->stock_quantity ?? 0) >= $qty;
        }
 


    public function increaseStock($productId, $qty, $type = 'IN', $reference = [])
        {
            return DB::transaction(function () use ($productId, $qty, $type, $reference) {

                $product = Product::where('id', $productId)
                    ->lockForUpdate()
                    ->first();

                $product->stock_quantity += $qty;
                $product->save();

                StockIn::create([
                    'product_id' => $productId,
                    'qty' => $qty,
                    'reference' => json_encode($reference),
                    'created_by' => auth()->id(),
                ]);

                StockLedger::create([
                    'product_id' => $productId,
                    'movement_type' => 'IN',
                    'qty' => $qty,
                    'reference_type' => $type,
                    'reference_id' => $reference['id'] ?? null,
                    'balance_after' => $product->stock_quantity,
                    'created_by' => auth()->id(),
                ]);
            });
        }
    


   public function decreaseStock($productId, $qty, $type = 'OUT', $reference = [])
{
    return DB::transaction(function () use ($productId, $qty, $type, $reference) {

        $product = Product::where('id', $productId)
            ->lockForUpdate()
            ->first();

        if (!$product) {
            throw new \Exception("Product not found: {$productId}");
        }

        if (($product->stock_quantity ?? 0) < $qty) {
            throw new \Exception("Insufficient stock for product: {$product->name}");
        }

        $product->stock_quantity -= $qty;
        $product->save();

        StockOut::create([
            'product_id' => $productId,
            'qty' => $qty,
            'reference_type' => $type,
            'reference_id' => $reference['id'] ?? null,
            'created_by' => $reference['user_id'] ?? auth()->id(),
        ]);

        StockLedger::create([
            'product_id' => $productId,
            'movement_type' => 'OUT',
            'qty' => $qty,
            'reference_type' => $type,
            'reference_id' => $reference['id'] ?? null,
            'balance_after' => $product->stock_quantity,
            'created_by' => $reference['user_id'] ?? auth()->id(),
        ]);
    });
}
 


    public function returnStock($productId, $qty, $condition, $reference = [])
        {
            return DB::transaction(function () use ($productId, $qty, $condition, $reference) {

                $product = Product::where('id', $productId)
                    ->lockForUpdate()
                    ->first();

                if ($condition === 'good') {

                    $product->stock_quantity += $qty;
                    $type = 'RETURN_GOOD';

                } elseif ($condition === 'damaged') {

                    $type = 'RETURN_DAMAGED';

                } else {

                    $type = 'RETURN_SCRAP';
                }

                $product->save();

                StockLedger::create([
                    'product_id' => $productId,
                    'movement_type' => 'IN',
                    'qty' => $qty,
                    'reference_type' => $type,
                    'reference_id' => $reference['id'] ?? null,
                    'balance_after' => $product->stock_quantity,
                    'created_by' => auth()->id(),
                ]);
            });
        }
}