<?php

namespace App\Services;

use App\Models\Product;
use App\Models\StockLedger;
use App\Models\StockIn;
use App\Models\StockOut;
use Illuminate\Support\Facades\DB;

class StockService
{
    // =====================
    // GET STOCK (SAFE)
    // =====================
    public function getStock($productId)
    {
        $product = Product::find($productId);
        return $product ? $product->stock_quantity : 0;
    }

    // =====================
    // CHECK STOCK
    // =====================
    public function hasStock($productId, $qty)
    {
        $product = Product::find($productId);
        return $product && ($product->stock_quantity ?? 0) >= $qty;
    }

    // =====================
    // INCREASE STOCK
    // =====================
    public function increaseStock($productId, $qty, $reference = [])
    {
        return DB::transaction(function () use ($productId, $qty, $reference) {

            $product = Product::where('id', $productId)
                ->lockForUpdate()
                ->first();

            if (!$product) {
                throw new \Exception("Product not found: {$productId}");
            }

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
                'reference_type' => $reference['type'] ?? 'IN',
                'reference_id' => $reference['id'] ?? null,
                'balance_after' => $product->stock_quantity,
                'created_by' => auth()->id(),
            ]);
        });
    }

    // =====================
    // DECREASE STOCK
    // =====================
    public function decreaseStock($productId, $qty, $reference = [])
    {
        return DB::transaction(function () use ($productId, $qty, $reference) {

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
                'reference_type' => $reference['type'] ?? 'OUT',
                'reference_id' => $reference['id'] ?? null,
                'created_by' => $reference['user_id'] ?? auth()->id(),
            ]);

            StockLedger::create([
                'product_id' => $productId,
                'movement_type' => 'OUT',
                'qty' => $qty,
                'reference_type' => $reference['type'] ?? 'OUT',
                'reference_id' => $reference['id'] ?? null,
                'balance_after' => $product->stock_quantity,
                'created_by' => $reference['user_id'] ?? auth()->id(),
            ]);
        });
    }

    // =====================
    // RETURN STOCK (FIXED ERP LOGIC)
    // =====================
    public function returnStock($productId, $qty, $condition, $reference = [])
    {
        return DB::transaction(function () use ($productId, $qty, $condition, $reference) {

            $product = Product::where('id', $productId)
                ->lockForUpdate()
                ->first();

            if (!$product) {
                throw new \Exception("Product not found: {$productId}");
            }

            $type = match ($condition) {
                'good' => 'RETURN_GOOD',
                'damaged' => 'RETURN_DAMAGED',
                'scrap' => 'RETURN_SCRAP',
                default => 'RETURN_UNKNOWN',
            };

            // ONLY GOOD STOCK increases inventory
            if ($condition === 'good') {
                $product->stock_quantity += $qty;
                $product->save();
            }

            // damaged/scrap → NO stock increase (ERP correct)

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