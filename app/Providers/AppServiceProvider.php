<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Pagination\Paginator;

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
        // Set default string length for MySQL compatibility
        Schema::defaultStringLength(191);
        
        // Use Bootstrap pagination views
        Paginator::useBootstrapFive();
        
        // Set timezone for Indonesia
        date_default_timezone_set('Asia/Jakarta');
    }
}
