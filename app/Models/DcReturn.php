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

    /* ================= CASTS ================= */

    protected $casts = [
        'return_date' => 'date',
    ];

    /* ================= RELATIONS ================= */

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

    /* ================= ERP HELPERS ================= */

    public function getTotalQtyAttribute()
    {
        return $this->items ? $this->items->sum('return_qty') : 0;
    }

    public function getTotalValueAttribute()
    {
        return $this->items
            ? $this->items->sum(function ($item) {
                return $item->return_qty * ($item->unit_price ?? 0);
            })
            : 0;
    }

    /* ================= STATUS HELPERS (OPTIONAL BUT USEFUL) ================= */

    public function isClosed()
    {
        return $this->status === 'closed';
    }

    public function isPartiallyReturned()
    {
        return $this->status === 'partially_returned';
    }
}