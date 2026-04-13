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

    public function images() {
        return $this->hasMany(ProductImage::class);
    }

    public function mainImage() {
        return $this->hasOne(ProductImage::class)->where('type','main');
    }

    public function category() {
        return $this->belongsTo(Category::class);
    }
}