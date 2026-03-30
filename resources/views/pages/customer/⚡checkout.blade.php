<?php

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Models\Order;
use App\Models\OrderItem;
use App\Services\CartService;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.customer'), Title('Checkout')] class extends Component {
    public string $type = 'pickup';

    public string $deliveryAddress = '';

    public string $notes = '';

    public function mount(): void
    {
        if (app(CartService::class)->isEmpty()) {
            $this->redirect(route('customer.menu'), navigate: true);
        }
    }

    protected function rules(): array
    {
        return [
            'type' => ['required', 'in:delivery,pickup'],
            'deliveryAddress' => [$this->type === 'delivery' ? 'required' : 'nullable', 'string', 'max:500'],
            'notes' => ['nullable', 'string', 'max:500'],
        ];
    }

    protected function messages(): array
    {
        return [
            'deliveryAddress.required' => 'Please enter a delivery address.',
        ];
    }

    public function placeOrder(): void
    {
        $this->validate();

        $cart = app(CartService::class);

        if ($cart->isEmpty()) {
            $this->dispatch('notify', type: 'error', message: 'Your cart is empty.');

            return;
        }

        $items = $cart->items();

        foreach ($items as $item) {
            if (! $item['product']->isInStock() || $item['product']->stock < $item['quantity']) {
                $this->dispatch('notify', type: 'error', message: "{$item['product']->name} does not have enough stock.");

                return;
            }
        }

        $order = Order::create([
            'user_id' => auth()->id(),
            'status' => OrderStatus::Pending,
            'type' => OrderType::from($this->type),
            'delivery_address' => $this->type === 'delivery' ? $this->deliveryAddress : null,
            'notes' => $this->notes ?: null,
            'total_amount' => $cart->total(),
        ]);

        foreach ($items as $item) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $item['product']->id,
                'quantity' => $item['quantity'],
                'unit_price' => $item['product']->price,
                'subtotal' => $item['subtotal'],
            ]);

            $item['product']->decrement('stock', $item['quantity']);
        }

        $cart->clear();

        $this->redirect(route('customer.order-detail', $order), navigate: true);
    }

    public function removeFromCart(int $productId): void
    {
        app(CartService::class)->remove($productId);
        $this->dispatch('cart-updated');
    }

    public function updateQuantity(int $productId, int $quantity): void
    {
        app(CartService::class)->update($productId, $quantity);
        $this->dispatch('cart-updated');
    }

    public function with(): array
    {
        $cart = app(CartService::class);

        return [
            'cartItems' => $cart->items(),
            'cartTotal' => $cart->total(),
        ];
    }
}

?>

<div class="max-w-4xl mx-auto flex flex-col gap-6">
    <h1 class="text-3xl font-bold text-zinc-900 dark:text-white">Checkout</h1>

    @if($cartItems->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700">
            <div class="mb-4 text-6xl">🛒</div>
            <h3 class="text-lg font-semibold text-zinc-700 dark:text-zinc-300">Your cart is empty</h3>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Add some delicious baked goods first!</p>
            <a href="{{ route('customer.menu') }}" wire:navigate class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition rounded-lg bg-gold-700 hover:bg-gold-800 dark:bg-gold-500 dark:text-zinc-900">
                Browse Menu
            </a>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 lg:grid-cols-5">

            {{-- Order Form --}}
            <div class="lg:col-span-3 flex flex-col gap-6">
                {{-- Order Type --}}
                <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h2 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider">Order Type</h2>

                    <div class="grid grid-cols-2 gap-3">
                        <label @class([
                            'flex flex-col items-center gap-2 p-4 cursor-pointer rounded-xl border-2 transition',
                            'border-gold-600 bg-gold-50 dark:border-gold-500 dark:bg-gold-900/20' => $type === 'pickup',
                            'border-zinc-200 dark:border-zinc-600 hover:border-zinc-300 dark:hover:border-zinc-500' => $type !== 'pickup',
                        ])>
                            <input type="radio" wire:model.live="type" value="pickup" class="sr-only" />
                            <svg class="size-6 @if($type === 'pickup') text-gold-700 dark:text-gold-400 @else text-zinc-400 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                            </svg>
                            <span class="text-sm font-semibold @if($type === 'pickup') text-gold-700 dark:text-gold-400 @else text-zinc-600 dark:text-zinc-400 @endif">Store Pickup</span>
                        </label>

                        <label @class([
                            'flex flex-col items-center gap-2 p-4 cursor-pointer rounded-xl border-2 transition',
                            'border-gold-600 bg-gold-50 dark:border-gold-500 dark:bg-gold-900/20' => $type === 'delivery',
                            'border-zinc-200 dark:border-zinc-600 hover:border-zinc-300 dark:hover:border-zinc-500' => $type !== 'delivery',
                        ])>
                            <input type="radio" wire:model.live="type" value="delivery" class="sr-only" />
                            <svg class="size-6 @if($type === 'delivery') text-gold-700 dark:text-gold-400 @else text-zinc-400 @endif" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4" />
                            </svg>
                            <span class="text-sm font-semibold @if($type === 'delivery') text-gold-700 dark:text-gold-400 @else text-zinc-600 dark:text-zinc-400 @endif">Delivery</span>
                        </label>
                    </div>

                    @if($type === 'delivery')
                        <div class="mt-4">
                            <label class="block mb-1 text-sm font-medium text-zinc-700 dark:text-zinc-300">
                                Delivery Address <span class="text-red-500">*</span>
                            </label>
                            <textarea
                                wire:model="deliveryAddress"
                                rows="3"
                                placeholder="Enter your full delivery address..."
                                class="w-full px-3 py-2 text-sm border rounded-lg border-zinc-300 bg-white text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-gold-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500"
                            ></textarea>
                            @error('deliveryAddress')
                                <p class="mt-1 text-xs text-red-600 dark:text-red-400">{{ $message }}</p>
                            @enderror
                        </div>
                    @endif
                </div>

                {{-- Notes --}}
                <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h2 class="mb-4 text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider">Order Notes (optional)</h2>
                    <textarea
                        wire:model="notes"
                        rows="3"
                        placeholder="Any special instructions or requests..."
                        class="w-full px-3 py-2 text-sm border rounded-lg border-zinc-300 bg-white text-zinc-900 placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-gold-500 dark:border-zinc-600 dark:bg-zinc-800 dark:text-white dark:placeholder-zinc-500"
                    ></textarea>
                </div>
            </div>

            {{-- Cart Summary --}}
            <div class="lg:col-span-2 flex flex-col gap-4">
                <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <div class="px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                        <h2 class="text-sm font-semibold text-zinc-900 dark:text-white uppercase tracking-wider">Your Cart</h2>
                    </div>

                    <div class="divide-y divide-zinc-100 dark:divide-zinc-700">
                        @foreach($cartItems as $item)
                            <div class="flex items-center gap-3 px-6 py-3">
                                <div class="flex-1 min-w-0">
                                    <p class="text-sm font-medium text-zinc-900 dark:text-white truncate">{{ $item['product']->name }}</p>
                                    <p class="text-xs text-zinc-500 dark:text-zinc-400">₱{{ number_format($item['product']->price, 2) }} each</p>
                                </div>

                                <div class="flex items-center gap-2">
                                    <button
                                        wire:click="updateQuantity({{ $item['product']->id }}, {{ $item['quantity'] - 1 }})"
                                        class="flex items-center justify-center rounded-lg size-6 bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-600 transition"
                                    >
                                        <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4" /></svg>
                                    </button>
                                    <span class="text-sm font-semibold text-zinc-900 dark:text-white w-6 text-center">{{ $item['quantity'] }}</span>
                                    <button
                                        wire:click="updateQuantity({{ $item['product']->id }}, {{ $item['quantity'] + 1 }})"
                                        class="flex items-center justify-center rounded-lg size-6 bg-zinc-100 text-zinc-600 hover:bg-zinc-200 dark:bg-zinc-700 dark:text-zinc-300 dark:hover:bg-zinc-600 transition"
                                    >
                                        <svg class="size-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                    </button>
                                </div>

                                <div class="flex items-center gap-2">
                                    <span class="text-sm font-semibold text-zinc-900 dark:text-white">₱{{ number_format($item['subtotal'], 2) }}</span>
                                    <button wire:click="removeFromCart({{ $item['product']->id }})" class="text-zinc-400 hover:text-red-500 transition">
                                        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <div class="px-6 py-4 border-t border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900">
                        <div class="flex items-center justify-between mb-4">
                            <span class="text-base font-bold text-zinc-900 dark:text-white">Total</span>
                            <span class="text-xl font-bold text-gold-700 dark:text-gold-400">₱{{ number_format($cartTotal, 2) }}</span>
                        </div>

                        <button
                            wire:click="placeOrder"
                            wire:loading.attr="disabled"
                            class="w-full flex items-center justify-center gap-2 px-4 py-3 text-sm font-semibold text-white transition rounded-xl bg-gold-700 hover:bg-gold-800 dark:bg-gold-500 dark:text-zinc-900 dark:hover:bg-gold-400 disabled:opacity-60 disabled:cursor-not-allowed"
                        >
                            <span wire:loading.remove wire:target="placeOrder">Place Order</span>
                            <span wire:loading wire:target="placeOrder">Placing Order...</span>
                        </button>
                    </div>
                </div>

                <a href="{{ route('customer.menu') }}" wire:navigate class="text-sm text-center text-zinc-500 hover:text-zinc-700 dark:text-zinc-400 dark:hover:text-zinc-200">
                    ← Continue Shopping
                </a>
            </div>
        </div>
    @endif
</div>
