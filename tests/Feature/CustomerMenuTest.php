<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;

test('guests cannot access menu page', function () {
    $this->get(route('customer.menu'))
        ->assertRedirect(route('login'));
});

test('authenticated customers can view menu page', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('customer.menu'))
        ->assertOk();
});

test('menu page displays available products', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['is_available' => true, 'stock' => 5]);

    $this->actingAs($user)
        ->get(route('customer.menu'))
        ->assertSee($product->name);
});

test('adding in-stock product to cart succeeds', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['is_available' => true, 'stock' => 5]);

    $this->actingAs($user);

    Livewire\Livewire::test('pages::customer.menu')
        ->call('addToCart', $product->id)
        ->assertDispatched('notify');

    expect(app(CartService::class)->count())->toBe(1);
});

test('adding out-of-stock product to cart fails', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['is_available' => true, 'stock' => 0]);

    $this->actingAs($user);

    Livewire\Livewire::test('pages::customer.menu')
        ->call('addToCart', $product->id);

    expect(app(CartService::class)->count())->toBe(0);
});

test('menu can be filtered by category', function () {
    $user = User::factory()->create();
    $category = Category::factory()->create();
    $productInCategory = Product::factory()->create(['category_id' => $category->id, 'is_available' => true, 'stock' => 5]);
    $otherProduct = Product::factory()->create(['is_available' => true, 'stock' => 5]);

    $this->actingAs($user);

    Livewire\Livewire::test('pages::customer.menu')
        ->set('categoryFilter', $category->id)
        ->assertSee($productInCategory->name)
        ->assertDontSee($otherProduct->name);
});
