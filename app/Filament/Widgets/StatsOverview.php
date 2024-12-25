<?php

namespace App\Filament\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Models\TransaksiPemasukkan;
use App\Models\TransaksiPengeluaran;
use App\Models\Transaksi;

class StatsOverview extends BaseWidget
{
    protected int|string|array $columnSpan = 'full'; 

    protected static ?int $sort = 1;

    protected function getStats(): array
    {

        // Hitung total pemasukkan hari ini
        $totalIncomeToday = TransaksiPemasukkan::whereDate('tanggal_pemasukkan', today())
            ->where('deleted', false)
            ->sum('balance_pemasukkan');

        // Hitung total pengeluaran hari ini
        $totalExpanseToday = TransaksiPengeluaran::whereDate('tanggal_pengeluaran', today())
            ->where('deleted', false)
            ->sum('balance_pengeluaran');
            
        // Hitung total uang keseluruhan
        $totalBalanceIn = Transaksi::where('tipe', 'IN')
            ->where('deleted', false)
            ->sum('balance');

        $totalBalanceOut = Transaksi::where('tipe', 'OUT')
            ->where('deleted', false)
            ->sum('balance');

        $totalStorage = $totalBalanceIn - $totalBalanceOut;

        return [
            Stat::make('Total Pemasukkan Hari Ini', 'Rp' . number_format($totalIncomeToday, 0, ',', '.'))
                ->description('Pembaruan data secara real-time')
                ->descriptionIcon('heroicon-o-arrow-path') 
                ->color('success'),
            Stat::make('Total Pengeluaran Hari Ini', 'Rp' . number_format($totalExpanseToday, 0, ',', '.'))
                ->description('Pembaruan data secara real-time')
                ->descriptionIcon('heroicon-o-arrow-path') 
                ->color('success'),
            Stat::make('Total Penyimpanan', 'Rp' . number_format($totalStorage))
                ->description('Berdasarkan total transaksi')
                ->descriptionIcon('heroicon-o-wallet') 
                ->color($totalStorage > 0 ? 'success' : 'danger'),
        ];
    }

}
