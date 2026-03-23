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
        \Illuminate\Support\Facades\Gate::policy(
            \BezhanSalleh\FilamentExceptions\Models\Exception::class,
            \App\Policies\ExceptionPolicy::class
        );

        if ($this->app->runningInConsole() || $this->app->environment('testing')) {
            if (class_exists(\BezhanSalleh\FilamentExceptions\FilamentExceptions::class)) {
                \BezhanSalleh\FilamentExceptions\FilamentExceptions::stopRecording();
            }
        }
    }
}
