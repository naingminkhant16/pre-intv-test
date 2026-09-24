<?php

namespace App\Providers;

use App\Models\Project;
use App\Services\ProjectService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ProjectService::class, fn() => new ProjectService(new Project()));
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
