<?php

namespace Hnooz\LaravelCart;

use Hnooz\LaravelCart\Contracts\CartInterface;
use Hnooz\LaravelCart\Models\CartItem;
use Illuminate\Session\SessionManager;
use Illuminate\Support\Facades\Auth;

class CartManager implements CartInterface
{
    public function __construct(protected SessionManager $session, protected ?string $driver, protected ?string $sessionKey) {}

    public function add(string $id, string $name, float $price, int $quantity = 1, array $options = []): void
    {
        $item = [
            'id' => $id,
            'name' => $name,
            'price' => $price,
            'quantity' => $quantity,
            'options' => $options,
        ];

        if ($this->shouldUseSession()) {
            $this->addToSession($item);
        }

        if ($this->shouldUseDatabase()) {
            $this->addToDatabase($item);
        }
    }

    public function remove(string $id): void
    {
        if ($this->shouldUseSession()) {
            $this->removeFromSession($id);
        }

        if ($this->shouldUseDatabase()) {
            $this->removeFromDatabase($id);
        }
    }

    public function increase(string $id, int $quantity = 1): void
    {
        if ($this->shouldUseSession()) {
            $this->increaseInSession($id, $quantity);
        }

        if ($this->shouldUseDatabase()) {
            $this->increaseInDatabase($id, $quantity);
        }
    }

    public function decrease(string $id, int $quantity = 1): void
    {
        if ($this->shouldUseSession()) {
            $this->decreaseInSession($id, $quantity);
        }

        if ($this->shouldUseDatabase()) {
            $this->decreaseInDatabase($id, $quantity);
        }
    }

    public function clear(): void
    {
        if ($this->shouldUseSession()) {
            $this->session->forget($this->sessionKey);
        }

        if ($this->shouldUseDatabase()) {
            $this->clearDatabase();
        }
    }

    public function all(): array
    {
        if ($this->driver === 'session') {
            return $this->session->get($this->sessionKey, []);
        }

        if ($this->driver === 'database') {
            return $this->getDatabaseItems()->toArray();
        }

        // For 'both' driver, prefer database if user is authenticated
        if (Auth::check()) {
            return $this->getDatabaseItems()->toArray();
        }

        return $this->session->get($this->sessionKey, []);
    }

    public function count(): int
    {
        return array_sum(array_column($this->all(), 'quantity'));
    }

    public function total(): float
    {
        $items = $this->all();

        return array_sum(array_map(fn ($item) => $item['price'] * $item['quantity'], $items));
    }

    protected function shouldUseSession(): bool
    {
        return in_array($this->driver, ['session', 'both']);
    }

    protected function shouldUseDatabase(): bool
    {
        return in_array($this->driver, ['database', 'both']);
    }

    protected function addToSession(array $item): void
    {
        $cart = $this->session->get($this->sessionKey, []);

        if (isset($cart[$item['id']])) {
            $cart[$item['id']]['quantity'] += $item['quantity'];
        } else {
            $cart[$item['id']] = $item;
        }

        $this->session->put($this->sessionKey, $cart);
    }

    protected function addToDatabase(array $item): void
    {
        // Build the where clause based on authentication status
        $whereConditions = ['item_id' => $item['id']];

        if (Auth::check()) {
            // For authenticated users, use user_id
            $whereConditions['user_id'] = Auth::id();
        } else {
            // For guests, use session_id and null user_id
            $whereConditions['session_id'] = $this->session->getId();
            $whereConditions['user_id'] = null;
        }

        $existingItem = CartItem::where($whereConditions)->first();

        if ($existingItem) {
            // Update existing item - increase quantity
            $existingItem->increment('quantity', $item['quantity']);
            $existingItem->update([
                'name' => $item['name'],
                'price' => $item['price'],
                'options' => $item['options'],
            ]);
        } else {
            // Create new item
            CartItem::create([
                'session_id' => Auth::check() ? null : $this->session->getId(),
                'user_id' => Auth::id(),
                'item_id' => $item['id'],
                'name' => $item['name'],
                'price' => $item['price'],
                'quantity' => $item['quantity'],
                'options' => $item['options'],
            ]);
        }
    }

    protected function removeFromSession(string $id): void
    {
        $cart = $this->session->get($this->sessionKey, []);
        unset($cart[$id]);
        $this->session->put($this->sessionKey, $cart);
    }

    protected function removeFromDatabase(string $id): void
    {
        CartItem::where('session_id', $this->session->getId())
            ->where('user_id', Auth::id())
            ->where('item_id', $id)
            ->delete();
    }

    protected function increaseInSession(string $id, int $quantity): void
    {
        $cart = $this->session->get($this->sessionKey, []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] += $quantity;
            $this->session->put($this->sessionKey, $cart);
        }
    }

    protected function increaseInDatabase(string $id, int $quantity): void
    {
        CartItem::where('session_id', $this->session->getId())
            ->where('user_id', Auth::id())
            ->where('item_id', $id)
            ->increment('quantity', $quantity);
    }

    protected function decreaseInSession(string $id, int $quantity): void
    {
        $cart = $this->session->get($this->sessionKey, []);

        if (isset($cart[$id])) {
            $cart[$id]['quantity'] = max(1, $cart[$id]['quantity'] - $quantity);
            $this->session->put($this->sessionKey, $cart);
        }
    }

    protected function decreaseInDatabase(string $id, int $quantity): void
    {
        $item = CartItem::where('session_id', $this->session->getId())
            ->where('user_id', Auth::id())
            ->where('item_id', $id)
            ->first();

        if ($item) {
            $newQuantity = max(1, $item->quantity - $quantity);
            $item->update(['quantity' => $newQuantity]);
        }
    }

    protected function clearDatabase(): void
    {
        CartItem::where('session_id', $this->session->getId())
            ->where('user_id', Auth::id())
            ->delete();
    }

    protected function getDatabaseItems()
    {
        return CartItem::where('session_id', $this->session->getId())
            ->where('user_id', Auth::id())
            ->get()
            ->map(fn ($item) => [
                'id' => $item->item_id,
                'name' => $item->name,
                'price' => $item->price,
                'quantity' => $item->quantity,
                'options' => $item->options,
            ]);
    }
}
