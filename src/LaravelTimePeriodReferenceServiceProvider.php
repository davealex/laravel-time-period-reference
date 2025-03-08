<?php

namespace Davealex\LaravelTimePeriodReference;

use Illuminate\Support\ServiceProvider;

class LaravelTimePeriodReferenceServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap the application services.
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__.'/../config/config.php' => config_path('laravel-time-period-reference.php'),
            ], 'config');
        }
    }

    /**
     * Register the application services.
     */
    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/../config/config.php', 'laravel-time-period-reference');

        $this->app->singleton('laravel-time-period-reference', function () {
            return new LaravelTimePeriodReference(config('laravel-time-period-reference'));
        });
    }
}
