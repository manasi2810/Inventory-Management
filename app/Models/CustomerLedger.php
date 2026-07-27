<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CustomerLedger extends Model
{
  protected $fillable = [
    'customer_id',
    'entry_type',
    'debit',
    'credit',

    'sub_total',
    'gst_amount',
    'total_amount',

    'reference_type',
    'reference_id',
    'reference_no',
    'balance_after',
    'remarks',
    'created_by',
];

  protected $casts = [
    'debit' => 'decimal:2',
    'credit' => 'decimal:2',
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