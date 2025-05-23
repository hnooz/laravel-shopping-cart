<?php
namespace Hnooz\Cart\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Cart extends Model
{
    protected $fillable = [
        'user_id',
        'session_id',
        'total_price',
        'total_quantity',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}