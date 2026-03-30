<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;

new #[Layout('layouts.admin'), Title('Admin Dashboard')] class extends Component {
    public function with(): array
    {
        return [
            'totalProducts' => Product::count(),
            'totalCategories' => Category::count(),
            'availableProducts' => Product::where('is_available', true)->count(),
            'outOfStock' => Product::where('stock', 0)->count(),
            'lowStock' => Product::where('stock', '>', 0)->where('stock', '<=', 10)->count(),
            'totalCustomers' => User::where('role', 'customer')->count(),
            'recentProducts' => Product::with('category')->latest()->limit(5)->get(),
            'totalOrders' => Order::count(),
            'pendingOrders' => Order::where('status', 'pending')->count(),
            'processingOrders' => Order::where('status', 'processing')->count(),
            'recentOrders' => Order::with(['user', 'items'])->latest()->limit(5)->get(),
        ];
    }
}

?>

<div class="flex h-full w-full flex-1 flex-col gap-6">
    {{-- Header --}}
    <div class="bg-white dark:bg-zinc-800 rounded-xl shadow-sm border border-zinc-200 dark:border-zinc-700 p-6">
        <h1 class="text-3xl font-bold bg-linear-to-r from-amber-600 to-orange-800 bg-clip-text text-transparent">
            {{ __('Admin Dashboard') }}
        </h1>
        <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400">
            {{ __('Welcome back! Here\'s an overview of your bakery operations.') }}
        </p>
    </div>

    {{-- Stat Cards Row 1: Catalog --}}
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
        {{-- Total Products --}}
        <div class="group relative bg-white dark:bg-zinc-800 rounded-2xl shadow-md hover:shadow-xl transition-all border border-zinc-200 dark:border-zinc-700">
            <div class="absolute inset-0 bg-linear-to-br from-amber-500/5 to-transparent opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity"></div>
            <div class="relative p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-linear-to-br from-amber-500 to-orange-600 rounded-xl shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold text-amber-700 bg-amber-100 dark:text-amber-300 dark:bg-amber-900/30 rounded-full">
                        {{ __('Inventory') }}
                    </span>
                </div>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Total Products') }}</p>
                <p class="text-4xl font-bold text-zinc-900 dark:text-white">{{ $totalProducts }}</p>
                <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ __('items in catalog') }}</p>
            </div>
        </div>

        {{-- Total Categories --}}
        <div class="group relative bg-white dark:bg-zinc-800 rounded-2xl shadow-md hover:shadow-xl transition-all border border-zinc-200 dark:border-zinc-700">
            <div class="absolute inset-0 bg-linear-to-br from-orange-500/5 to-transparent opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity"></div>
            <div class="relative p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-linear-to-br from-orange-500 to-red-500 rounded-xl shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold text-orange-700 bg-orange-100 dark:text-orange-300 dark:bg-orange-900/30 rounded-full">
                        {{ __('Groups') }}
                    </span>
                </div>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Categories') }}</p>
                <p class="text-4xl font-bold text-zinc-900 dark:text-white">{{ $totalCategories }}</p>
                <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ __('product categories') }}</p>
            </div>
        </div>

        {{-- Available Products --}}
        <div class="group relative bg-white dark:bg-zinc-800 rounded-2xl shadow-md hover:shadow-xl transition-all border border-zinc-200 dark:border-zinc-700">
            <div class="absolute inset-0 bg-linear-to-br from-emerald-500/5 to-transparent opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity"></div>
            <div class="relative p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-linear-to-br from-emerald-500 to-green-600 rounded-xl shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold text-emerald-700 bg-emerald-100 dark:text-emerald-300 dark:bg-emerald-900/30 rounded-full">
                        {{ __('Active') }}
                    </span>
                </div>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Available') }}</p>
                <p class="text-4xl font-bold text-zinc-900 dark:text-white">{{ $availableProducts }}</p>
                <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ __('ready for ordering') }}</p>
            </div>
        </div>

        {{-- Out of Stock --}}
        <div class="group relative bg-white dark:bg-zinc-800 rounded-2xl shadow-md hover:shadow-xl transition-all border border-zinc-200 dark:border-zinc-700">
            <div class="absolute inset-0 bg-linear-to-br from-red-500/5 to-transparent opacity-0 group-hover:opacity-100 rounded-2xl transition-opacity"></div>
            <div class="relative p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-linear-to-br from-red-500 to-rose-600 rounded-xl shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold text-red-700 bg-red-100 dark:text-red-300 dark:bg-red-900/30 rounded-full">
                        {{ __('Alert') }}
                    </span>
                </div>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ __('Out of Stock') }}</p>
                <p class="text-4xl font-bold text-zinc-900 dark:text-white">{{ $outOfStock }}</p>
                <p class="text-xs text-zinc-400 dark:text-zinc-500">{{ __('items need restocking') }}</p>
            </div>
        </div>
    </div>

    {{-- Quick Actions & Recent Products --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    {{-- Stat Cards Row 2: Orders --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        {{-- Total Orders --}}
        <div class="group relative bg-white dark:bg-zinc-800 rounded-2xl shadow-md hover:shadow-xl transition-all border border-zinc-200 dark:border-zinc-700">
            <div class="relative p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-linear-to-br from-violet-500 to-purple-600 rounded-xl shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold text-violet-700 bg-violet-100 dark:text-violet-300 dark:bg-violet-900/30 rounded-full">All Time</span>
                </div>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Total Orders</p>
                <p class="text-4xl font-bold text-zinc-900 dark:text-white">{{ $totalOrders }}</p>
                <a href="{{ route('admin.orders.index') }}" wire:navigate class="text-xs text-violet-600 dark:text-violet-400 hover:underline">View all orders →</a>
            </div>
        </div>

        {{-- Pending Orders --}}
        <div class="group relative bg-white dark:bg-zinc-800 rounded-2xl shadow-md hover:shadow-xl transition-all border border-zinc-200 dark:border-zinc-700">
            <div class="relative p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-linear-to-br from-yellow-400 to-orange-500 rounded-xl shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold text-yellow-700 bg-yellow-100 dark:text-yellow-300 dark:bg-yellow-900/30 rounded-full">Action Needed</span>
                </div>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Pending Orders</p>
                <p class="text-4xl font-bold text-zinc-900 dark:text-white">{{ $pendingOrders }}</p>
                <a href="{{ route('admin.orders.index') }}" wire:navigate class="text-xs text-yellow-600 dark:text-yellow-400 hover:underline">Review pending →</a>
            </div>
        </div>

        {{-- Processing Orders --}}
        <div class="group relative bg-white dark:bg-zinc-800 rounded-2xl shadow-md hover:shadow-xl transition-all border border-zinc-200 dark:border-zinc-700">
            <div class="relative p-6">
                <div class="flex items-start justify-between mb-4">
                    <div class="p-3 bg-linear-to-br from-blue-500 to-cyan-500 rounded-xl shadow-md">
                        <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                        </svg>
                    </div>
                    <span class="px-3 py-1 text-xs font-semibold text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-900/30 rounded-full">In Progress</span>
                </div>
                <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">Processing</p>
                <p class="text-4xl font-bold text-zinc-900 dark:text-white">{{ $processingOrders }}</p>
                <a href="{{ route('admin.orders.index') }}" wire:navigate class="text-xs text-blue-600 dark:text-blue-400 hover:underline">View processing →</a>
            </div>
        </div>
    </div>

    {{-- Quick Actions & Recent Products --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Quick Actions --}}
        <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-md border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="bg-linear-to-r from-zinc-50 to-slate-50 dark:from-zinc-800 dark:to-zinc-800 px-6 py-4 border-b border-zinc-200 dark:border-zinc-700">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
                    </svg>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-wide">{{ __('Quick Actions') }}</h3>
                </div>
            </div>
            <div class="p-6 space-y-3">
                <a href="{{ route('admin.products.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl bg-amber-50 dark:bg-amber-900/10 hover:bg-amber-100 dark:hover:bg-amber-900/20 border border-amber-200 dark:border-amber-800/30 transition-all group">
                    <div class="p-2 bg-amber-500 rounded-lg">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 group-hover:text-amber-700 dark:group-hover:text-amber-400">{{ __('Manage Products') }}</span>
                </a>
                <a href="{{ route('admin.categories.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl bg-orange-50 dark:bg-orange-900/10 hover:bg-orange-100 dark:hover:bg-orange-900/20 border border-orange-200 dark:border-orange-800/30 transition-all group">
                    <div class="p-2 bg-orange-500 rounded-lg">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 group-hover:text-orange-700 dark:group-hover:text-orange-400">{{ __('Manage Categories') }}</span>
                </a>
                <a href="{{ route('admin.orders.index') }}" wire:navigate class="flex items-center gap-3 px-4 py-3 rounded-xl bg-violet-50 dark:bg-violet-900/10 hover:bg-violet-100 dark:hover:bg-violet-900/20 border border-violet-200 dark:border-violet-800/30 transition-all group">
                    <div class="p-2 bg-violet-500 rounded-lg">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                        </svg>
                    </div>
                    <span class="text-sm font-semibold text-zinc-700 dark:text-zinc-300 group-hover:text-violet-700 dark:group-hover:text-violet-400">{{ __('Manage Orders') }}</span>
                </a>

                @if ($lowStock > 0)
                    <div class="flex items-center gap-3 px-4 py-3 rounded-xl bg-red-50 dark:bg-red-900/10 border border-red-200 dark:border-red-800/30">
                        <div class="p-2 bg-red-500 rounded-lg">
                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                            </svg>
                        </div>
                        <span class="text-sm font-semibold text-red-700 dark:text-red-300">{{ $lowStock }} {{ __('items low on stock') }}</span>
                    </div>
                @endif
            </div>
        </div>

        {{-- Recent Products --}}
        <div class="lg:col-span-2 bg-white dark:bg-zinc-800 rounded-2xl shadow-md border border-zinc-200 dark:border-zinc-700 overflow-hidden">
            <div class="bg-linear-to-r from-zinc-50 to-slate-50 dark:from-zinc-800 dark:to-zinc-800 px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <h3 class="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-wide">{{ __('Recent Products') }}</h3>
                </div>
                <a href="{{ route('admin.products.index') }}" wire:navigate class="text-xs font-semibold text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300">
                    {{ __('View All →') }}
                </a>
            </div>
            <table class="w-full">
                <thead class="bg-zinc-100 dark:bg-zinc-800/70 border-b border-zinc-200 dark:border-zinc-700">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">{{ __('Product') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">{{ __('Category') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">{{ __('Price') }}</th>
                        <th class="px-6 py-3 text-left text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">{{ __('Stock') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                    @forelse ($recentProducts as $product)
                        <tr class="group hover:bg-amber-50/50 dark:hover:bg-amber-950/20 transition-all">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    @if ($product->image_path)
                                        <img src="{{ Storage::url($product->image_path) }}" alt="{{ $product->name }}" class="h-9 w-9 rounded-lg object-cover" />
                                    @else
                                        <div class="flex h-9 w-9 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/20">
                                            <svg class="w-5 h-5 text-amber-600 dark:text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/>
                                            </svg>
                                        </div>
                                    @endif
                                    <div class="text-sm font-semibold text-zinc-900 dark:text-white">{{ $product->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-semibold bg-zinc-100 dark:bg-zinc-700 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-600">
                                    {{ $product->category?->name ?? '—' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-sm font-semibold text-zinc-700 dark:text-zinc-300">₱{{ number_format($product->price, 2) }}</td>
                            <td class="px-6 py-4">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-xs font-bold {{ $product->stock > 10 ? 'bg-emerald-100 text-emerald-700 dark:bg-emerald-900/30 dark:text-emerald-300' : ($product->stock > 0 ? 'bg-amber-100 text-amber-700 dark:bg-amber-900/30 dark:text-amber-300' : 'bg-red-100 text-red-700 dark:bg-red-900/30 dark:text-red-300') }}">
                                    {{ $product->stock }} {{ __('units') }}
                                </span>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center">
                                <p class="text-sm text-zinc-500 dark:text-zinc-400">{{ __('No products yet. Add your first product to get started.') }}</p>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Recent Orders --}}
    <div class="bg-white dark:bg-zinc-800 rounded-2xl shadow-md border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="bg-linear-to-r from-zinc-50 to-slate-50 dark:from-zinc-800 dark:to-zinc-800 px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between">
            <div class="flex items-center gap-2">
                <svg class="w-5 h-5 text-zinc-600 dark:text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                </svg>
                <h3 class="text-sm font-bold text-zinc-900 dark:text-white uppercase tracking-wide">Recent Orders</h3>
            </div>
            <a href="{{ route('admin.orders.index') }}" wire:navigate class="text-xs font-semibold text-amber-600 hover:text-amber-700 dark:text-amber-400 dark:hover:text-amber-300">
                View All →
            </a>
        </div>
        <table class="w-full">
            <thead class="bg-zinc-100 dark:bg-zinc-800/70 border-b border-zinc-200 dark:border-zinc-700">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">Order</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">Customer</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">Total</th>
                    <th class="px-6 py-3 text-left text-xs font-bold text-zinc-700 dark:text-zinc-300 uppercase">Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                @forelse($recentOrders as $order)
                    <tr class="hover:bg-amber-50/50 dark:hover:bg-amber-950/20 transition-all">
                        <td class="px-6 py-3 text-sm font-semibold text-zinc-900 dark:text-white">#{{ $order->id }}</td>
                        <td class="px-6 py-3 text-sm text-zinc-700 dark:text-zinc-300">{{ $order->user->name }}</td>
                        <td class="px-6 py-3 text-sm font-semibold text-zinc-700 dark:text-zinc-300">₱{{ number_format($order->total_amount, 2) }}</td>
                        <td class="px-6 py-3">
                            <span @class([
                                'px-2.5 py-0.5 text-xs font-semibold rounded-full',
                                'text-yellow-700 bg-yellow-100 dark:text-yellow-300 dark:bg-yellow-900/30' => $order->status->value === 'pending',
                                'text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-900/30' => $order->status->value === 'processing',
                                'text-green-700 bg-green-100 dark:text-green-300 dark:bg-green-900/30' => $order->status->value === 'ready',
                                'text-zinc-700 bg-zinc-100 dark:text-zinc-300 dark:bg-zinc-700' => $order->status->value === 'completed',
                                'text-red-700 bg-red-100 dark:text-red-300 dark:bg-red-900/30' => $order->status->value === 'cancelled',
                            ])>{{ $order->status->label() }}</span>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" class="px-6 py-8 text-center text-sm text-zinc-500 dark:text-zinc-400">No orders yet.</td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
