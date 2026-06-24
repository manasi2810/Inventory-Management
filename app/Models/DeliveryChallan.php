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

        'approved_by',
        'dispatched_by',
        'dispatched_at',
    ];

    /* ================= CASTS ================= */

    protected $casts = [
        'challan_date'   => 'date',
        'total_qty'      => 'integer',
        'sub_total'      => 'decimal:2',
        'gst_amount'     => 'decimal:2',
        'total_amount'   => 'decimal:2',
        'dispatched_at'  => 'datetime',
    ];

    /* ================= RELATIONS ================= */

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function items()
    {
        return $this->hasMany(DeliveryChallanItem::class, 'delivery_challan_id', 'id');
    }

    public function approver()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function dispatcher()
    {
        return $this->belongsTo(User::class, 'dispatched_by');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /* ================= ERP HELPERS ================= */

    public function isDraft()
    {
        return $this->status === 'draft';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isDispatched()
    {
        return $this->status === 'dispatched';
    }

    public function isClosed()
    {
        return $this->status === 'closed';
    }
}