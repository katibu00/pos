<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockExpense extends Model
{
    use HasFactory;

    protected $fillable = ['restock_id', 'expense_type', 'amount'];

    public function restock()
    {
        return $this->belongsTo(Restock::class);
    }
}