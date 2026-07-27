<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Dispatch extends Model
{
    protected $fillable = [
        'dispatch_no',
        'delivery_challan_id',
        'customer_id',
        'dispatch_date',
        'status',
        'remarks',
        'packing_slip_pdf',
        'created_by',
    ];

    /**
     * Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Delivery Challan
     */
    public function deliveryChallan()
    {
        return $this->belongsTo(
            DeliveryChallan::class,
            'delivery_challan_id'
        );
    }

    /**
     * Dispatch Items
     */
    public function items()
    {
        return $this->hasMany(DispatchItem::class);
    }

    /**
     * Invoice
     */
    public function invoice()
    {
        return $this->hasOne(Invoice::class);
    }
}