<?php 

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Widgets\ChartWidget;
use Illuminate\Support\Facades\DB;
use Illuminate\Contracts\View\View;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Support\Facades\FilamentView;
use Filament\Widgets\StatsOverviewWidget\Stat;
use App\Filament\Resources\YesResource\Widgets\UserChart;

class Dashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-home';
    
    protected static string $view = 'filament.pages.dashboard';
    
    public function getHeaderWidgets(): array
    {
        return [
             UserChart::class,
        ];
    }

}
