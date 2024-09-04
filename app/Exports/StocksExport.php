<?php

namespace App\Exports;

use App\Models\Stock;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class StocksExport implements FromQuery, WithHeadings, WithMapping
{
    protected $branchId;
    protected $columns;

    public function __construct($branchId, $columns)
    {
        $this->branchId = $branchId;
        $this->columns = $columns;
    }

    public function query()
    {
        return Stock::query()->where('branch_id', $this->branchId);
    }

    public function headings(): array
    {
        return $this->columns;
    }

    public function map($stock): array
    {
        $row = [];
        foreach ($this->columns as $column) {
            $row[] = $stock->{$column};
        }
        return $row;
    }
}
