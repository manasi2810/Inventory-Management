<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use SoftDeletes;

    protected $fillable = [

        /* ERP IDENTITY */
        'customer_code',

        /* BASIC */
        'name',
        'company_name',
        'customer_type',

        /* CONTACT */
        'mobile',
        'alternate_mobile',
        'email',

        /* ADDRESS */
        'billing_address',
        'shipping_address',
        'city',
        'state',
        'pincode',
        'country',

        /* TAX */
        'gst_number',
        'pan_number',

        /* ERP FINANCE */
        'credit_limit',
        'opening_balance',
        'status',

        /* AUDIT */
        'created_by',
        'updated_by',

        /* EXTRA */
        'notes'
    ];

    protected $casts = [
        'status' => 'boolean',
        'credit_limit' => 'decimal:2',
        'opening_balance' => 'decimal:2',
    ];

    /* ================= RELATIONSHIPS ================= */

    public function challans()
    {
        return $this->hasMany(DeliveryChallan::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function ledgers()
    {
        return $this->hasMany(CustomerLedger::class);
    }

    /* ================= ERP METHODS ================= */

    public function isActive()
    {
        return $this->status == 1;
    }

    public function getTotalSales()
    {
        return $this->ledgers()
            ->where('entry_type', 'DEBIT')
            ->sum('amount');
    }

    public function getTotalReceived()
    {
        return $this->ledgers()
            ->where('entry_type', 'CREDIT')
            ->sum('amount');
    }

    public function getOutstanding()
    {
        return (float) $this->opening_balance
            + $this->getTotalSales()
            - $this->getTotalReceived();
    }

    public function balance()
    {
        return $this->getOutstanding();
    }

    /* ================= BOOT ================= */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($customer) {

            if (!$customer->customer_code) {
                $customer->customer_code = 'CUST-' . now()->format('YmdHis');
            }

            if (is_null($customer->opening_balance)) {
                $customer->opening_balance = 0;
            }

            if (is_null($customer->status)) {
                $customer->status = 1;
            }
        });
    }
}