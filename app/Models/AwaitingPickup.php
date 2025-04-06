<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AwaitingPickup extends Model
{
    use HasFactory;

    protected $fillable = [
        'receipt_no',
        'stock_id',
        'sale_id',
        'quantity',
        'price',
        'status',
        'note',
        'user_id',
        'delivery_user_id',
        'delivered_at',
    ];

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function sale()
    {
        return $this->belongsTo(Sale::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function deliveryUser()
    {
        return $this->belongsTo(User::class, 'delivery_user_id');
    }
}