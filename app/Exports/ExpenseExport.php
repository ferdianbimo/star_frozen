<?php

namespace App\Exports;

use Illuminate\Support\Collection;

class ExpenseExport
{
    protected $expenses;
    protected $totalExpenses;
    
    public function __construct($expenses, $totalExpenses)
    {
        $this->expenses = $expenses;
        $this->totalExpenses = $totalExpenses;
    }

    public function collection()
    {
        return $this->expenses;
    }

    public function headings()
    {
        return [
            'No',
            'Tanggal',
            'Kategori',
            'Deskripsi',
            'Jumlah',
            'Dicatat Oleh'
        ];
    }

    public function map($expense)
    {
        static $no = 0;
        $no++;
        
        return [
            $no,
            \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y'),
            $expense->category,
            $expense->description,
            'Rp ' . number_format($expense->amount, 0, ',', '.'),
            $expense->user->name ?? 'N/A'
        ];
    }
}
