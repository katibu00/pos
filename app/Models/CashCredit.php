<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashCredit extends Model
{
    use HasFactory;

    public function customer(){
        return $this->belongsTo(User::class, 'customer_id','id');
    }
    public function cashier(){
        return $this->belongsTo(User::class, 'cashier_id','id');
    }

    protected $fillable = [
        'customer_id',
        'amount',
        'status',
        'amount_paid',
    ];
   
}
