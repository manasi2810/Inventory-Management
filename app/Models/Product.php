<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\DispatchItem;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'category_id',
        'sku',
        'description',
        'pack_size',
        'moq',
        'uom',
        'price',
        'cost_price',
        'stock_quantity',
        'feature_product',
        'sequence',
        'status',
    ];


    public function stockIns()
    {
        return $this->hasMany(StockIn::class);
    }


    public function stockOuts()
    {
        return $this->hasMany(StockOut::class);
    }


    public function stockLedgers()
    {
        return $this->hasMany(StockLedger::class);
    }


    public function dispatchItems()
    {
        return $this->hasMany(DispatchItem::class);
    }


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class, 'category_id');
    }
}