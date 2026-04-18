<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryChallanItem extends Model
{
    protected $fillable = [
        'delivery_challan_id',
        'product_id',
        'qty',
        'rate',
        'total'
    ];

    // Challan relation
    public function challan()
    {
        return $this->belongsTo(DeliveryChallan::class);
    }

    // Product relation
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}