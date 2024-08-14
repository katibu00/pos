<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BranchRestock extends Model
{
    use HasFactory;

    protected $fillable = ['restock_id', 'branch_id', 'percentage'];

    public function restock()
    {
        return $this->belongsTo(Restock::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }
}