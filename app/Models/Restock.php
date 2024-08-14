<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Restock extends Model
{
    use HasFactory;

    protected $fillable = [
        'restock_number', 'type', 'supplier_id', 'status', 'total_cost'
    ];

    public function supplier()
    {
        return $this->belongsTo(User::class);
    }

    public function items()
    {
        return $this->hasMany(RestockItem::class);
    }

    public function expenses()
    {
        return $this->hasMany(RestockExpense::class);
    }

    public function damages()
    {
        return $this->hasMany(RestockDamage::class);
    }

    public function branchRestocks()
    {
        return $this->hasMany(BranchRestock::class);
    }
}