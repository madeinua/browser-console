<?php

declare(strict_types=1);

namespace BrowserConsole;

use Illuminate\Support\ServiceProvider;

class BrowserConsoleServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     *
     * @return void
     */
    public function register(): void
    {
        $this->mergeConfigFrom(__DIR__ . '/../config/browser-console.php', 'browser-console');

        $this->app->singleton(BrowserConsole::class, function ($app) {
            $enabled = $app['config']->get('browser-console.enabled', true);

            return new BrowserConsole($enabled);
        });

        $this->app->alias(BrowserConsole::class, 'browser-console');
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/../config/browser-console.php' => config_path('browser-console.php'),
            ], 'browser-console-config');
        }
    }
}
