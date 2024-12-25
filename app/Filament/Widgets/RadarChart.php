<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Charts\Chart;
use Illuminate\Support\Facades\DB;

class RadarChart extends ChartWidget
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
            ->get();

        // Combine both data sources
        $labels = $pemasukkan->pluck('nama')->merge($pengeluaran->pluck('nama'))->unique()->toArray();
        $pemasukkanCounts = collect($labels)->map(fn($label) => $pemasukkan->firstWhere('nama', $label)->count ?? 0)->toArray();
        $pengeluaranCounts = collect($labels)->map(fn($label) => $pengeluaran->firstWhere('nama', $label)->count ?? 0)->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'label' => 'Pemasukkan',
                    'data' => $pemasukkanCounts,
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => $pengeluaranCounts,
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',
                ],
            ],
        ];
    }

    public function chart(): Chart
    {
        return Chart::make()
            ->radar()
            ->labels($this->getData()['labels'])
            ->datasets($this->getData()['datasets']);
    }

    protected function getType(): string
    {
        return 'radar';
    }
}
