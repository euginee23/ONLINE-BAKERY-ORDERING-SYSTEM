<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Support\Collection;

class CartService
{
    private const SESSION_KEY = 'cart';

    /** @return array<int, array{product_id: int, quantity: int}> */
    private function getRaw(): array
    {
        return session()->get(self::SESSION_KEY, []);
    }

    private function save(array $cart): void
    {
        session()->put(self::SESSION_KEY, $cart);
    }

    public function add(int $productId, int $quantity = 1): void
    {
        $cart = $this->getRaw();

        if (isset($cart[$productId])) {
            $cart[$productId]['quantity'] += $quantity;
        } else {
            $cart[$productId] = [
                'product_id' => $productId,
                'quantity' => $quantity,
            ];
        }

        $this->save($cart);
    }

    public function remove(int $productId): void
    {
        $cart = $this->getRaw();
        unset($cart[$productId]);
        $this->save($cart);
    }

    public function update(int $productId, int $quantity): void
    {
        if ($quantity <= 0) {
            $this->remove($productId);

            return;
        }

        $cart = $this->getRaw();
        $cart[$productId] = [
            'product_id' => $productId,
            'quantity' => $quantity,
        ];
        $this->save($cart);
    }

    public function clear(): void
    {
        session()->forget(self::SESSION_KEY);
    }

    public function count(): int
    {
        return array_sum(array_column($this->getRaw(), 'quantity'));
    }

    public function isEmpty(): bool
    {
        return empty($this->getRaw());
    }

    /** @return Collection<int, array{product: Product, quantity: int, subtotal: float}> */
    public function items(): Collection
    {
        $raw = $this->getRaw();

        if (empty($raw)) {
            return collect();
        }

        $products = Product::whereIn('id', array_keys($raw))->with('category')->get()->keyBy('id');

        return collect($raw)->map(function (array $item) use ($products): array {
            $product = $products->get($item['product_id']);

            return [
                'product' => $product,
                'quantity' => $item['quantity'],
                'subtotal' => $product ? (float) $product->price * $item['quantity'] : 0.0,
            ];
        })->filter(fn (array $item) => $item['product'] !== null)->values();
    }

    public function total(): float
    {
        return (float) $this->items()->sum('subtotal');
    }
}
