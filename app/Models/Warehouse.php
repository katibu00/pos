<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    public function warehouseItems()
    {
        return $this->hasMany(WarehouseItem::class);
    }

    public function warehouseTransactions()
    {
        return $this->hasMany(WarehouseTransaction::class);
    }
    
}
