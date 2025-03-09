<?php

namespace App\Filament\Resources\YesResource\Widgets;

use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class YourStatsOverview extends BaseWidget
{
    protected function getStats(): array
    {
        return [
             Card::make('Total Pengguna', User::count())
                ->description('Peningkatan 3% dari bulan lalu')
                ->descriptionIcon('heroicon-s-trending-up')
                ->color('success'),
                
            Card::make('Pendapatan', 'Rp ' . number_format(Order::sum('total') ?? 0, 0, ',', '.'))
                ->description('Penurunan 2% dari bulan lalu')
                ->descriptionIcon('heroicon-s-trending-down')
                ->color('danger'),
                
            Card::make('Pesanan Baru', Order::whereDate('created_at', today())->count())
                ->description(Order::whereDate('created_at', yesterday())->count() . ' kemarin')
                ->color('primary'),
        ];
    }
}
