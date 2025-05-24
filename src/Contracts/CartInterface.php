<?php

namespace Hnooz\LaravelCart\Contracts;

/**
 * Interface CartInterface
 *
 * This interface defines the contract for a shopping cart system.
 * It provides methods for adding, removing, and managing items in the cart,
 * as well as retrieving cart details such as total count and price.
 */
interface CartInterface
{
    /**
     * Add an item to the cart.
     *
     * @param  string  $id  The unique identifier of the item.
     * @param  string  $name  The name of the item.
     * @param  float  $price  The price of the item.
     * @param  int  $quantity  The quantity of the item to add (default is 1).
     * @param  array  $options  Additional options or attributes for the item (default is an empty array).
     */
    public function add(string $id, string $name, float $price, int $quantity = 1, array $options = []): void;

    /**
     * Remove an item from the cart.
     *
     * @param  string  $id  The unique identifier of the item to remove.
     */
    public function remove(string $id): void;

    /**
     * Increase the quantity of an item in the cart.
     *
     * @param  string  $id  The unique identifier of the item.
     * @param  int  $quantity  The quantity to increase by (default is 1).
     */
    public function increase(string $id, int $quantity = 1): void;

    /**
     * Decrease the quantity of an item in the cart.
     *
     * @param  string  $id  The unique identifier of the item.
     * @param  int  $quantity  The quantity to decrease by (default is 1).
     */
    public function decrease(string $id, int $quantity = 1): void;

    /**
     * Clear all items from the cart.
     */
    public function clear(): void;

    /**
     * Retrieve all items in the cart.
     *
     * @return array An array of all items in the cart.
     */
    public function all(): array;

    /**
     * Get the total count of items in the cart.
     *
     * @return int The total number of items in the cart.
     */
    public function count(): int;

    /**
     * Get the total price of all items in the cart.
     *
     * @return float The total price of all items in the cart.
     */
    public function total(): float;
}
