<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DcReturn extends Model
{
    protected $fillable = [
        'delivery_challan_id',
        'customer_id',
        'return_no',
        'return_date',
        'status',
        'notes',
        'created_by',
        'approved_by'
    ];

    public function deliveryChallan()
    {
        return $this->belongsTo(DeliveryChallan::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(DcReturnItem::class);
    }

    // ERP SUMMARY HELPERS
    public function getTotalQtyAttribute()
    {
        return $this->items->sum('return_qty');
    }

    public function getTotalValueAttribute()
    {
        return $this->items->sum(function ($item) {
            return $item->return_qty * $item->unit_price;
        });
    }
}