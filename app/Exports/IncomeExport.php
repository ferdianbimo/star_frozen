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
use App\Models\StockLog;

/**
 * IncomeExport - Export data pemasukan (penjualan) ke Excel dengan format profesional.
 *
 * Class ini mengimplementasikan Maatwebsite Excel untuk export
 * data pemasukan dari StockLog dengan format laporan yang rapi dan profesional.
 *
 * @package    App\Exports
 * @author     Star Frozen Team
 * @version    2.0.0
 */
class IncomeExport implements FromCollection, WithHeadings, WithMapping, WithStyles, WithColumnWidths, WithTitle, WithEvents, ShouldAutoSize
{
    /**
     * Collection data StockLog (sale) yang akan di-export.
     *
     * @var Collection
     */
    protected $incomeLogs;

    /**
     * Total pemasukan untuk ditampilkan di summary row.
     *
     * @var float
     */
    protected $totalValue;

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
     * Counter nomor baris untuk penomoran otomatis.
     *
     * @var int
     */
    protected $rowNumber = 0;

    /**
     * Membuat instance IncomeExport baru.
     *
     * @param  Collection  $incomeLogs Collection data StockLog penjualan
     * @param  float       $totalValue Total nilai pemasukan
     * @param  string|null $startDate  Tanggal awal periode (opsional)
     * @param  string|null $endDate    Tanggal akhir periode (opsional)
     * @return void
     */
    public function __construct($incomeLogs, $totalValue, $startDate = null, $endDate = null)
    {
        $this->incomeLogs = $incomeLogs;
        $this->totalValue = $totalValue;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    /**
     * Mendapatkan collection data untuk export.
     *
     * @return Collection
     */
    public function collection()
    {
        return $this->incomeLogs;
    }

    /**
     * Mendapatkan judul sheet Excel.
     *
     * @return string
     */
    public function title(): string
    {
        return 'Laporan Pemasukan';
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
            'Waktu',
            'Produk',
            'Kode',
            'Qty',
            'Satuan',
            'Harga Satuan (Rp)',
            'Total (Rp)',
            'Kasir',
            'Catatan'
        ];
    }

    /**
     * Mapping setiap row StockLog ke format array.
     *
     * @param  mixed $log Data stock log penjualan yang akan di-map
     * @return array<mixed>
     */
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

    /**
     * Mendapatkan lebar kolom dalam karakter.
     *
     * @return array<string, int>
     */
    public function columnWidths(): array
    {
        return [
            'A' => 6,    // No
            'B' => 12,   // Tanggal
            'C' => 8,    // Waktu
            'D' => 25,   // Produk
            'E' => 12,   // Kode
            'F' => 8,    // Qty
            'G' => 10,   // Satuan
            'H' => 16,   // Harga Satuan
            'I' => 16,   // Total
            'J' => 14,   // Kasir
            'K' => 20,   // Catatan
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
            // Header row styling - hijau untuk pemasukan
            1 => [
                'font' => [
                    'bold' => true,
                    'color' => ['rgb' => 'FFFFFF'],
                    'size' => 11,
                ],
                'fill' => [
                    'fillType' => Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '059669'],
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
                $lastRow = $this->incomeLogs->count() + 1;
                
                // Set row height for header
                $sheet->getRowDimension(1)->setRowHeight(28);
                
                // Style all data cells - border
                $sheet->getStyle('A1:K' . $lastRow)->applyFromArray([
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
                    $bgColor = ($i % 2 == 0) ? 'ECFDF5' : 'FFFFFF';
                    $sheet->getStyle('A' . $i . ':K' . $i)->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'startColor' => ['rgb' => $bgColor],
                        ],
                    ]);
                    $sheet->getRowDimension($i)->setRowHeight(22);
                }
                
                // Format currency columns
                $sheet->getStyle('H2:I' . $lastRow)->getNumberFormat()
                    ->setFormatCode('#,##0');
                
                // Center align kolom No, Tanggal, Waktu, Qty, Satuan
                $sheet->getStyle('A2:C' . $lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                $sheet->getStyle('F2:G' . $lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_CENTER);
                
                // Right align kolom Harga dan Total
                $sheet->getStyle('H2:I' . $lastRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                
                // Wrap text untuk Catatan
                $sheet->getStyle('K2:K' . $lastRow)->getAlignment()
                    ->setWrapText(true);
                
                // Add total row
                $totalRow = $lastRow + 1;
                $sheet->setCellValue('H' . $totalRow, 'TOTAL PEMASUKAN:');
                $sheet->setCellValue('I' . $totalRow, $this->totalValue);
                
                // Style total row
                $sheet->getStyle('A' . $totalRow . ':K' . $totalRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 12,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'startColor' => ['rgb' => '047857'],
                    ],
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_MEDIUM,
                            'color' => ['rgb' => '065F46'],
                        ],
                    ],
                    'alignment' => [
                        'vertical' => Alignment::VERTICAL_CENTER,
                    ],
                ]);
                $sheet->getStyle('H' . $totalRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('I' . $totalRow)->getAlignment()
                    ->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('I' . $totalRow)->getNumberFormat()
                    ->setFormatCode('"Rp "#,##0');
                $sheet->getRowDimension($totalRow)->setRowHeight(30);
                
                // Add info row
                $infoRow = $totalRow + 2;
                $periodText = $this->getPeriodText();
                $sheet->setCellValue('A' . $infoRow, 'LAPORAN PEMASUKAN - STAR FROZEN');
                $sheet->mergeCells('A' . $infoRow . ':K' . $infoRow);
                $sheet->getStyle('A' . $infoRow)->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size' => 14,
                        'color' => ['rgb' => '059669'],
                    ],
                    'alignment' => [
                        'horizontal' => Alignment::HORIZONTAL_CENTER,
                    ],
                ]);
                
                $infoRow2 = $infoRow + 1;
                $sheet->setCellValue('A' . $infoRow2, $periodText . ' | Dicetak: ' . Carbon::now()->format('d/m/Y H:i'));
                $sheet->mergeCells('A' . $infoRow2 . ':K' . $infoRow2);
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
