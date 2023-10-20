<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Estimate extends Model
{
    use HasFactory;

    public function product(){
        return $this->belongsTo(Stock::class, 'product_id','id');
    }
    protected $fillable = [
        'branch_id',
        'estimate_no',
        'product_id',
        'price',
        'quantity',
        'discount',
        'cashier_id',
        'customer',
        'note',
    ];

    public function buyer(){
        return $this->belongsTo(User::class, 'customer','id');
    }
}
