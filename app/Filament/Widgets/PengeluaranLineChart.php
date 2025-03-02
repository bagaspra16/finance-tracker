<?php

namespace App\Filament\Widgets;

use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class PengeluaranLineChart extends ChartWidget
{
    protected static ?string $heading = 'Perkembangan Pengeluaran';
    protected static ?int $sort = 5;

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

        $expenseData = DB::table('tr_pengeluaran')
            ->select(DB::raw('DATE(tanggal_pengeluaran) as date'), DB::raw('SUM(balance_pengeluaran) as total'))
            ->whereMonth('tanggal_pengeluaran', $selectedMonth)
            ->whereYear('tanggal_pengeluaran', Carbon::now()->year)
            ->groupBy('date')
            ->orderBy('date', 'ASC')
            ->get()
            ->keyBy('date');

        $expenses = $dates->map(fn ($date) => $expenseData[$date]->total ?? 0)->toArray();

        return [
            'labels' => $dates->map(fn ($date) => Carbon::parse($date)->format('d'))->toArray(),
            'datasets' => [
                [
                    'label' => 'Balance Pengeluaran',
                    'data' => $expenses,
                    'borderColor' => 'rgba(255, 99, 132, 1)', // Merah
                    'backgroundColor' => 'rgba(255, 99, 132, 0.2)', // Merah transparan
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
