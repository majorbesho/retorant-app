<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Gate;
use App\Models\User;

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
        Paginator::useBootstrap();

        Gate::define('manage-system', function (User $user) {
            return $user->is_super_admin || $user->hasRole('super_admin');
        });

        Gate::define('manage-requests', function (User $user) {
            return $user->is_super_admin || $user->hasRole('super_admin') || $user->hasRole('restaurant_owner');
        });

        // Share active restaurants with registration view
        view()->composer('auth.register', function ($view) {
            $view->with('restaurants', \App\Models\Restaurant::where('is_active', true)->get());
        });
    }
}
