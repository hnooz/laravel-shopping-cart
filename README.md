# Laravel Cart

[![Latest Version on Packagist](https://img.shields.io/packagist/v/hnooz/laravel-cart.svg?style=flat-square)](https://packagist.org/packages/hnooz/laravel-cart)
[![GitHub Tests Action Status](https://img.shields.io/github/actions/workflow/status/hnooz/laravel-cart/run-tests.yml?branch=main&label=tests&style=flat-square)](https://github.com/hnooz/laravel-cart/actions?query=workflow%3Arun-tests+branch%3Amain)
[![GitHub Code Style Action Status](https://img.shields.io/github/actions/workflow/status/hnooz/laravel-cart/fix-php-code-style-issues.yml?branch=main&label=code%20style&style=flat-square)](https://github.com/hnooz/laravel-cart/actions?query=workflow%3A"Fix+PHP+code+style+issues"+branch%3Amain)
[![Total Downloads](https://img.shields.io/packagist/dt/hnooz/laravel-cart.svg?style=flat-square)](https://packagist.org/packages/hnooz/laravel-cart)

---

**Laravel Cart** is a lightweight and flexible package for managing shopping carts in Laravel applications. It supports storing cart data both in the session and database, automatically syncing between them. Perfect for authenticated user-based carts that persist across sessions and support easy retrieval.

```php
Cart::addItem('product-id-123', 2);
$cart = Cart::getCart();
Cart::clear();
```

---

## Installation

Install via Composer:

```bash
composer require hnooz/laravel-cart
```

Publish and run the migrations:

```bash
php artisan vendor:publish --tag="laravel-cart-migrations"
php artisan migrate
```

---

## Usage

Ensure the user is authenticated (`auth()->user()` is required).

```php
use Hnooz\Cart\Facades\Cart;

// Add item to cart
Cart::addItem('product-123', 2);

// Get current cart
$cart = Cart::getCart();

// Clear cart
Cart::clear();
```

Returned cart structure:

```php
[
    'id' => 1,
    'items' => [
        [
            'product_id' => 'product-123',
            'quantity' => 2,
        ],
        // ...
    ]
]
```

---

## Configuration (Optional)

Publish the config file:

```bash
php artisan vendor:publish --tag="laravel-cart-config"
```

Contents of the published config file:

```php
return [

];
```

Publish the views (if needed):

```bash
php artisan vendor:publish --tag="laravel-cart-views"
```

---

## Requirements

- PHP 8.1+
- Laravel 10 or later

---

## Testing

Make sure [Pest](https://pestphp.com) is installed:

```bash
composer require pestphp/pest --dev
./vendor/bin/pest --init
```

Run tests:

```bash
composer test
```

Example Pest test:

```php
use Hnooz\Cart\Facades\Cart;
use Hnooz\Cart\Models\Cart as CartModel;
use App\Models\User;

it('adds item to cart and syncs database', function () {
    $user = User::factory()->create();
    $this->actingAs($user);

    Cart::addItem('product-123', 2);
    $cartData = Cart::getCart();

    expect($cartData['items'])->toHaveCount(1)
        ->and($cartData['items'][0]['product_id'])->toBe('product-123');

    $dbCart = CartModel::where('user_id', $user->id)->first();
    expect($dbCart->items()->count())->toBe(1);
});
```

---

## Folder Structure

```
src/
├── CartServiceProvider.php
├── Facades/
│   └── Cart.php
├── Models/
│   ├── Cart.php
│   └── CartItem.php
└── Services/
    └── CartManager.php
```

---

## Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information on what has changed recently.

---

## Contributing

Please see [CONTRIBUTING](CONTRIBUTING.md) for details.

---

## Security Vulnerabilities

Please review [our security policy](../../security/policy) on how to report security vulnerabilities.

---

## Credits

- [Hnooz](https://github.com/hnooz)
- [All Contributors](../../contributors)

---

## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.
