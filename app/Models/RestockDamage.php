<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RestockDamage extends Model
{
    use HasFactory;

    protected $fillable = ['restock_id', 'stock_id', 'quantity', 'damage_level', 'notes'];

    public function restock()
    {
        return $this->belongsTo(Restock::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }
}