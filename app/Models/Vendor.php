<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'name',
        'email',
        'phone',
        'address',
        'gst_number',
        'company_name',
        'status'
    ];

    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}