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

class ExpenseExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents
{
    protected $expenses;
    protected $totalExpenses;
    protected $rowNumber = 0;
    
    public function __construct($expenses, $totalExpenses)
    {
        $this->expenses = $expenses;
        $this->totalExpenses = $totalExpenses;
    }

    public function collection()
    {
        return $this->expenses;
    }

    public function title(): string
    {
        return 'Laporan Pengeluaran';
    }

    public function headings(): array
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

    public function map($expense): array
    {
        $this->rowNumber++;
        
        return [
            $this->rowNumber,
            \Carbon\Carbon::parse($expense->expense_date)->format('d/m/Y'),
            $expense->category,
            $expense->description,
            $expense->amount,
            $expense->user->name ?? 'N/A'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 6,
            'B' => 12,
            'C' => 20,
            'D' => 45,
            'E' => 18,
            'F' => 18,
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
                    'startColor' => ['rgb' => 'EF4444'],
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
                $lastRow = $this->expenses->count() + 1;
                
                // Style all data cells
                $sheet->getStyle('A1:F' . $lastRow)->applyFromArray([
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
                        $sheet->getStyle('A' . $i . ':F' . $i)->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'startColor' => ['rgb' => 'F8FAFC'],
                            ],
                        ]);
                    }
                }
                
                // Format currency column
                $sheet->getStyle('E2:E' . $lastRow)->getNumberFormat()->setFormatCode('"Rp "#,##0');
                
                // Center align specific columns
                $sheet->getStyle('A2:A' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('B2:B' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Right align currency
                $sheet->getStyle('E2:E' . $lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Wrap text for description
                $sheet->getStyle('D2:D' . $lastRow)->getAlignment()->setWrapText(true);
                
                // Add total row
                $totalRow = $lastRow + 1;
                $sheet->setCellValue('A' . $totalRow, 'TOTAL PENGELUARAN');
                $sheet->mergeCells('A' . $totalRow . ':D' . $totalRow);
                $sheet->setCellValue('E' . $totalRow, $this->totalExpenses);
                
                // Style total row
                $sheet->getStyle('A' . $totalRow . ':F' . $totalRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => 'B91C1C'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'FEE2E2'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => 'EF4444'],
                        ],
                    ],
                ]);
                $sheet->getStyle('A' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('E' . $totalRow)->getNumberFormat()->setFormatCode('"Rp "#,##0');
                $sheet->getStyle('E' . $totalRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Set row height for header
                $sheet->getRowDimension(1)->setRowHeight(25);
                
                // Freeze header row
                $sheet->freezePane('A2');
            },
        ];
    }
}
