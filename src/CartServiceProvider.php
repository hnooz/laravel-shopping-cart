<?php

namespace Hnooz\Cart;

use Hnooz\Cart\Commands\CartCommand;
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
            ->hasViews()
            ->hasMigration('create_laravel_cart_table')
            ->hasCommand(CartCommand::class);
    }
}
