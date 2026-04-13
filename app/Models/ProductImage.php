<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ProductImage extends Model
{
    use HasFactory;

    protected $fillable = ['product_id','type','image_path'];

    public function product() {
        return $this->belongsTo(Product::class);
    }
}