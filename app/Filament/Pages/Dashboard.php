<?php

namespace App\Filament\Pages;

use Filament\Pages\Dashboard as BaseDashboard;
use App\Filament\Widgets\StatsOverview;
use App\Filament\Widgets\DonutChart;
use App\Filament\Widgets\PieChart;
use App\Filament\Widgets\BarChart;

class Dashboard extends BaseDashboard
{

    public function getWidgets(): array
    {
        return [
            StatsOverview::class,
            DonutChart::class,
            PieChart::class,
            BarChart::class,

        ];
    }
}
