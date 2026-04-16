<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    protected $fillable = [
        'vendor_id',
        'invoice_no',
        'purchase_date',
        'total_amount',
        'status',
    ];

    /**
     * Purchase belongs to Vendor
     */
    public function vendor()
    {
        return $this->belongsTo(Vendor::class);
    }

    /**
     * Purchase has many items
     */
    public function items()
    {
        return $this->hasMany(PurchaseItem::class);
    }
}