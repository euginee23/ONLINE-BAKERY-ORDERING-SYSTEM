<?php

use App\Models\Product;
use App\Services\CartService;

beforeEach(function () {
    $this->cart = app(CartService::class);
});

test('cart is empty by default', function () {
    expect($this->cart->isEmpty())->toBeTrue();
    expect($this->cart->count())->toBe(0);
});

test('can add a product to cart', function () {
    $product = Product::factory()->create(['price' => 50, 'stock' => 10]);

    $this->cart->add($product->id);

    expect($this->cart->count())->toBe(1);
    expect($this->cart->isEmpty())->toBeFalse();
});

test('adding same product increments quantity', function () {
    $product = Product::factory()->create(['price' => 50, 'stock' => 10]);

    $this->cart->add($product->id, 1);
    $this->cart->add($product->id, 2);

    $items = $this->cart->items();
    expect($items->first()['quantity'])->toBe(3);
});

test('can remove a product from cart', function () {
    $product = Product::factory()->create(['stock' => 10]);

    $this->cart->add($product->id);
    $this->cart->remove($product->id);

    expect($this->cart->isEmpty())->toBeTrue();
});

test('can update product quantity', function () {
    $product = Product::factory()->create(['stock' => 10]);

    $this->cart->add($product->id);
    $this->cart->update($product->id, 5);

    $items = $this->cart->items();
    expect($items->first()['quantity'])->toBe(5);
});

test('updating to zero removes the item', function () {
    $product = Product::factory()->create(['stock' => 10]);

    $this->cart->add($product->id);
    $this->cart->update($product->id, 0);

    expect($this->cart->isEmpty())->toBeTrue();
});

test('can clear cart', function () {
    $product = Product::factory()->create(['stock' => 10]);

    $this->cart->add($product->id, 3);
    $this->cart->clear();

    expect($this->cart->isEmpty())->toBeTrue();
});

test('cart total is calculated correctly', function () {
    $productA = Product::factory()->create(['price' => 100, 'stock' => 10]);
    $productB = Product::factory()->create(['price' => 50, 'stock' => 10]);

    $this->cart->add($productA->id, 2);
    $this->cart->add($productB->id, 3);

    expect($this->cart->total())->toBe(350.0);
});

test('items collection contains product model and subtotal', function () {
    $product = Product::factory()->create(['price' => 75, 'stock' => 10]);

    $this->cart->add($product->id, 2);

    $item = $this->cart->items()->first();
    expect($item['product']->id)->toBe($product->id)
        ->and($item['quantity'])->toBe(2)
        ->and($item['subtotal'])->toBe(150.0);
});
