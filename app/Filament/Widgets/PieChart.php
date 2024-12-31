<?php
namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Charts\Chart;
use Illuminate\Support\Facades\DB;

class PieChart extends ChartWidget
{
    protected static ?string $heading = 'Kategori Transaksi';
    protected static ?int $sort = 3;

    protected function getData(): array
    {
        // Fetch category counts for pemasukkan
        $pemasukkan = DB::table('tr_pemasukkan')
            ->select(
                'mm_kategori_pemasukkan.nama',
                DB::raw('COUNT(tr_pemasukkan.id_kategori_pemasukkan) as count')
            )
            ->join('mm_kategori_pemasukkan', 'tr_pemasukkan.id_kategori_pemasukkan', '=', 'mm_kategori_pemasukkan.id')
            ->groupBy('mm_kategori_pemasukkan.id', 'mm_kategori_pemasukkan.nama')
            ->where('tr_pemasukkan.deleted', false)
            ->orderBy('count', 'desc')
            ->get();

        // Fetch category counts for pengeluaran
        $pengeluaran = DB::table('tr_pengeluaran')
            ->select(
                'mm_kategori_pengeluaran.nama',
                DB::raw('COUNT(tr_pengeluaran.id_kategori_pengeluaran) as count')
            )
            ->join('mm_kategori_pengeluaran', 'tr_pengeluaran.id_kategori_pengeluaran', '=', 'mm_kategori_pengeluaran.id')
            ->groupBy('mm_kategori_pengeluaran.id', 'mm_kategori_pengeluaran.nama')
            ->where('tr_pengeluaran.deleted', false)
            ->orderBy('count', 'desc')
            ->get();

        // Combine both data sources
        $combinedData = collect();
        
        foreach ($pemasukkan as $item) {
            $combinedData->push([
                'nama' => $item->nama . ' (Pemasukkan)',
                'count' => $item->count,
                'type' => 'Pemasukkan'
            ]);
        }
        
        foreach ($pengeluaran as $item) {
            $combinedData->push([
                'nama' => $item->nama . ' (Pengeluaran)',
                'count' => $item->count,
                'type' => 'Pengeluaran'
            ]);
        }

        // Sort by count
        $sortedData = $combinedData->sortByDesc('count');
        
        $backgroundColors = [
            // Vibrant colors for variety
            'rgba(255, 99, 132, 0.8)',   // Salmon Pink
            'rgba(54, 162, 235, 0.8)',   // Blue
            'rgba(255, 206, 86, 0.8)',   // Yellow
            'rgba(75, 192, 192, 0.8)',   // Teal
            'rgba(153, 102, 255, 0.8)',  // Purple
            'rgba(255, 159, 64, 0.8)',   // Orange
            'rgba(76, 175, 80, 0.8)',    // Green
            'rgba(233, 30, 99, 0.8)',    // Pink
            'rgba(3, 169, 244, 0.8)',    // Light Blue
            'rgba(255, 152, 0, 0.8)',    // Deep Orange
            'rgba(156, 39, 176, 0.8)',   // Deep Purple
            'rgba(121, 85, 72, 0.8)',    // Brown
            'rgba(0, 150, 136, 0.8)',    // Turquoise
            'rgba(63, 81, 181, 0.8)',    // Indigo
            'rgba(139, 195, 74, 0.8)',   // Light Green
            'rgba(205, 220, 57, 0.8)',   // Lime
            'rgba(255, 235, 59, 0.8)',   // Light Yellow
            'rgba(158, 158, 158, 0.8)',  // Gray
            'rgba(96, 125, 139, 0.8)',   // Blue Gray
            'rgba(244, 67, 54, 0.8)',    // Red
        ];

        return [
            'labels' => $sortedData->pluck('nama')->toArray(),
            'datasets' => [
                [
                    'label' => 'Jumlah Transaksi',
                    'data' => $sortedData->pluck('count')->toArray(),
                    'backgroundColor' => $backgroundColors,
                    'borderColor' => '#ffffff',
                    'borderWidth' => 1
                ]
            ]
        ];
    }

    public function chart(): Chart
    {
        return Chart::make()
            ->pie()
            ->labels($this->getData()['labels'])
            ->datasets($this->getData()['datasets']);
    }

    protected function getType(): string
    {
        return 'pie';
    }
}