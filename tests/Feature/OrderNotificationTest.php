<?php

use App\Enums\OrderStatus;
use App\Mail\NewOrderAlert;
use App\Mail\OrderPlaced;
use App\Mail\OrderStatusUpdated;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\CartService;
use Illuminate\Support\Facades\Mail;

test('placing order sends OrderPlaced email to customer', function () {
    Mail::fake();

    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

    app(CartService::class)->add($product->id, 1);

    $this->actingAs($user);

    Livewire\Livewire::test('pages::customer.checkout')
        ->set('type', 'pickup')
        ->call('placeOrder')
        ->assertRedirect();

    Mail::assertSent(OrderPlaced::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

test('placing order sends NewOrderAlert email to admin', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $user = User::factory()->create();
    $product = Product::factory()->create(['price' => 100, 'stock' => 10]);

    app(CartService::class)->add($product->id, 1);

    $this->actingAs($user);

    Livewire\Livewire::test('pages::customer.checkout')
        ->set('type', 'pickup')
        ->call('placeOrder')
        ->assertRedirect();

    Mail::assertSent(NewOrderAlert::class, function ($mail) use ($admin) {
        return $mail->hasTo($admin->email);
    });
});

test('updating order status sends OrderStatusUpdated email to customer', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();
    $order = Order::factory()->for($customer)->create(['status' => 'pending']);

    $this->actingAs($admin);

    Livewire\Livewire::test('pages::admin.orders.index')
        ->call('updateStatus', $order->id, 'processing');

    Mail::assertSent(OrderStatusUpdated::class, function ($mail) use ($customer) {
        return $mail->hasTo($customer->email);
    });
});

test('OrderStatusUpdated email contains previous and new status', function () {
    Mail::fake();

    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();
    $order = Order::factory()->for($customer)->create(['status' => 'pending']);

    $this->actingAs($admin);

    Livewire\Livewire::test('pages::admin.orders.index')
        ->call('updateStatus', $order->id, 'processing');

    Mail::assertSent(OrderStatusUpdated::class, function (OrderStatusUpdated $mail) {
        return $mail->previousStatus === OrderStatus::Pending
            && $mail->order->status === OrderStatus::Processing;
    });
});
