<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseRecord extends Model
{
    use HasFactory;


    protected $fillable = ['expense_account_id', 'user_id', 'description', 'amount'];

    public function expenseAccount()
    {
        return $this->belongsTo(ExpenseAccount::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
