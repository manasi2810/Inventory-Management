<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerLedger extends Model
{
    protected $fillable = [
        'customer_id',
        'entry_type',       // DEBIT / CREDIT
        'amount',
        'reference_type',   // DC / INVOICE / PAYMENT / OPENING
        'reference_id',
        'balance_after',
        'remarks',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'balance_after' => 'decimal:2',
    ];

    /* ================= RELATION ================= */

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    /* ================= HELPERS ================= */

    public function isDebit()
    {
        return $this->entry_type === 'DEBIT';
    }

    public function isCredit()
    {
        return $this->entry_type === 'CREDIT';
    }
}