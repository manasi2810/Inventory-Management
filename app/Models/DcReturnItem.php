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

    public function dcReturn()
    {
        return $this->belongsTo(DcReturn::class);
    }

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    /* ======================
       ERP CALCULATIONS
    ====================== */

    public function getTotalValueAttribute()
    {
        return $this->return_qty * $this->unit_price;
    }

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
        return $this->dcReturn->return_date ?? '-';
    }

    public function getProductNameAttribute()
    {
        return $this->product->name ?? '-';
    }
}