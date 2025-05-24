<?php

namespace Hnooz\LaravelCart;

use Hnooz\LaravelCart\Contracts\CartInterface;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;

class CartServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('laravel-cart')
            ->hasConfigFile()
            ->hasMigration('create_cart_items_table');
    }

    public function packageRegistered(): void
    {
        $this->app->singleton('cart', function ($app) {
            $driver = config('cart.driver', 'both');
            $sessionKey = config('cart.session_key', 'shopping_cart');

            return new CartManager(session: $app['session'], driver: $driver, sessionKey: $sessionKey);
        });

        $this->app->bind(CartInterface::class, CartManager::class);
    }

    public function packageBooted(): void
    {
        // Publish config file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                $this->package->basePath('/../config/cart.php') => config_path('cart.php'),
            ], "{$this->package->name}-config");

            // Publish migrations
            $this->publishes([
                $this->package->basePath('/../database/migrations') => database_path('migrations'),
            ], "{$this->package->name}-migrations");
        }
    }
}
