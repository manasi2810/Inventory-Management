<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_no',
        'dispatch_id',
        'delivery_challan_id',
        'customer_id',
        'invoice_date',
        'sub_total',
        'gst_amount',
        'discount',
        'transport_charge',
        'round_off',
        'total_amount',
        'payment_status',
        'invoice_pdf',
        'remarks',
        'created_by',
        'updated_by',
    ];

    /**
     * Invoice belongs to Dispatch
     */
    public function dispatch()
    {
        return $this->belongsTo(Dispatch::class);
    }

    /**
     * Invoice belongs to Delivery Challan
     */
    public function deliveryChallan()
    {
        return $this->belongsTo(DeliveryChallan::class);
    }

    /**
     * Invoice belongs to Customer
     */
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /**
     * Invoice has many Invoice Items
     */
    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    /**
     * User who created the invoice
     */
    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * User who last updated the invoice
     */
    public function updatedBy()
    {
        return $this->belongsTo(User::class, 'updated_by');
    }
}