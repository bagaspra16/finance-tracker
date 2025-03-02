<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PemasukkanLineChart extends ChartWidget
{
    protected static ?string $heading = 'Perkembangan Pemasukkan';
    protected static ?int $sort = 4;

    public ?string $filter = null;

    public function mount(): void
    {
        $currentMonth = strtolower(Carbon::now()->locale('id')->monthName);
        $this->filter = $currentMonth;
    }

    protected function getFilters(): ?array
    {
        return [
            'januari' => 'Januari',
            'februari' => 'Februari',
            'maret' => 'Maret',
            'april' => 'April',
            'mei' => 'Mei',
            'juni' => 'Juni',
            'juli' => 'Juli',
            'agustus' => 'Agustus',
            'september' => 'September',
            'oktober' => 'Oktober',
            'november' => 'November',
            'desember' => 'Desember',
        ];
    }

    protected function getData(): array
    {
        $selectedMonth = match ($this->filter) {
            'januari' => 1,
            'februari' => 2,
            'maret' => 3,
            'april' => 4,
            'mei' => 5,
            'juni' => 6,
            'juli' => 7,
            'agustus' => 8,
            'september' => 9,
            'oktober' => 10,
            'november' => 11,
            'desember' => 12,
            default => Carbon::now()->month,
        };

        $dates = collect(range(1, Carbon::create(null, $selectedMonth)->daysInMonth))->map(fn ($day) =>
            Carbon::create(null, $selectedMonth, $day)->format('Y-m-d')
        );

        $balanceData = DB::table('tr_pemasukkan')
            ->select(DB::raw('DATE(tanggal_pemasukkan) as date'), DB::raw('SUM(balance_pemasukkan) as total'))
            ->whereMonth('tanggal_pemasukkan', $selectedMonth)
            ->whereYear('tanggal_pemasukkan', Carbon::now()->year)
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->keyBy('date');

        $balances = $dates->map(fn ($date) => $balanceData[$date]->total ?? 0)->toArray();

        return [
            'labels' => $dates->map(fn ($date) => Carbon::parse($date)->format('d'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Balance Pemasukkan',
                    'data' => $balances,
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                    'backgroundColor' => 'rgba(54, 162, 235, 0.2)',
                    'tension' => 0.4,
                ],
            ],
        ];
    }

    protected function getType(): string
    {
        return 'line';
    }
}
