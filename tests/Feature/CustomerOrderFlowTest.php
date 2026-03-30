<?php

use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;

test('guests cannot access checkout', function () {
    $this->get(route('customer.checkout'))
        ->assertRedirect(route('login'));
});

test('checkout redirects to menu when cart is empty', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire\Livewire::test('pages::customer.checkout')
        ->assertRedirect(route('customer.menu'));
});

test('customer can place a pickup order', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

    $cart = app(CartService::class);
    $cart->add($product->id, 2);

    $this->actingAs($user);

    Livewire\Livewire::test('pages::customer.checkout')
        ->set('type', 'pickup')
        ->call('placeOrder')
        ->assertRedirect();

    $this->assertDatabaseHas('orders', [
        'user_id' => $user->id,
        'type' => 'pickup',
        'total_amount' => 200.00,
    ]);

    expect(app(CartService::class)->isEmpty())->toBeTrue();
});

test('delivery order requires address', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

    $cart = app(CartService::class);
    $cart->add($product->id, 1);

    $this->actingAs($user);

    Livewire\Livewire::test('pages::customer.checkout')
        ->set('type', 'delivery')
        ->set('deliveryAddress', '')
        ->call('placeOrder')
        ->assertHasErrors(['deliveryAddress']);
});

test('customer can view their own order detail', function () {
    $user = User::factory()->create();
    $order = Order::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('customer.order-detail', $order))
        ->assertOk()
        ->assertSee('#'.$order->id);
});

test('customer cannot view another user order', function () {
    $user = User::factory()->create();
    $otherUser = User::factory()->create();
    $order = Order::factory()->for($otherUser)->create();

    $this->actingAs($user)
        ->get(route('customer.order-detail', $order))
        ->assertForbidden();
});

test('order history page shows customer orders', function () {
    $user = User::factory()->create();
    $order = Order::factory()->for($user)->create();

    $this->actingAs($user)
        ->get(route('customer.orders'))
        ->assertOk()
        ->assertSee('#'.$order->id);
});

test('placing order decrements product stock', function () {
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 50, 'stock' => 10]);

    $cart = app(CartService::class);
    $cart->add($product->id, 3);

    $this->actingAs($user);

    Livewire\Livewire::test('pages::customer.checkout')
        ->set('type', 'pickup')
        ->call('placeOrder');

    expect($product->fresh()->stock)->toBe(7);
});
