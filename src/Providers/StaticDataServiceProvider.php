<?php

declare(strict_types=1);

namespace Gyrobus\MoonshineStaticData\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

final class StaticDataServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'moonshine-static-data');

        $this->publishes([
            __DIR__ . '/../../config/static-data.php' => config_path('moonshine-static-data.php'),
        ]);

        $this->publishes([
            __DIR__ . '/../../lang' => $this->app->langPath('vendor/moonshine-static-data'),
        ]);

        $this->commands([]);

        Blade::directive('staticData', function ($key, $default = '') {
            return staticData($key, $default);
        });
    }
}