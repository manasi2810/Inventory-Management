<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class DeliveryChallan extends Model
{
    use SoftDeletes;

    protected $fillable = [

        'challan_no',
        'customer_id',
        'challan_date',

        'status',

        'transport_mode',
        'vehicle_no',
        'lr_no',
        'dispatch_from',
        'delivery_to',
        'notes',

        'total_qty',
        'sub_total',
        'gst_amount',
        'total_amount',

        'created_by',

        // tracking fields
        'approved_by',
        'dispatched_by',
        'dispatched_at',
    ];
 

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(DeliveryChallanItem::class);
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function dispatcher()
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }
}