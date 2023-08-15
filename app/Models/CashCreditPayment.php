<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CashCreditPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'cash_credits_id',
        'customer_id',
        'branch_id',
        'amount_paid',
        'payment_method',
    ];
}
