<?php

namespace Hnooz\Cart\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity',
        'price',
        'options',
    ];

    protected function casts()
    {
        return [
            'options' => 'array',
        ];
    }

    public function cart()
    {
        return $this->belongsTo(Cart::class);
    }
}
