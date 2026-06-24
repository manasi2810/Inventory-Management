<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use App\Models\Purchase;
use App\Models\VendorLedger;

class Vendor extends Model
{
    use SoftDeletes;

    protected $fillable = [
    'name',
    'contact',
    'address',
    'gst_number',
    'pan_number',
    'email',
    'company_name',
    'city',
    'state',
    'status',
    'vendor_code',
    'credit_limit',
    'payment_days',
    'bank_name',
    'bank_account_no',
    'ifsc_code',
    'remarks',
    'opening_balance',
    'opening_balance_type',
];

    /*
    |----------------------------------
    | RELATIONSHIPS
    |----------------------------------
    */

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }

    public function ledgers()
    {
        return $this->hasMany(VendorLedger::class);
    }

    /*
    |----------------------------------
    | SCOPES
    |----------------------------------
    */

    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    public function scopeBlocked($query)
    {
        return $query->where('status', 'blocked');
    }

    /*
    |----------------------------------
    | STATUS HELPERS
    |----------------------------------
    */

    public function isActive()
    {
        return $this->status === 'active';
    }

    public function canTransact()
    {
        return $this->status === 'active';
    }

    public function isBlocked()
    {
        return $this->status === 'blocked';
    }

    /*
    |----------------------------------
    | ERP FINANCIAL LOGIC (CORE)
    |----------------------------------
    */

    // Total CREDIT (Purchase, etc.)
    public function getTotalCredit()
    {
        return $this->ledgers()
            ->where('entry_type', 'CREDIT')
            ->sum('amount');
    }

    // Total DEBIT (Payments, Returns)
    public function getTotalDebit()
    {
        return $this->ledgers()
            ->where('entry_type', 'DEBIT')
            ->sum('amount');
    }

    // Outstanding Amount (ERP STANDARD)
    public function getOutstandingAmountAttribute()
    {
        return ($this->opening_balance ?? 0)
            + $this->getTotalCredit()
            - $this->getTotalDebit();
    }

    // Available Credit
    public function getAvailableCreditAttribute()
    {
        return $this->credit_limit - $this->outstanding_amount;
    }

    // Purchase Summary
    public function getTotalPurchaseAmount()
    {
        return $this->purchases()->sum('total_amount');
    }

    public function getTotalPurchaseCount()
    {
        return $this->purchases()->count();
    }

    /*
    |----------------------------------
    | BOOT (ERP SAFE)
    |----------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($vendor) {

            // Vendor Code Auto Generate (ERP STANDARD)
            if (!$vendor->vendor_code) {

                $last = self::orderBy('id', 'desc')->first();

                $next = $last ? $last->id + 1 : 1;

                $vendor->vendor_code =
                    'V' . str_pad($next, 5, '0', STR_PAD_LEFT);
            }

            // Audit
            if (auth()->check()) {
                $vendor->created_by = auth()->id();
            }
        });

        static::updating(function ($vendor) {

            if (auth()->check()) {
                $vendor->updated_by = auth()->id();
            }
        });
    }


    public function getOutstandingAmount()
    {
        $credit = $this->ledgers()
            ->where('entry_type', 'CREDIT')
            ->sum('amount');

        $debit = $this->ledgers()
            ->where('entry_type', 'DEBIT')
            ->sum('amount');

        return $this->opening_balance + $credit - $debit;
    }
}