<?php

use Hnooz\LaravelCart\Facades\Cart;

beforeEach(function () {
    Cart::clear();
});

it('can add items to cart', function () {
    Cart::add('1', 'Product 1', 10.99, 2);

    expect(Cart::count())->toBe(2);
    expect(Cart::total())->toBe(21.98);
});

it('can remove items from cart', function () {
    Cart::add('1', 'Product 1', 10.99, 2);
    Cart::add('2', 'Product 2', 5.99, 1);

    Cart::remove('1');

    expect(Cart::count())->toBe(1);
    expect(Cart::total())->toBe(5.99);
});

it('can increase item quantity', function () {
    Cart::add('1', 'Product 1', 10.99, 1);
    Cart::increase('1', 2);

    expect(Cart::count())->toBe(3);
});

it('can decrease item quantity', function () {
    Cart::add('1', 'Product 1', 10.99, 5);
    Cart::decrease('1', 2);

    expect(Cart::count())->toBe(3);
});

it('can clear all items', function () {
    Cart::add('1', 'Product 1', 10.99, 2);
    Cart::add('2', 'Product 2', 5.99, 1);

    Cart::clear();

    expect(Cart::count())->toBe(0);
    expect(Cart::total())->toBe(0.0);
});

it('can get all items', function () {
    Cart::add('1', 'Product 1', 10.99, 2);
    Cart::add('2', 'Product 2', 5.99, 1);

    expect(count(Cart::all()))->toBe(2);
});
