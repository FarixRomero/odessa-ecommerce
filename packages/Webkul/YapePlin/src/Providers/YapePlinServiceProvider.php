<?php

namespace Webkul\YapePlin\Providers;

use Illuminate\Support\Facades\Event;
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

        // Listen to invoice creation to update order status
        Event::listen('sales.invoice.save.after', function ($invoice) {
            $order = $invoice->order;

            // If order is using Yape/Plin and is in pending_payment status
            if ($order->payment->method === 'yapeplin' && $order->status === 'pending_payment') {
                // Update order status to processing
                app('Webkul\Sales\Repositories\OrderRepository')->update(
                    ['status' => 'processing'],
                    $order->id
                );
            }
        });
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
