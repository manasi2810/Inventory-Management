<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvoiceItem extends Model
{
    protected $fillable = [
        'invoice_id',
        'dispatch_item_id',
        'product_id',
        'quantity',
        'rate',
        'gst_percent',
        'gst_amount',
        'amount',
    ];

    /**
     * Invoice Item belongs to Invoice
     */
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    /**
     * Invoice Item belongs to Dispatch Item
     */
    public function dispatchItem()
    {
        return $this->belongsTo(DispatchItem::class);
    }

    /**
     * Invoice Item belongs to Product
     */
    public function product()
    {
        return $this->belongsTo(Product::class);
    }
}