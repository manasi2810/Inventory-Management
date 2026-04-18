<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockIn extends Model
{
    protected $fillable = [
    'product_id',
    'purchase_id',
    'po_no',
    'qty',
    'type',
    'reference_id',
    'created_by',
];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}