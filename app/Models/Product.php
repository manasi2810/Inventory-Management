<?php

namespace App\Models;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name','category_id','sku','description','opening_stock','pack_size',
        'moq','uom','price','cost_price','feature_product','sequence','status',
        'page_title','alt_text','meta_keywords'
    ];

    public function stockIns()
{
    return $this->hasMany(\App\Models\StockIn::class);
}

public function stockOuts()
{
    return $this->hasMany(\App\Models\StockOut::class);
}
public function stockLedgers()
{
    return $this->hasMany(\App\Models\StockLedger::class);
}
 
}