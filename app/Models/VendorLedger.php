<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\Vendor;

class VendorLedger extends Model
{
    protected $fillable = [
        'vendor_id',
        'transaction_date',
        'voucher_no',
        'entry_type',   // CREDIT / DEBIT
        'amount',
        'reference_type',
        'reference_id',
        'balance_after',
        'note',
        'created_by'
    ];

    /*
    |----------------------------------
    | RELATIONSHIP
    |----------------------------------
    */

    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /*
    |----------------------------------
    | SCOPES (ERP REPORTING)
    |----------------------------------
    */

    public function scopeCredit($query)
    {
        return $query->where('entry_type', 'CREDIT');
    }

    public function scopeDebit($query)
    {
        return $query->where('entry_type', 'DEBIT');
    }

    public function scopeForVendor($query, $vendorId)
    {
        return $query->where('vendor_id', $vendorId);
    }

    /*
    |----------------------------------
    | BOOT (AUTO FIELDS)
    |----------------------------------
    */

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($ledger) {

            // Auto voucher if not set
            if (!$ledger->voucher_no) {
                $ledger->voucher_no =
                    'VL-' . now()->format('YmdHis') . rand(100, 999);
            }

            // Auto date
            if (!$ledger->transaction_date) {
                $ledger->transaction_date = now();
            }

            // Audit
            if (auth()->check()) {
                $ledger->created_by = auth()->id();
            }
        });
    }
}