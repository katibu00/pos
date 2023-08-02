<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Returns extends Model
{
    use HasFactory;
    
    public function product(){
        return $this->belongsTo(Stock::class, 'product_id','id');
    }

    public function customer(){
        return $this->belongsTo(User::class, 'customer','id');
    }

    protected $fillable = [
        'return_no',
        'branch_id',
        'cashier_id',
        'product_id',
        'price',
        'discount',
        'quantity',
        'customer',
        'note',
    ];
}
