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
   
}
