<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Sale extends Model
{
    use HasFactory;

    public function product(){
        return $this->belongsTo(Stock::class, 'stock_id','id');
    }
    public function user(){
        return $this->belongsTo(User::class, 'user_id','id');
    }
    public function customer(){
        return $this->belongsTo(User::class, 'customer_name','id');
    }


    protected $fillable = [
        'branch_id',
        'receipt_no',
        'stock_id',
        'price',
        'quantity',
        'discount',
        'payment_type',
        'payment_amount',
        'user_id',
        'customer',
        'note',
        'returned_qty',
        'collected',
    ];
}
