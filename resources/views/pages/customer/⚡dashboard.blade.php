<?php

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.customer'), Title('Dashboard')] class extends Component {
    public function with(): array
    {
        $userId = auth()->id();

        return [
            'totalOrders' => Order::forUser($userId)->count(),
            'pendingOrders' => Order::forUser($userId)->pending()->count(),
            'recentOrders' => Order::forUser($userId)->with(['items.product'])->latest()->limit(5)->get(),
        ];
    }
}

?>

<div class="flex flex-col gap-6">
    {{-- Welcome Header --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
        <h1 class="text-3xl font-bold bg-linear-to-r from-amber-600 to-orange-800 bg-clip-text text-transparent">
            Welcome back, {{ auth()->user()->name }}!
        </h1>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
            Here's an overview of your orders.
        </p>
    </div>

    {{-- Stat Cards --}}
    <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
        {{-- Total Orders --}}
        <div class="group relative bg-white dark:bg-zinc-800 rounded-2xl shadow-md border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="p-3 bg-linear-to-br from-amber-500 to-orange-600 rounded-xl shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <span class="px-3 py-1 text-xs font-semibold text-amber-700 bg-amber-100 dark:text-amber-300 dark:bg-amber-900/30 rounded-full">
                    All Time
                </span>
            </div>
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Orders</p>
            <p class="text-4xl font-bold text-zinc-900 dark:text-white">{{ $totalOrders }}</p>
        </div>

        {{-- Pending Orders --}}
        <div class="group relative bg-white dark:bg-zinc-800 rounded-2xl shadow-md border border-zinc-200 dark:border-zinc-700 p-6">
            <div class="flex items-start justify-between mb-4">
                <div class="p-3 bg-linear-to-br from-yellow-400 to-amber-500 rounded-xl shadow-md">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <span class="px-3 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 dark:text-yellow-300 dark:bg-yellow-900/30 rounded-full">
                    Pending
                </span>
            </div>
            <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Pending Orders</p>
            <p class="text-4xl font-bold text-zinc-900 dark:text-white">{{ $pendingOrders }}</p>
        </div>
    </div>

    {{-- Quick Actions --}}
    <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-md border border-zinc-200 dark:border-zinc-700 p-6">
        <h2 class="mb-4 text-sm font-semibold tracking-wider uppercase text-zinc-500 dark:text-zinc-400">
            Quick Actions
        </h2>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2">
            <a
                href="{{ route('customer.menu') }}"
                wire:navigate
                class="flex items-center gap-3 p-4 transition border rounded-xl border-amber-200 bg-amber-50 hover:bg-amber-100 dark:border-amber-800 dark:bg-amber-900/20 dark:hover:bg-amber-900/30"
            >
                <div class="flex items-center justify-center rounded-lg size-10 bg-gold-700 dark:bg-gold-500">
                    <svg class="size-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-zinc-900 dark:text-white">Browse Menu</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">View all available products</p>
                </div>
            </a>
            <a
                href="{{ route('customer.orders') }}"
                wire:navigate
                class="flex items-center gap-3 p-4 transition border rounded-xl border-zinc-200 bg-zinc-50 hover:bg-zinc-100 dark:border-zinc-700 dark:bg-zinc-900 dark:hover:bg-zinc-700"
            >
                <div class="flex items-center justify-center rounded-lg size-10 bg-zinc-600 dark:bg-zinc-500">
                    <svg class="size-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <div>
                    <p class="text-sm font-semibold text-zinc-900 dark:text-white">My Orders</p>
                    <p class="text-xs text-zinc-500 dark:text-zinc-400">Track your order history</p>
                </div>
            </a>
        </div>
    </div>

    {{-- Recent Orders --}}
    @if($recentOrders->isNotEmpty())
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-md border border-zinc-200 dark:border-zinc-700">
            <div class="flex items-center justify-between px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <h2 class="text-sm font-semibold tracking-wider uppercase text-zinc-500 dark:text-zinc-400">
                    Recent Orders
                </h2>
                <a href="{{ route('customer.orders') }}" wire:navigate class="text-sm font-medium text-gold-700 dark:text-gold-400 hover:underline">
                    View All
                </a>
            </div>
            <div class="divide-y divide-zinc-100 dark:divide-zinc-700">
                @foreach($recentOrders as $order)
                    <div class="flex items-center justify-between px-6 py-4">
                        <div class="flex items-center gap-3">
                            <div class="flex items-center justify-center rounded-lg size-9 bg-zinc-100 dark:bg-zinc-700">
                                <svg class="size-4 text-zinc-500 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div>
                                <p class="text-sm font-semibold text-zinc-900 dark:text-white">Order #{{ $order->id }}</p>
                                <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $order->items->count() }} item(s) · {{ $order->type->label() }}</p>
                            </div>
                        </div>
                        <div class="flex items-center gap-3">
                            <span @class([
                                'px-2.5 py-0.5 text-xs font-semibold rounded-full',
                                'text-yellow-700 bg-yellow-100 dark:text-yellow-300 dark:bg-yellow-900/30' => $order->status->value === 'pending',
                                'text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-900/30' => $order->status->value === 'processing',
                                'text-green-700 bg-green-100 dark:text-green-300 dark:bg-green-900/30' => $order->status->value === 'ready',
                                'text-zinc-700 bg-zinc-100 dark:text-zinc-300 dark:bg-zinc-900/30' => $order->status->value === 'completed',
                                'text-red-700 bg-red-100 dark:text-red-300 dark:bg-red-900/30' => $order->status->value === 'cancelled',
                            ])>
                                {{ $order->status->label() }}
                            </span>
                            <span class="text-sm font-semibold text-zinc-900 dark:text-white">
                                ₱{{ number_format($order->total_amount, 2) }}
                            </span>
                            <a href="{{ route('customer.order-detail', $order) }}" wire:navigate class="text-xs text-gold-700 dark:text-gold-400 hover:underline">
                                View
                            </a>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    @endif
</div>
