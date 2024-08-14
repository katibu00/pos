<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockItem extends Model
{
    use HasFactory;

    public $timestamps = false; 

    protected $fillable = [
        'restock_id', 
        'stock_id', 
        'ordered_quantity', 
        'received_quantity', 
        'old_quantity', 
        'old_buying_price', 
        'new_buying_price', 
        'old_selling_price', 
        'new_selling_price', 
        'price_changed',
    ];

    public function restock()
    {
        return $this->belongsTo(Restock::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}