<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OnlineProductImage extends Model
{
    use HasFactory;

    protected $fillable = [
        'online_product_id',
        'image_url',
        'featured',
    ];

    public function onlineStoreProduct()
    {
        return $this->belongsTo(OnlineStoreProduct::class, 'online_product_id');
    }
    
}
