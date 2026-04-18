<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryChallan extends Model
{ 
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
    'gst_amount',
    'sub_total',
    'gst_amount',
    'total_amount',
    'created_by',  
]; 

    // Customer relation
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // Items relation
    public function items()
    {
        return $this->hasMany(DeliveryChallanItem::class);
    }
}