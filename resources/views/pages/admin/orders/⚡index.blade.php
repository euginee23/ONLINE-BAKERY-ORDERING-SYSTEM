<?php

use App\Enums\OrderStatus;
use App\Models\Order;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Livewire\WithPagination;

new #[Layout('layouts.admin'), Title('Manage Orders')] class extends Component {
    use WithPagination;

    public string $statusFilter = '';
    public string $typeFilter = '';
    public string $search = '';

    public function updatingStatusFilter(): void
    {
        $this->resetPage();
    }

    public function updatingTypeFilter(): void
    {
        $this->resetPage();
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updateStatus(int $orderId, string $status): void
    {
        $order = Order::findOrFail($orderId);
        $order->update(['status' => $status]);
        $this->dispatch('notify', message: 'Order #' . $orderId . ' status updated.', type: 'success');
    }

    public function with(): array
    {
        $query = Order::with(['user', 'items'])->latest();

        if ($this->statusFilter) {
            $query->where('status', $this->statusFilter);
        }

        if ($this->typeFilter) {
            $query->where('type', $this->typeFilter);
        }

        if ($this->search) {
            $query->whereHas('user', fn ($q) => $q->where('name', 'like', '%' . $this->search . '%')
                ->orWhere('email', 'like', '%' . $this->search . '%'));
        }

        return [
            'orders' => $query->paginate(15),
            'statusCounts' => collect(OrderStatus::cases())->mapWithKeys(
                fn (OrderStatus $s) => [$s->value => Order::where('status', $s->value)->count()]
            ),
        ];
    }
}

?>

<div class="flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">Manage Orders</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">View and update customer order statuses.</p>
        </div>
    </div>

    {{-- Status Summary Cards --}}
    <div class="grid grid-cols-2 gap-4 sm:grid-cols-5">
        @foreach(\App\Enums\OrderStatus::cases() as $status)
            <button
                wire:click="$set('statusFilter', '{{ $statusFilter === $status->value ? '' : $status->value }}')"
                @class([
                    'relative flex flex-col items-center gap-1 p-4 rounded-2xl border transition text-center',
                    'border-gold-400 bg-gold-50 dark:border-gold-600 dark:bg-gold-900/20' => $statusFilter === $status->value,
                    'border-zinc-200 bg-white hover:border-zinc-300 dark:border-zinc-700 dark:bg-zinc-800 dark:hover:border-zinc-600' => $statusFilter !== $status->value,
                ])
            >
                <span class="text-2xl font-bold text-zinc-900 dark:text-white">{{ $statusCounts[$status->value] ?? 0 }}</span>
                <span @class([
                    'px-2 py-0.5 text-xs font-semibold rounded-full',
                    'text-yellow-700 bg-yellow-100 dark:text-yellow-300 dark:bg-yellow-900/30' => $status->value === 'pending',
                    'text-blue-700 bg-blue-100 dark:text-blue-300 dark:bg-blue-900/30' => $status->value === 'processing',
                    'text-green-700 bg-green-100 dark:text-green-300 dark:bg-green-900/30' => $status->value === 'ready',
                    'text-zinc-700 bg-zinc-100 dark:text-zinc-300 dark:bg-zinc-700' => $status->value === 'completed',
                    'text-red-700 bg-red-100 dark:text-red-300 dark:bg-red-900/30' => $status->value === 'cancelled',
                ])>{{ $status->label() }}</span>
            </button>
        @endforeach
    </div>

    {{-- Filters --}}
    <div class="flex flex-col gap-3 sm:flex-row">
        <div class="relative flex-1">
            <svg class="absolute left-3 top-1/2 -translate-y-1/2 size-4 text-zinc-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
            </svg>
            <input
                wire:model.live.debounce.300ms="search"
                type="text"
                placeholder="Search by customer name or email..."
                class="w-full pl-10 pr-4 py-2 text-sm border rounded-lg bg-white dark:bg-zinc-800 border-zinc-300 dark:border-zinc-600 text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-gold-500"
            />
        </div>
        <select
            wire:model.live="typeFilter"
            class="px-3 py-2 text-sm border rounded-lg bg-white dark:bg-zinc-800 border-zinc-300 dark:border-zinc-600 text-zinc-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-gold-500"
        >
            <option value="">All Types</option>
            <option value="delivery">Delivery</option>
            <option value="pickup">Pickup</option>
        </select>
    </div>

    {{-- Orders Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        @if($orders->isEmpty())
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="mb-4 text-6xl">📋</div>
                <h3 class="text-lg font-semibold text-zinc-700 dark:text-zinc-300">No orders found</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Try adjusting your filters.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50">
                            <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Order</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Customer</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Items</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Type</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Total</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Date</th>
                            <th class="px-6 py-3 text-left text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wider">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-200 dark:divide-zinc-700">
                        @foreach($orders as $order)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-900/30 transition">
                                <td class="px-6 py-4">
                                    <span class="font-semibold text-zinc-900 dark:text-white">#{{ $order->id }}</span>
                                </td>
                                <td class="px-6 py-4">
                                    <div>
                                        <p class="font-medium text-zinc-900 dark:text-white">{{ $order->user->name }}</p>
                                        <p class="text-xs text-zinc-500 dark:text-zinc-400">{{ $order->user->email }}</p>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-zinc-600 dark:text-zinc-400">
                                    {{ $order->items->count() }} item(s)
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-0.5 text-xs font-medium rounded-full border border-zinc-200 text-zinc-600 dark:border-zinc-600 dark:text-zinc-400">
                                        {{ $order->type->label() }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 font-semibold text-zinc-900 dark:text-white">
                                    ₱{{ number_format($order->total_amount, 2) }}
                                </td>
                                <td class="px-6 py-4 text-xs text-zinc-500 dark:text-zinc-400">
                                    {{ $order->created_at->format('M d, Y') }}<br>
                                    {{ $order->created_at->format('h:i A') }}
                                </td>
                                <td class="px-6 py-4">
                                    <select
                                        wire:change="updateStatus({{ $order->id }}, $event.target.value)"
                                        class="w-full px-2 py-1.5 text-xs font-medium rounded-lg border focus:outline-none focus:ring-2 focus:ring-gold-500 cursor-pointer
                                            {{ match($order->status->value) {
                                                'pending' => 'text-yellow-700 bg-yellow-50 border-yellow-200 dark:text-yellow-300 dark:bg-yellow-900/20 dark:border-yellow-800',
                                                'processing' => 'text-blue-700 bg-blue-50 border-blue-200 dark:text-blue-300 dark:bg-blue-900/20 dark:border-blue-800',
                                                'ready' => 'text-green-700 bg-green-50 border-green-200 dark:text-green-300 dark:bg-green-900/20 dark:border-green-800',
                                                'completed' => 'text-zinc-700 bg-zinc-50 border-zinc-200 dark:text-zinc-300 dark:bg-zinc-800 dark:border-zinc-700',
                                                'cancelled' => 'text-red-700 bg-red-50 border-red-200 dark:text-red-300 dark:bg-red-900/20 dark:border-red-800',
                                                default => 'text-zinc-700 bg-zinc-50 border-zinc-200',
                                            } }}"
                                    >
                                        @foreach(\App\Enums\OrderStatus::cases() as $status)
                                            <option
                                                value="{{ $status->value }}"
                                                {{ $order->status->value === $status->value ? 'selected' : '' }}
                                            >
                                                {{ $status->label() }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-zinc-200 dark:border-zinc-700">
                {{ $orders->links() }}
            </div>
        @endif
    </div>
</div>
