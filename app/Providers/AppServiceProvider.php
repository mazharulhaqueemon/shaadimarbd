<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Filesystem\Filesystem;
use Filament\Facades\Filament;
use App\Models\Payment;
use App\Observers\PaymentObserver;



class AppServiceProvider extends ServiceProvider
{
    
    // Register any application services.
    public function register(): void
    {
        if (! $this->app->bound('files')) {
            $this->app->singleton('files', function () {
                return new Filesystem();
            });
        }
    }

    // Bootstrap any application services.
    public function boot(): void
    {
                // Register the Payment observer
        Payment::observe(PaymentObserver::class);

        // Filament admin panel access restriction
        Filament::serving(function () {
            // Only restrict access after login
            if (auth()->check() && !auth()->user()->is_admin) {
                abort(403); 
            }
        });

    }
}
