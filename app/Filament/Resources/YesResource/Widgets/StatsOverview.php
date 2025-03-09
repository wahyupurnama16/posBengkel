<?php

namespace App\Filament\Widgets;

use App\Models\User;
use App\Models\Order;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Card;
use Carbon\Carbon;

class StatsOverview extends BaseWidget
{
    protected function getCards(): array
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
                
            Card::make('Pesanan Baru', Order::whereDate('created_at', Carbon::today())->count())
                ->description(Order::whereDate('created_at', Carbon::yesterday())->count() . ' kemarin')
                ->color('primary'),
        ];
    }
}