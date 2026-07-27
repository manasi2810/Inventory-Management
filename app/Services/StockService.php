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
    
        if (!$product) {
            return 0;
        }
    
        return $product->stock_quantity;
    }

    // =====================
    // CHECK STOCK
    // =====================
    public function hasStock($productId, $qty)
    {
        return Product::where('id', $productId)
            ->where('stock_quantity', '>=', $qty)
            ->exists();
    }

    // =====================
    // INCREASE STOCK (STOCK IN)
    // =====================
   public function increaseStock($productId, $qty, $reference = [])
    {
    return DB::transaction(function () use ($productId, $qty, $reference) {

        $product = Product::where('id', $productId)
            ->lockForUpdate()
            ->first();

        if (!$product) {
            throw new \Exception("Product not found.");
        }

        $product->stock_quantity += $qty;
        $product->save();

        StockIn::create([
            'product_id' => $productId,
            'qty' => $qty,
            'reference' => !empty($reference)
                ? json_encode($reference)
                : null,
            'created_by' => $reference['user_id'] ?? auth()->id(),
        ]);

        StockLedger::create([
            'product_id' => $productId,
            'movement_type' => 'IN',
            'qty' => $qty,

            'reference_type' =>
                $reference['reference_type']
                ?? $reference['type']
                ?? 'MANUAL_IN',

            'reference_id' =>
                $reference['reference_id']
                ?? $reference['id']
                ?? null,

            'balance_after' => $product->stock_quantity,

            'created_by' =>
                $reference['user_id']
                ?? auth()->id(),
        ]);
    });
}
    // =====================
    // DECREASE STOCK (STOCK OUT)
    // =====================
  public function decreaseStock($productId, $qty, $reference = [])
{
    return DB::transaction(function () use ($productId, $qty, $reference) {

        $product = Product::where('id', $productId)
            ->lockForUpdate()
            ->first();

        if (!$product) {
            throw new \Exception("Product not found.");
        }

        if ($product->stock_quantity < $qty) {
            throw new \Exception("Insufficient stock for {$product->name}");
        }

        /*
        |--------------------------------------------------------------------------
        | Update Product Stock
        |--------------------------------------------------------------------------
        */

        $product->stock_quantity -= $qty;
        $product->save();

        /*
        |--------------------------------------------------------------------------
        | Stock Out Entry
        |--------------------------------------------------------------------------
        */

        StockOut::create([

            'product_id' => $productId,

            'qty' => $qty,

            'type' => strtolower(
                $reference['reference_type']
                ?? $reference['type']
                ?? 'manual'
            ),

            'reference_id' => $reference['reference_id']
                ?? $reference['id']
                ?? null,

            'reference_no' => $reference['reference_no']
                ?? null,

            'reason' => $reference['reason']
                ?? 'Stock Out',

            'created_by' => $reference['user_id']
                ?? auth()->id(),
        ]);

        /*
        |--------------------------------------------------------------------------
        | Stock Ledger Entry
        |--------------------------------------------------------------------------
        */

        StockLedger::create([

            'product_id' => $productId,

            'movement_type' => 'OUT',

            'qty' => $qty,

            'reference_type' => $reference['reference_type']
                ?? $reference['type']
                ?? 'MANUAL_OUT',

            'reference_id' => $reference['reference_id']
                ?? $reference['id']
                ?? null,

            'balance_after' => $product->stock_quantity,

            'created_by' => $reference['user_id']
                ?? auth()->id(),
        ]);

        return true;
    });
}

    // =====================
    // RETURN STOCK (ERP SAFE)
    // =====================
  public function returnStock($productId, $qty, $condition, $reference = [])
{
    return DB::transaction(function () use ($productId, $qty, $condition, $reference) {

        $product = Product::where('id', $productId)
            ->lockForUpdate()
            ->first();

        if (!$product) {
            throw new \Exception("Product not found.");
        }

        switch ($condition) {

            case 'good':
                $referenceType = 'RETURN_GOOD';
                break;

            case 'damaged':
                $referenceType = 'RETURN_DAMAGED';
                break;

            case 'scrap':
                $referenceType = 'RETURN_SCRAP';
                break;

            default:
                $referenceType = 'RETURN_UNKNOWN';
        }

        if ($condition == 'good') {

            $product->stock_quantity += $qty;
            $product->save();

            StockIn::create([

                'product_id' => $productId,

                'qty' => $qty,

                'reference' => !empty($reference)
                    ? json_encode($reference)
                    : null,

                'created_by' =>
                    $reference['user_id']
                    ?? auth()->id(),
            ]);
        }

        StockLedger::create([

            'product_id' => $productId,

            'movement_type' =>
            ($condition == 'good')
                ? 'IN'
                : 'RETURN',

            'qty' => $qty,

            'reference_type' => $referenceType,

            'reference_id' =>
                $reference['reference_id']
                ?? $reference['id']
                ?? null,

            'balance_after' => $product->stock_quantity,

            'created_by' =>
                $reference['user_id']
                ?? auth()->id(),
        ]);
    });
}
}