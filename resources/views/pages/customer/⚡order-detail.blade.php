<?php

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.customer')] class extends Component {
    public Order $order;

    public function mount(Order $order): void
    {
        abort_if($order->user_id !== auth()->id(), 403);

        $this->order = $order->load(['items.product']);
    }

    public function title(): string
    {
        return 'Order #' . $this->order->id;
    }
}

?>

<div class="flex flex-col gap-6">
    {{-- Back link --}}
    <a href="{{ route('customer.orders') }}" wire:navigate class="inline-flex items-center gap-2 text-sm font-medium text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition w-fit">
        <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7" />
        </svg>
        Back to My Orders
    </a>

    {{-- Order Header --}}
    <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-6">
        <div class="flex flex-col gap-4 sm:flex-row sm:items-start sm:justify-between">
            <div>
                <h1 class="text-2xl font-bold text-zinc-900 dark:text-white">Order #{{ $order->id }}</h1>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                    Placed on {{ $order->created_at->format('F d, Y \a\t h:i A') }}
                </p>
            </div>
            <div class="flex items-center gap-3">
                <span class="px-2.5 py-0.5 text-xs font-medium rounded-full border border-zinc-200 text-zinc-600 dark:border-zinc-600 dark:text-zinc-400">
                    {{ $order->type->label() }}
                </span>
                <span @class([
                    'px-3 py-1 text-sm font-semibold rounded-full',
                    'text-yellow-700 bg-yellow-100 dark:text-yellow-300 dark:bg-yellow-900/30' => $order->status->value === 'pending',
                    'text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-900/30' => $order->status->value === 'processing',
                    'text-green-700 bg-green-100 dark:text-green-300 dark:bg-green-900/30' => $order->status->value === 'ready',
                    'text-zinc-700 bg-zinc-100 dark:text-zinc-300 dark:bg-zinc-900/30' => $order->status->value === 'completed',
                    'text-red-700 bg-red-100 dark:text-red-300 dark:bg-red-900/30' => $order->status->value === 'cancelled',
                ])>
                    {{ $order->status->label() }}
                </span>
            </div>
        </div>

        {{-- Status Timeline --}}
        @if($order->status->value !== 'cancelled')
            <div class="mt-6">
                @php
                    $steps = [
                        ['key' => 'pending', 'label' => 'Order Placed', 'icon' => '📝'],
                        ['key' => 'processing', 'label' => 'Being Prepared', 'icon' => '👨‍🍳'],
                        ['key' => 'ready', 'label' => 'Ready', 'icon' => '✅'],
                        ['key' => 'completed', 'label' => 'Completed', 'icon' => '🎉'],
                    ];
                    $statusOrder = ['pending' => 0, 'processing' => 1, 'ready' => 2, 'completed' => 3];
                    $currentIndex = $statusOrder[$order->status->value] ?? 0;
                @endphp
                <div class="flex items-center gap-0">
                    @foreach($steps as $index => $step)
                        @php $isActive = $index <= $currentIndex; @endphp
                        <div class="flex items-center {{ $index < count($steps) - 1 ? 'flex-1' : '' }}">
                            <div class="flex flex-col items-center gap-1 {{ $index < count($steps) - 1 ? 'flex-shrink-0' : '' }}">
                                <div @class([
                                    'flex items-center justify-center size-10 rounded-full text-sm transition',
                                    'bg-gold-700 text-white dark:bg-gold-500 dark:text-zinc-900' => $isActive,
                                    'bg-zinc-100 text-zinc-400 dark:bg-zinc-700 dark:text-zinc-500' => !$isActive,
                                ])>
                                    {{ $step['icon'] }}
                                </div>
                                <span class="text-xs font-medium {{ $isActive ? 'text-zinc-800 dark:text-zinc-200' : 'text-zinc-400 dark:text-zinc-500' }} hidden sm:block">
                                    {{ $step['label'] }}
                                </span>
                            </div>
                            @if($index < count($steps) - 1)
                                <div @class([
                                    'flex-1 h-0.5 mx-2',
                                    'bg-gold-700 dark:bg-gold-500' => $index < $currentIndex,
                                    'bg-zinc-200 dark:bg-zinc-700' => $index >= $currentIndex,
                                ])></div>
                            @endif
                        </div>
                    @endforeach
                </div>
            </div>
        @else
            <div class="mt-4 inline-flex items-center gap-2 px-3 py-2 text-sm text-red-700 bg-red-50 rounded-lg dark:text-red-300 dark:bg-red-900/20">
                <svg class="size-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                This order has been cancelled.
            </div>
        @endif
    </div>

    <div class="grid grid-cols-1 gap-6 lg:grid-cols-3">
        {{-- Order Items --}}
        <div class="lg:col-span-2 bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="p-6 border-b border-zinc-200 dark:border-zinc-700">
                <h2 class="text-base font-semibold text-zinc-900 dark:text-white">Order Items</h2>
            </div>
            <div class="divide-y divide-zinc-200 dark:divide-zinc-700">
                @foreach($order->items as $item)
                    <div class="flex items-center gap-4 p-6">
                        @if($item->product->image)
                            <img
                                src="{{ Storage::url($item->product->image) }}"
                                alt="{{ $item->product->name }}"
                                class="object-cover rounded-lg size-16 flex-shrink-0"
                            />
                        @else
                            <div class="flex items-center justify-center rounded-lg size-16 flex-shrink-0 bg-amber-50 dark:bg-amber-900/20 text-2xl">
                                🍞
                            </div>
                        @endif
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-semibold text-zinc-900 dark:text-white truncate">{{ $item->product->name }}</p>
                            <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400">₱{{ number_format($item->unit_price, 2) }} each</p>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm text-zinc-600 dark:text-zinc-400">× {{ $item->quantity }}</span>
                            <span class="text-sm font-semibold text-zinc-900 dark:text-white min-w-[5rem] text-right">₱{{ number_format($item->subtotal, 2) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
            <div class="flex items-center justify-between p-6 bg-zinc-50 dark:bg-zinc-900/50 border-t border-zinc-200 dark:border-zinc-700">
                <span class="text-base font-semibold text-zinc-900 dark:text-white">Total</span>
                <span class="text-xl font-bold text-zinc-900 dark:text-white">₱{{ number_format($order->total_amount, 2) }}</span>
            </div>
        </div>

        {{-- Order Info Sidebar --}}
        <div class="flex flex-col gap-4">
            {{-- Delivery / Pickup --}}
            <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-6">
                <h2 class="text-base font-semibold text-zinc-900 dark:text-white mb-4">
                    {{ $order->type->value === 'delivery' ? 'Delivery Address' : 'Pickup Information' }}
                </h2>
                @if($order->type->value === 'delivery')
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $order->delivery_address }}</p>
                @else
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">Please pick up your order at our bakery. We'll update the status when it's ready!</p>
                @endif
            </div>

            {{-- Notes --}}
            @if($order->notes)
                <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-6">
                    <h2 class="text-base font-semibold text-zinc-900 dark:text-white mb-3">Order Notes</h2>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400">{{ $order->notes }}</p>
                </div>
            @endif

            {{-- Actions --}}
            <a
                href="{{ route('customer.menu') }}"
                wire:navigate
                class="w-full inline-flex items-center justify-center gap-2 px-4 py-2.5 text-sm font-semibold text-white transition rounded-xl bg-gold-700 hover:bg-gold-800 dark:bg-gold-500 dark:text-zinc-900 dark:hover:bg-gold-400"
            >
                Order Again
            </a>
        </div>
    </div>
</div>
