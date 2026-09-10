<?php

namespace App\Providers;

use App\Services\SystemHealthService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;

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
        View::composer(['layouts.admin', 'layouts.employee', 'admin.dashboard'], function ($view): void {
            $view->with('systemHealth', app(SystemHealthService::class)->check());
        });
    }
}
