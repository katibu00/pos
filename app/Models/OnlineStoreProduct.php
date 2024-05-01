<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineStoreProduct extends Model
{
    use HasFactory;

    protected $fillable = [
        'stock_id', 
        'original_price',
        'selling_price',
        'discount_price',
        'discount_applied',
        'description',
        'category_id',
        'available',
        'featured',
    ];

   
    public function onlineProductImages()
    {
        return $this->hasMany(OnlineProductImage::class, 'online_product_id');
    }
    public function product()
    {
        return $this->belongsTo(Stock::class,'stock_id');
    }
    
}
