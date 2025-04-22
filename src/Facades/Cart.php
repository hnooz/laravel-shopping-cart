<?php

namespace Hnooz\Cart\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Hnooz\Cart\Cart
 */
class Cart extends Facade
{
    protected static function getFacadeAccessor(): string
    {
        return \Hnooz\Cart\Cart::class;
    }
}
