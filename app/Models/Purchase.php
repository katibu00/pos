<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Purchase extends Model
{
    use HasFactory;

    public function product(){
        return $this->belongsTo(Stock::class, 'stock_id','id');
    }

    protected $fillable = [
        'branch_id',
        'stock_id',
        'buying_price',
        'quantity',
        'old_quantity',
        'old_buying_price',
        'old_selling_price',
        'date',
    ];
}


