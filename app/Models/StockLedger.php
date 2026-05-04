<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLedger extends Model
{
    protected $fillable = [
        'product_id',
        'movement_type',
        'qty',
        'reference_type',
        'reference_id',
        'balance_after',
        'created_by'
    ];

    public function product()
    {
        return $this->belongsTo(\App\Models\Product::class);
    }

    // Optional scopes
    public function scopeIn($query)
    {
        return $query->where('movement_type', 'IN');
    }

    public function scopeOut($query)
    {
        return $query->where('movement_type', 'OUT');
    }
}