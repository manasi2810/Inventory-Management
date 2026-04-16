<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReceiveItem extends Model
{
    protected $fillable = [
        'purchase_receive_id',
        'product_id',
        'ordered_qty',
        'received_qty',
        'short_qty',
        'price'
    ];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function receive()
    {
        return $this->belongsTo(PurchaseReceive::class, 'purchase_receive_id');
    }
}