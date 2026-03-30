<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use App\Models\User;

test('guests cannot access admin orders', function () {
    $this->get(route('admin.orders.index'))
        ->assertRedirect(route('login'));
});

test('customers cannot access admin orders', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.orders.index'))
        ->assertForbidden();
});

test('admin can view orders management page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.orders.index'))
        ->assertOk();
});

test('admin can see all orders', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();
    $order = Order::factory()->for($customer)->create();

    $this->actingAs($admin)
        ->get(route('admin.orders.index'))
        ->assertSee('#'.$order->id);
});

test('admin can update order status', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();
    $order = Order::factory()->for($customer)->create(['status' => 'pending']);

    $this->actingAs($admin);

    Livewire\Livewire::test('pages::admin.orders.index')
        ->call('updateStatus', $order->id, 'processing');

    expect($order->fresh()->status)->toBe(OrderStatus::Processing);
});
