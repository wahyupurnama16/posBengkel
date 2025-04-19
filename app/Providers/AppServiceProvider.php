<?php
namespace App\Providers;

use Filament\Pages\Auth\Login;
use Illuminate\Support\Facades\Blade;
use Illuminate\Support\ServiceProvider;
use App\Filament\Pages\Auth\CustomLogin;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::anonymousComponentPath(resource_path('views/filament/pages/auth'), 'filament.pages.auth');
    }
}
