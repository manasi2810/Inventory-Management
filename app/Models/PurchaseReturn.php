<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
class PurchaseReturn extends Model
{
    protected $fillable = [
        'purchase_id',
        'vendor_id',
        'return_date',
        'total_amount',
        'note'
    ];

    public function items()
    {
        return $this->hasMany(PurchaseReturnItem::class);
    }
}
