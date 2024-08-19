<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StockTransferItem extends Model
{
    protected $fillable = [
        'stock_transfer_id',
        'from_stock_id',
        'to_stock_id',
        'quantity',
    ];

    public function stockTransfer()
    {
        return $this->belongsTo(StockTransfer::class);
    }

    public function fromStock()
    {
        return $this->belongsTo(Stock::class, 'from_stock_id');
    }

    public function toStock()
    {
        return $this->belongsTo(Stock::class, 'to_stock_id');
    }
}