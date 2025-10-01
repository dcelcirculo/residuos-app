<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;

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
        // Gate para proteger rutas solo para administradores
        Gate::define('admin-only', function ($user) {
            // Si tu modelo User tiene el método isAdmin(), úsalo:
            return method_exists($user, 'isAdmin')
                ? $user->isAdmin()
                : ($user->role === 'admin');
        });
    }
}
