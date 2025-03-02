<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\DonutChart;
use App\Filament\Widgets\RadarChart;
use App\Filament\Widgets\BarChart;
use App\Filament\Widgets\PemasukkanLineChart;
use App\Filament\Widgets\PengeluaranLineChart;

class Dashboard extends BaseDashboard
{

    public static function getNavigationIcon(): string
    {
    return 'heroicon-o-computer-desktop';
    }

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            DonutChart::class,
            RadarChart::class,
            PemasukkanLineChart::class,
            PengeluaranLineChart::class,
            BarChart::class,

        ];
    }
}
