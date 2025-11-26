<?php

namespace App\Exports;

use Illuminate\Support\Collection;

class IncomeExport
{
    protected $incomeLogs;
    protected $totalValue;
    
    public function __construct($incomeLogs, $totalValue)
    {
        $this->incomeLogs = $incomeLogs;
        $this->totalValue = $totalValue;
    }

    public function collection()
    {
        return $this->incomeLogs;
    }

    public function headings()
    {
        return [
            'No',
            'Tanggal',
            'Produk',
            'Jumlah Terjual',
            'Harga Satuan',
            'Total Pemasukan',
            'User/Kasir',
            'Catatan'
        ];
    }

    public function map($log)
    {
        static $no = 0;
        $no++;
        
        return [
            $no,
            $log->created_at->format('d/m/Y H:i'),
            $log->product->name ?? 'N/A',
            abs($log->change) . ' pack',
            'Rp ' . number_format($log->unit_price, 0, ',', '.'),
            'Rp ' . number_format($log->total_value, 0, ',', '.'),
            $log->user->name ?? 'System',
            $log->note ?? '-'
        ];
    }
}
