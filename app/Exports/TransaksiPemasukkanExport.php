<?php

namespace App\Exports;

use App\Models\TransaksiPemasukkan;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Events\AfterSheet;
use Illuminate\Support\Facades\Auth;

class TransaksiPemasukkanExport implements WithCustomStartCell, FromQuery, WithHeadings, WithEvents, WithStyles, WithColumnWidths
{
    use \Maatwebsite\Excel\Concerns\Exportable;

    private $tableFilters;
    private $rowCount;

    public function __construct(array $tableFilters = [])
    {
        // Receive Filament table filters
        $this->tableFilters = $tableFilters;
    }

    public function query()
    {
        $query = TransaksiPemasukkan::query()
            ->select(
                'tr_pemasukkan.kode',
                'tr_pemasukkan.tanggal_pemasukkan',
                'tr_pemasukkan.jam_pemasukkan',
                'mm_kategori_pemasukkan.nama as kategori',
                'tr_pemasukkan.balance_pemasukkan',
                'tr_pemasukkan.catatan_pemasukkan',
                'mm_jenis_penyimpanan.nama as penyimpanan'
            )
            ->leftJoin('mm_kategori_pemasukkan', 'tr_pemasukkan.id_kategori_pemasukkan', '=', 'mm_kategori_pemasukkan.id')
            ->leftJoin('mm_jenis_penyimpanan', 'tr_pemasukkan.id_jenis_penyimpanan', '=', 'mm_jenis_penyimpanan.id')
            ->where('tr_pemasukkan.deleted', false);

        // Apply Filament table filters
        if (isset($this->tableFilters['Tanggal'])) {
            $dateFilter = $this->tableFilters['Tanggal'];
            
            if (!empty($dateFilter['start_date'])) {
                $query->whereDate('tanggal_pemasukkan', '>=', $dateFilter['start_date']);
            }

            if (!empty($dateFilter['end_date'])) {
                $query->whereDate('tanggal_pemasukkan', '<=', $dateFilter['end_date']);
            }
        }

        $this->rowCount = $query->count();
        return $query;
    }

    public function headings(): array
    {
        return [
            'Kode Transaksi',
            'Tanggal',
            'Jam',
            'Kategori',
            'Saldo',
            'Catatan',
            'Penyimpanan'
        ];
    }

    public function columnWidths(): array
    {
        return [
            'A' => 20, // Kode Transaksi
            'B' => 15, // Tanggal
            'C' => 10, // Jam
            'D' => 20, // Kategori
            'E' => 20, // Saldo
            'F' => 50, // Catatan
            'G' => 20, // Penyimpanan
        ];
    }

    public function startCell(): string
    {
        return 'A5';
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function(AfterSheet $event) {
                $sheet = $event->sheet;
                $lastColumn = 'G';
                $startRow = 6;
                $highestRow = $this->rowCount + $startRow - 1;

                // Header Information
                $sheet->mergeCells("A1:{$lastColumn}1");
                $sheet->setCellValue('A1', 'Laporan Transaksi Pemasukkan');
                
                // Filter Information
                $filterText = 'Semua Data';
                if (isset($this->tableFilters['Tanggal'])) {
                    $startDate = $this->tableFilters['Tanggal']['start_date'] ?? '-';
                    $endDate = $this->tableFilters['Tanggal']['end_date'] ?? '-';
                    $filterText = "Periode: {$startDate} s/d {$endDate}";
                }
                $sheet->mergeCells("A2:{$lastColumn}2");
                $sheet->setCellValue('A2', $filterText);

                // Export Information
                $sheet->setCellValue('A3', 'Dicetak oleh: ' . Auth::user()->name);
                $sheet->setCellValue('A4', 'Waktu Cetak: ' . now()->format('d-m-Y H:i:s'));

                // Styling
                $sheet->getStyle("A1:{$lastColumn}1")->applyFromArray([
                    'font' => ['size' => 16, 'bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                $sheet->getStyle("A2:{$lastColumn}2")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                ]);

                // Header styling
                $sheet->getStyle("A5:{$lastColumn}5")->applyFromArray([
                    'font' => ['bold' => true],
                    'alignment' => ['horizontal' => 'center'],
                    'fill' => [
                        'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                        'startColor' => ['rgb' => 'E2EFDA'],
                    ],
                ]);

                // Data formatting
                foreach (range($startRow, $highestRow) as $row) {
                    // Format date
                    $sheet->setCellValue(
                        'B' . $row,
                        \Carbon\Carbon::parse($sheet->getCell('B' . $row)->getValue())->format('d/m/Y')
                    );

                    // Format time
                    $sheet->setCellValue(
                        'C' . $row,
                        \Carbon\Carbon::parse($sheet->getCell('C' . $row)->getValue())->format('H:i')
                    );
                }

                // Aktifkan word wrap untuk kolom Catatan
                $sheet->getStyle('F' . $startRow . ':F' . $highestRow)->getAlignment()->setWrapText(true);

                // Number formatting for balance column
                $sheet->getStyle("E{$startRow}:E{$highestRow}")
                    ->getNumberFormat()
                    ->setFormatCode('#,##');

                // Add borders
                $sheet->getStyle("A5:{$lastColumn}{$highestRow}")->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        ],
                    ],
                ]);

                 // Atur posisi data tanggal
                 $sheet->getStyle('B' . $startRow . ':B' . $highestRow)->applyFromArray([
                    'alignment' => ['horizontal' => 'center'],
                ]);

                // Atur posisi data jam
                $sheet->getStyle('C' . $startRow . ':C' . $highestRow)->applyFromArray([
                    'alignment' => ['horizontal' => 'center'],
                ]);

                // Atur posisi data balance
                $sheet->getStyle('E' . $startRow . ':E' . $highestRow)->applyFromArray([
                    'alignment' => ['horizontal' => 'center'],
                ]);

                 // Atur tinggi baris secara otomatis untuk semua baris data
                 foreach (range($startRow, $highestRow) as $row) {
                    $sheet->getRowDimension($row)->setRowHeight(-1);
                }

                // Total row
                $sheet->setCellValue("A" . ($highestRow + 1), 'Total Data: ' . $this->rowCount);
            },
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [];
    }
}