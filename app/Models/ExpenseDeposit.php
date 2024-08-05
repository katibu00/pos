<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ExpenseDeposit extends Model
{
    use HasFactory;

    protected $fillable = ['expense_account_id', 'admin_id', 'amount'];

    public function expenseAccount()
    {
        return $this->belongsTo(ExpenseAccount::class);
    }

    public function admin()
    {
        return $this->belongsTo(User::class, 'admin_id');
    }
}