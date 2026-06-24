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

    /* ================= CASTS ================= */

    protected $casts = [
        'qty'   => 'integer',
        'rate'  => 'decimal:2',
        'total' => 'decimal:2',
    ];

    /* ================= RELATIONS ================= */

    public function challan()
    {
        return $this->belongsTo(DeliveryChallan::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /* ================= ERP HELPER ================= */

    public function getLineTotalAttribute()
    {
        return (float) $this->qty * (float) $this->rate;
    }
}