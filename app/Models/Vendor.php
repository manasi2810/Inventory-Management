<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    protected $fillable = [
        'name',
        'contact',
        'address',
        'gst_number',
        'email',
        'company_name',
        'city',
        'state'
    ];

    // RELATIONSHIP
    public function purchases()
    {
        return $this->hasMany(Purchase::class);
    }
}