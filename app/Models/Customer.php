<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    /**
     * Mass assignable fields
     */
    protected $fillable = [
        // Basic Info
        'name',
        'company_name',

        // Contact Info
        'mobile',
        'alternate_mobile',
        'email',

        // Address Info
        'billing_address',
        'shipping_address',
        'city',
        'state',
        'pincode',
        'country',
 
        'gst_number',
        'pan_number',
 
        'customer_type',
        'status', 
        'notes'
    ];
 
    protected $casts = [
        'status' => 'boolean',
    ];

    /**
     * Relationships (optional but useful)
     */

    // Customer → Delivery Challans
    public function challans()
    {
        return $this->hasMany(DeliveryChallan::class);
    }

    // Customer → Orders (future use if needed)
    public function orders()
    {
        return $this->hasMany(Order::class);
    }
}