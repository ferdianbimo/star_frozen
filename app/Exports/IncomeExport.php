<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;

class IncomeExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $incomeLogs;
    protected $totalValue;
    protected $rowNumber = 0;
    
    public function __construct($incomeLogs, $totalValue)
    {
        $this->incomeLogs = $incomeLogs;
        $this->totalValue = $totalValue;
    }

    public function collection()
    {
        return $this->incomeLogs;
    }

    public function title(): string
    {
        return 'Laporan Pemasukan';
    }

    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Waktu',
            'Produk',
            'Kode Produk',
            'Jumlah',
            'Satuan',
            'Harga Satuan',
            'Total Pemasukan',
            'Kasir',
            'Catatan'
        ];
    }

    public function map($log): array
    {
        $this->rowNumber++;
        
        return [
            $this->rowNumber,
            $log->created_at->format('d/m/Y'),
            $log->created_at->format('H:i'),
            $log->product->name ?? 'N/A',
            $log->product->code ?? '-',
            abs($log->change),
            $log->unit_label ?? 'pcs',
            $log->unit_price,
            $log->total_value,
            $log->user->name ?? 'System',
            $log->note ?? '-'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 12,
            'C' => 8,
            'D' => 25,
            'E' => 12,
            'F' => 10,
            'G' => 10,
            'H' => 15,
            'I' => 18,
            'J' => 15,
            'K' => 20,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row styling
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '10B981'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $this->incomeLogs->count() + 1;
                
                // Style all data cells
                $sheet->getStyle('A1:K' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'E2E8F0'],
                        ],
                    ],
                ]);
                
                // Alternate row colors
                for ($i = 2; $i <= $lastRow; $i++) {
                    if ($i % 2 == 0) {
                        $sheet->getStyle('A' . $i . ':K' . $i)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8FAFC'],
                            ],
                        ]);
                    }
                }
                
                // Format currency columns
                $sheet->getStyle('H2:H' . $lastRow)->getNumberFormat()->setFormatCode('"Rp "#,##0');
                $sheet->getStyle('I2:I' . $lastRow)->getNumberFormat()->setFormatCode('"Rp "#,##0');
                
                // Center align specific columns
                $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B2:C' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F2:G' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Right align currency
                $sheet->getStyle('H2:I' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Add total row
                $totalRow = $lastRow + 1;
                $sheet->setCellValue('A' . $totalRow, 'TOTAL PEMASUKAN');
                $sheet->mergeCells('A' . $totalRow . ':H' . $totalRow);
                $sheet->setCellValue('I' . $totalRow, $this->totalValue);
                
                // Style total row
                $sheet->getStyle('A' . $totalRow . ':K' . $totalRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => '047857'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'D1FAE5'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '10B981'],
                        ],
                    ],
                ]);
                $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('I' . $totalRow)->getNumberFormat()->setFormatCode('"Rp "#,##0');
                $sheet->getStyle('I' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Set row height for header
                $sheet->getRowDimension(1)->setRowHeight(25);
                
                // Freeze header row
                $sheet->freezePane('A2');
            },
        ];
    }
}
