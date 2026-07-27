<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DispatchItem extends Model
{
    protected $fillable = [

        'dispatch_id',

        'delivery_challan_item_id',

        'product_id',

        'quantity',

        'rate',

        'gst_percent',

        'gst_amount',

        'amount',
    ];

    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function challanItem()
    {
        return $this->belongsTo(
            DeliveryChallanItem::class,
            'delivery_challan_item_id'
        );
    }
}