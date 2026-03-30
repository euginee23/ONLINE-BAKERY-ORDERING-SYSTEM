<?php

use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Attributes\WithoutUrlPagination;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.customer'), Title('My Orders'), WithoutUrlPagination] class extends Component {
    use WithPagination;

    public string $statusFilter = '';

    public int $perPage = 10;

    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        $this->resetPage();
    }

    public function with(): array
    {
        $query = Order::forUser(auth()->id())->with(['items.product'])->latest();

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        return [
            'orders' => $query->paginate($this->perPage),
            'perPageOptions' => [5, 10, 25, 50],
        ];
    }
}

?>

<div class="flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">My Orders</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Track all your bakery orders here.</p>
        </div>
        <a
            href="{{ route('customer.menu') }}"
            wire:navigate
            class="inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition rounded-lg bg-gold-700 hover:bg-gold-800 dark:bg-gold-500 dark:text-zinc-900 dark:hover:bg-gold-400"
        >
            Order More
        </a>
    </div>

    {{-- Filters row: status + per-page --}}
    <div class="flex flex-wrap items-center justify-between gap-3">
    <div class="flex flex-wrap gap-2">
        @foreach(['' => 'All Orders', 'pending' => 'Pending', 'processing' => 'Processing', 'ready' => 'Ready', 'completed' => 'Completed', 'cancelled' => 'Cancelled'] as $value => $label)
            <button
                wire:click="$set('statusFilter', '{{ $value }}')"
                @class([
                    'px-3 py-1.5 text-sm font-medium transition rounded-lg',
                    'bg-gold-700 text-white dark:bg-gold-500 dark:text-zinc-900' => $statusFilter === $value,
                    'border border-zinc-300 text-zinc-600 hover:bg-zinc-100 dark:border-zinc-600 dark:text-zinc-400 dark:hover:bg-zinc-800' => $statusFilter !== $value,
                ])
            >
                {{ $label }}
            </button>
        @endforeach
    </div>
        <select wire:model.live="perPage" class="px-3 py-1.5 text-sm border rounded-lg bg-white dark:bg-zinc-800 border-zinc-300 dark:border-zinc-600 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gold-500">
            <option value="5">5 / page</option>
            <option value="10">10 / page</option>
            <option value="25">25 / page</option>
            <option value="50">50 / page</option>
            <option value="100">100 / page</option>
        </select>
    </div>

    {{-- Orders List --}}
    @if($orders->isEmpty())
        <div class="flex flex-col items-center justify-center py-20 text-center bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700">
            <div class="mb-4 text-6xl">📋</div>
            <h3 class="text-lg font-semibold text-zinc-700 dark:text-zinc-300">No orders yet</h3>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Start browsing our fresh baked goods!</p>
            <a href="{{ route('customer.menu') }}" wire:navigate class="mt-4 inline-flex items-center gap-2 px-4 py-2 text-sm font-semibold text-white transition rounded-lg bg-gold-700 hover:bg-gold-800 dark:bg-gold-500 dark:text-zinc-900">
                Browse Menu
            </a>
        </div>
    @else
        <div class="flex flex-col gap-4">
            @foreach($orders as $order)
                <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
                    <div class="flex flex-col gap-4 p-6 sm:flex-row sm:items-center sm:justify-between">
                        <div class="flex items-start gap-4">
                            <div class="flex items-center justify-center rounded-xl size-12 bg-amber-50 dark:bg-amber-900/20">
                                <svg class="size-6 text-gold-700 dark:text-gold-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                </svg>
                            </div>
                            <div>
                                <div class="flex items-center gap-2 flex-wrap">
                                    <h3 class="text-sm font-semibold text-zinc-900 dark:text-white">Order #{{ $order->id }}</h3>
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
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full border border-zinc-200 text-zinc-600 dark:border-zinc-600 dark:text-zinc-400">
                                        {{ $order->type->label() }}
                                    </span>
                                </div>
                                <p class="mt-1 text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $order->items->count() }} item(s) · {{ $order->created_at->format('M d, Y \a\t h:i A') }}
                                </p>
                                <p class="mt-0.5 text-xs text-zinc-500 dark:text-zinc-400 line-clamp-1">
                                    {{ $order->items->pluck('product.name')->implode(', ') }}
                                </p>
                            </div>
                        </div>

                        <div class="flex items-center gap-4 sm:flex-col sm:items-end">
                            <span class="text-lg font-bold text-zinc-900 dark:text-white">₱{{ number_format($order->total_amount, 2) }}</span>
                            <a
                                href="{{ route('customer.order-detail', $order) }}"
                                wire:navigate
                                class="px-4 py-1.5 text-sm font-medium transition border rounded-lg text-zinc-700 border-zinc-300 hover:bg-zinc-50 dark:text-zinc-300 dark:border-zinc-600 dark:hover:bg-zinc-800"
                            >
                                View Details
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach

            <div>
                {{ $orders->links(data: ['scrollTo' => false]) }}
            </div>
        </div>
    @endif
</div>
