<?php

namespace Webkul\YapePlin\Providers;

use Illuminate\Support\ServiceProvider;

class YapePlinServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     */
    public function boot(): void
    {
        $this->loadMigrationsFrom(__DIR__ . '/../Database/Migrations');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/shop-routes.php');

        $this->loadRoutesFrom(__DIR__ . '/../Routes/admin-routes.php');

        $this->loadTranslationsFrom(__DIR__ . '/../Resources/lang', 'yapeplin');

        $this->loadViewsFrom(__DIR__ . '/../Resources/views', 'yapeplin');

        $this->publishes([
            __DIR__ . '/../Resources/views' => resource_path('views/vendor/yapeplin'),
        ], 'yapeplin-views');
    }

    /**
     * Register services.
     */
    public function register(): void
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/paymentmethods.php',
            'payment_methods'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php',
            'core'
        );
    }
}
