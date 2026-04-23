<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockLedger extends Model
{
    protected $fillable = [
        'product_id',
        'movement_type',
        'qty',
        'reference_type',
        'reference_id',
        'balance_after',
        'created_by'
    ];
}