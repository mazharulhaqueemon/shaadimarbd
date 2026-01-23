<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        /**
         * Gate to restrict Filament panel access
         * 
         * Only users with `is_admin = true/1` will get access.
         */
        Gate::define('access-filament', fn ($user) => (bool) $user->is_admin);


        // Gate::define('access-filament', fn ($user) => (bool) $user->is_admin);
    }
}
