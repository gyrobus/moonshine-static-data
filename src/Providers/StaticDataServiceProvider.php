<?php

declare(strict_types=1);

namespace Gyrobus\MoonshineStaticData\Providers;

use Gyrobus\MoonshineStaticData\Resources\StaticDataResource;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use MoonShine\Contracts\Core\DependencyInjection\CoreContract;
use MoonShine\Contracts\MenuManager\MenuManagerContract;
use MoonShine\MenuManager\MenuItem;

final class StaticDataServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../../config/moonshine-static-data.php',
            'moonshine-static-data'
        );
    }

    public function boot(CoreContract $core, MenuManagerContract $menu): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');
        $this->loadTranslationsFrom(__DIR__ . '/../../lang', 'moonshine-static-data');

        $this->publishes([
            __DIR__ . '/../../config/moonshine-static-data.php' => config_path('moonshine-static-data.php'),
            __DIR__ . '/../../database/migrations' => database_path('migrations'),
            __DIR__ . '/../../lang' => $this->app->langPath('vendor/moonshine-static-data'),
        ], 'moonshine-static-data');

        Blade::directive('staticData', function ($expression) {
            return "<?php echo function_exists('staticData') ? staticData(...[$expression]) : ''; ?>";
        });

        $core->resources([
            StaticDataResource::class
        ]);

        $menu->add([
            MenuItem::make(__('moonshine-static-data::main.menu'), StaticDataResource::class)
        ]);
    }
}