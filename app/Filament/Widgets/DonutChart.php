<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Filament\Charts\Chart;
use Filament\Charts\Series;
use Illuminate\Support\Facades\DB;

class DonutChart extends ChartWidget
{
    protected static ?string $heading = 'Jenis Penyimpanan';

    protected static ?int $sort = 2;

    protected function getData(): array
    {
        $data = DB::table('mm_transaksi')
            ->select(
                'mm_jenis_penyimpanan.nama',
                DB::raw('SUM(CASE WHEN mm_transaksi.tipe = \'IN\'  THEN mm_transaksi.balance ELSE 0 END) - SUM(CASE WHEN mm_transaksi.tipe = \'OUT\' THEN mm_transaksi.balance ELSE 0 END) AS total_balance')
            )
            ->join('mm_jenis_penyimpanan', 'mm_transaksi.id_jenis_penyimpanan', '=', 'mm_jenis_penyimpanan.id')
            ->groupBy('mm_jenis_penyimpanan.id', 'mm_jenis_penyimpanan.nama')
            ->where('mm_transaksi.deleted',false)
            ->get();

        $labels = $data->pluck('nama')->toArray();
        $values = $data->pluck('total_balance')->toArray();

        return [
            'labels' => $labels,
            'datasets' => [
                [
                    'data' => $values,
                    'backgroundColor' => ['#3a00ca','#fd4900','#00e342','#ff305c'],
                ],
            ],
        ];
    }

    public function chart(): Chart
    {
        return Chart::make()
            ->donut()
            ->labels($this->getData()['labels'])
            ->datasets($this->getData()['datasets']);
    }

    protected function getType(): string
    {
        return 'doughnut';
    }
}
