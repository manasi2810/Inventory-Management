<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PurchaseReceive extends Model
{
    protected $fillable = [
        'purchase_id',
        'receive_date',
        'status'
    ];

    public function items()
    {
        return $this->hasMany(PurchaseReceiveItem::class);
    }

    public function purchase()
    {
        return $this->belongsTo(Purchase::class);
    }
}
