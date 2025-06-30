<?php

declare(strict_types=1);

namespace Gyrobus\MoonshineStaticData\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

final class StaticDataServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/moonshine-static-data.php',
            'moonshine-static-data'
        );
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'moonshine-static-data');

        $this->publishes([
            __DIR__ . '/../../config/moonshine-static-data.php' => config_path('moonshine-static-data.php'),
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
            __DIR__ . '/../../lang' => $this->app->langPath('vendor/moonshine-static-data'),
        ], 'moonshine-static-data');

        Blade::directive('staticData', function ($key, $default = '') {
            return staticData($key, $default);
        });
    }
}