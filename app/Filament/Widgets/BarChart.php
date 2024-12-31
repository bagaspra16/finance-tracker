<?php

namespace App\Filament\Widgets;

use App\Models\Transaksi;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Carbon;

class BarChart extends ChartWidget
{
    protected static ?string $heading = 'Grafik Transaksi';
    protected int | string | array $columnSpan = 'full';
    public ?string $filter = null;

    protected static ?int $sort = 4;

    public function mount(): void
    {
        parent::mount();
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

        $data = Transaksi::whereMonth('tanggal', $selectedMonth)
            ->whereYear('tanggal', Carbon::now()->year)
            ->get()
            ->groupBy(function($item) {
                return Carbon::parse($item->tanggal)->format('d');
            })
            ->map(function($group) {
                $income = $group->where('transaksi', 'PEMASUKKAN')->sum('balance');
                $expense = $group->where('transaksi', 'PENGELUARAN')->sum('balance');
                $codes = $group->pluck('kode')->toArray();
                return [
                    'income' => $income,
                    'expense' => $expense,
                    'codes' => $codes
                ];
            });

        $maxValue = max([
            $data->max('income') ?? 0,
            $data->max('expense') ?? 0
        ]);

        $days = range(1, Carbon::create(null, $selectedMonth)->daysInMonth);
        
        return [
            'datasets' => [
                [
                    'label' => 'Pemasukkan',
                    'data' => collect($days)->map(fn($day) => 
                        $data->get($day < 10 ? "0$day" : "$day")['income'] ?? 0
                    )->toArray(),
                    'backgroundColor' => 'rgba(54, 162, 235, 0.5)',
                    'borderColor' => 'rgba(54, 162, 235, 1)',
                ],
                [
                    'label' => 'Pengeluaran',
                    'data' => collect($days)->map(fn($day) => 
                        $data->get($day < 10 ? "0$day" : "$day")['expense'] ?? 0
                    )->toArray(),
                    'backgroundColor' => 'rgba(255, 99, 132, 0.5)',
                    'borderColor' => 'rgba(255, 99, 132, 1)',   
                ],
            ],
            'labels' => collect($days)->map(fn($day) => "$day")->toArray(),
            'codes' => collect($days)->map(fn($day) => 
                implode(', ', $data->get($day < 10 ? "0$day" : "$day")['codes'] ?? [])
            )->toArray(),
            'maxValue' => $maxValue * 1.5,
        ];
    }

    protected function getOptions(): array
    {
        return [
            'responsive' => true,
            'scales' => [
                'y' => [
                    'beginAtZero' => true,
                    'max' => $this->getData()['maxValue'],
                    'title' => [
                        'display' => true,
                        'text' => 'Balance (Rp)',
                    ],
                    'ticks' => [
                        'callback' => 'function(value) { 
                            return new Intl.NumberFormat("id-ID", {
                                style: "currency",
                                currency: "IDR",
                                maximumFractionDigits: 0
                            }).format(value);
                        }',
                    ],
                ],
                'x' => [
                    'title' => [
                        'display' => true,
                        'text' => 'Tanggal',
                    ],
                ],
            ],
            'plugins' => [
                'tooltip' => [
                    'callbacks' => [
                        'title' => 'function(context) {
                            return context[0].label;
                        }',
                        'label' => 'function(context) {
                            let value = context.raw;
                            let code = context.chart.config._config.data.codes[context.dataIndex];
                            return `${context.dataset.label}: ${new Intl.NumberFormat("id-ID", {
                                style: "currency",
                                currency: "IDR",
                                maximumFractionDigits: 0
                            }).format(value)} (Kode: ${code})`;
                        }',
                    ],
                ],
                'legend' => [
                    'position' => 'top',
                ],
            ],
            'interaction' => [
                'intersect' => false,
                'mode' => 'index',
            ],
        ];
    }

    protected function getType(): string
    {
        return 'bar';
    }
}
