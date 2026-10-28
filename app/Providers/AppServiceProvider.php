<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

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
        // Force HTTPS URL generation when behind SSL-terminating proxy.
        if (config('app.env') !== 'local') {
            \URL::forceScheme('http');
        }
        // Set locale for Carbon to Indonesian
        \Carbon\Carbon::setLocale('id');
    }
}
