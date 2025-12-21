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
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Carbon\Carbon;
use App\Models\Expense;

/**
 * ExpenseExport - Export data pengeluaran ke Excel dengan format profesional.
 *
 * Class ini mengimplementasikan Maatwebsite Excel untuk export
 * data pengeluaran dengan format laporan yang rapi dan profesional.
 *
 * @package    App\Exports
 * @author     Star Frozen Team
 * @version    2.0.0
 */
class ExpenseExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents, ShouldAutoSize
{
    /**
     * Collection data pengeluaran yang akan di-export.
     *
     * @var Collection
     */
    protected $expenses;

    /**
     * Total pengeluaran untuk ditampilkan di summary row.
     *
     * @var float
     */
    protected $totalExpenses;

    /**
     * Periode awal laporan.
     *
     * @var string|null
     */
    protected $startDate;

    /**
     * Periode akhir laporan.
     *
     * @var string|null
     */
    protected $endDate;

    /**
     * Kategori filter yang dipilih.
     *
     * @var string|null
     */
    protected $category;

    /**
     * Counter nomor baris untuk penomoran otomatis.
     *
     * @var int
     */
    protected $rowNumber = 0;

    /**
     * Membuat instance ExpenseExport baru.
     *
     * @param  Collection  $expenses      Collection data Expense
     * @param  float       $totalExpenses Total pengeluaran
     * @param  string|null $startDate     Tanggal awal periode (opsional)
     * @param  string|null $endDate       Tanggal akhir periode (opsional)
     * @param  string|null $category      Filter kategori (opsional)
     * @return void
     */
    public function __construct($expenses, $totalExpenses, $startDate = null, $endDate = null, $category = null)
    {
        $this->expenses = $expenses;
        $this->totalExpenses = $totalExpenses;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->category = $category;
    }

    /**
     * Mendapatkan collection data untuk export.
     *
     * @return Collection
     */
    public function collection()
    {
        return $this->expenses;
    }

    /**
     * Mendapatkan judul sheet Excel.
     *
     * @return string
     */
    public function title(): string
    {
        return 'Laporan Pengeluaran';
    }

    /**
     * Mendapatkan header kolom Excel.
     *
     * @return array<string>
     */
    public function headings(): array
    {
        return [
            'No',
            'Tanggal',
            'Kategori',
            'Deskripsi',
            'Jumlah (Rp)',
            'Dicatat Oleh'
        ];
    }

    /**
     * Mapping setiap row expense ke format array.
     *
     * @param  mixed $expense Data expense yang akan di-map
     * @return array<mixed>
     */
    public function map($expense): array
    {
        $this->rowNumber++;
        
        return [
            $this->rowNumber,
            Carbon::parse($expense->expense_date)->format('d/m/Y'),
            $expense->category ?? '-',
            $expense->description ?? '-',
            $expense->amount,
            $expense->user->name ?? 'N/A'
        ];
    }

    /**
     * Mendapatkan lebar kolom dalam karakter.
     *
     * @return array<string, int>
     */
    public function columnWidths(): array
    {
        return [
            'A' => 8,    // No
            'B' => 14,   // Tanggal
            'C' => 22,   // Kategori
            'D' => 40,   // Deskripsi
            'E' => 18,   // Jumlah
            'F' => 18,   // Dicatat Oleh
        ];
    }

    /**
     * Mendapatkan styles untuk worksheet.
     *
     * @param  Worksheet $sheet Sheet yang akan di-style
     * @return array<int|string, array>
     */
    public function styles(Worksheet $sheet): array
    {
        return [
            // Header row styling - merah untuk pengeluaran
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 12,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'DC2626'],
                ],
                'alignment' => [
                    'horizontal' => Alignment::HORIZONTAL_CENTER,
                    'vertical' => Alignment::VERTICAL_CENTER,
                ],
            ],
        ];
    }

    /**
     * Register event listeners untuk Excel sheet.
     *
     * @return array<class-string, callable>
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $this->expenses->count() + 1;
                
                // Set row height for header
                $sheet->getRowDimension(1)->setRowHeight(28);
                
                // Style all data cells - border
                $sheet->getStyle('A1:F' . $lastRow)->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => 'D1D5DB'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                
                // Alternate row colors (zebra striping)
                for ($i = 2; $i <= $lastRow; $i++) {
                    $bgColor = ($i % 2 == 0) ? 'FEF2F2' : 'FFFFFF';
                    $sheet->getStyle('A' . $i . ':F' . $i)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $bgColor],
                        ],
                    ]);
                    $sheet->getRowDimension($i)->setRowHeight(22);
                }
                
                // Format currency column
                $sheet->getStyle('E2:E' . $lastRow)->getNumberFormat()
                    ->setFormatCode('#,##0');
                
                // Center align kolom No dan Tanggal
                $sheet->getStyle('A2:B' . $lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Right align kolom Jumlah
                $sheet->getStyle('E2:E' . $lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Wrap text untuk Deskripsi
                $sheet->getStyle('D2:D' . $lastRow)->getAlignment()
                    ->setWrapText(true);
                
                // Add total row
                $totalRow = $lastRow + 1;
                $sheet->setCellValue('A' . $totalRow, '');
                $sheet->setCellValue('D' . $totalRow, 'TOTAL PENGELUARAN:');
                $sheet->setCellValue('E' . $totalRow, $this->totalExpenses);
                
                // Style total row
                $sheet->getStyle('A' . $totalRow . ':F' . $totalRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'B91C1C'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '991B1B'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getStyle('D' . $totalRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('E' . $totalRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('E' . $totalRow)->getNumberFormat()
                    ->setFormatCode('"Rp "#,##0');
                $sheet->getRowDimension($totalRow)->setRowHeight(30);
                
                // Add info row
                $infoRow = $totalRow + 2;
                $periodText = $this->getPeriodText();
                $sheet->setCellValue('A' . $infoRow, 'LAPORAN PENGELUARAN - STAR FROZEN');
                $sheet->mergeCells('A' . $infoRow . ':F' . $infoRow);
                $sheet->getStyle('A' . $infoRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => 'DC2626'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                
                $infoRow2 = $infoRow + 1;
                $sheet->setCellValue('A' . $infoRow2, $periodText . ' | Dicetak: ' . Carbon::now()->format('d/m/Y H:i'));
                $sheet->mergeCells('A' . $infoRow2 . ':F' . $infoRow2);
                $sheet->getStyle('A' . $infoRow2)->applyFromArray([
                    'font' => [
                        'italic' => true,
                        'size' => 10,
                        'color' => ['rgb' => '6B7280'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                
                // Freeze header row
                $sheet->freezePane('A2');
                
                // Print settings
                $sheet->getPageSetup()->setOrientation(\PhpOffice\PhpSpreadsheet\Worksheet\PageSetup::ORIENTATION_LANDSCAPE);
                $sheet->getPageSetup()->setFitToWidth(1);
                $sheet->getPageSetup()->setFitToHeight(0);
            },
        ];
    }
    
    /**
     * Mendapatkan teks periode laporan.
     *
     * @return string
     */
    private function getPeriodText(): string
    {
        if ($this->startDate && $this->endDate) {
            $start = Carbon::parse($this->startDate)->format('d/m/Y');
            $end = Carbon::parse($this->endDate)->format('d/m/Y');
            return "Periode: {$start} - {$end}";
        } elseif ($this->startDate) {
            return "Mulai: " . Carbon::parse($this->startDate)->format('d/m/Y');
        } elseif ($this->endDate) {
            return "Sampai: " . Carbon::parse($this->endDate)->format('d/m/Y');
        }
        return "Semua Periode";
    }
}
