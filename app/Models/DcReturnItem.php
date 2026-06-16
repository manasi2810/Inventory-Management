<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DcReturnItem extends Model
{
    protected $fillable = [
        'dc_return_id',
        'dc_item_id',
        'product_id',
        'batch_no',
        'return_qty',
        'accepted_qty',
        'damaged_qty',
        'scrap_qty',
        'unit_price',
        'condition',
        'reason'
    ];

    /* ================= CASTS ================= */

    protected $casts = [
        'return_qty'    => 'decimal:2',
        'accepted_qty'  => 'decimal:2',
        'damaged_qty'   => 'decimal:2',
        'scrap_qty'     => 'decimal:2',
        'unit_price'    => 'decimal:2',
    ];

    /* ================= RELATIONS ================= */

    public function dcReturn()
    {
        return $this->belongsTo(DcReturn::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /* ================= ERP CALCULATIONS ================= */

    public function getTotalValueAttribute()
    {
        return (float) $this->return_qty * (float) $this->unit_price;
    }

    /* ================= SAFE ACCESSORS ================= */

    public function getCustomerNameAttribute()
    {
        return $this->dcReturn->customer->name ?? '-';
    }

    public function getChallanNoAttribute()
    {
        return $this->dcReturn->deliveryChallan->challan_no ?? '-';
    }

    public function getReturnDateAttribute()
    {
        return optional($this->dcReturn)->return_date ?? '-';
    }

    public function getProductNameAttribute()
    {
        return $this->product->name ?? '-';
    }

    /* ================= OPTIONAL ERP HELPERS ================= */

    public function isGoodCondition()
    {
        return $this->condition === 'good';
    }

    public function isDamaged()
    {
        return $this->condition === 'damaged';
    }

    public function isScrap()
    {
        return $this->condition === 'scrap';
    }
}