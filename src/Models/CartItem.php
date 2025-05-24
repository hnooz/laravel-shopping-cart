<?php

namespace Hnooz\LaravelCart\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'session_id',
        'user_id',
        'item_id',
        'name',
        'price',
        'quantity',
        'options',
    ];

    protected function casts()
    {
        return [
            'options' => 'array',
            'price' => 'float',
            'quantity' => 'integer',
        ];
    }

    public function getTable(): string
    {
        return config('cart.table', 'cart_items');
    }

    public function getSubtotal(): float
    {
        return $this->price * $this->quantity;
    }
}
