<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InventoryLog extends Model
{ 
    protected $fillable = [
        'purchase_id',
        'product_id',
        'action_type',
        'qty',
        'amount',
        'status_from',
        'status_to',
        'remarks',
        'created_by',
    ];
}
 