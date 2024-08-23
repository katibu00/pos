<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class WarehouseTransaction extends Model
{
    use HasFactory;

    protected $fillable = ['warehouse_id', 'stock_id', 'type', 'quantity', 'source', 'batch_number', 'branch_id'];

    public function warehouse()
    {
        return $this->belongsTo(Warehouse::class);
    }

    public function stock()
    {
        return $this->belongsTo(Stock::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
    
}
