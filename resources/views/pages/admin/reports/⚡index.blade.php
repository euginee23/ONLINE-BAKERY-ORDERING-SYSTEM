<?php

use App\Enums\OrderStatus;
use App\Enums\OrderType;
use App\Exports\CategorySalesExport;
use App\Exports\CustomerReportExport;
use App\Exports\OrdersReportExport;
use App\Exports\ProductSalesExport;
use App\Exports\SalesSummaryExport;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Title;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;

new #[Layout('layouts.admin'), Title('Reports')] class extends Component {
    public string $reportType = 'sales_summary';
    public string $dateFrom = '';
    public string $dateTo = '';
    public string $statusFilter = '';
    public string $orderTypeFilter = '';
    public ?int $categoryFilter = null;

    public function mount(): void
    {
        $this->dateFrom = now()->startOfMonth()->format('Y-m-d');
        $this->dateTo = now()->format('Y-m-d');
    }

    public function updatedReportType(): void
    {
        $this->statusFilter = '';
        $this->orderTypeFilter = '';
        $this->categoryFilter = null;
    }

    public function export(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $filename = $this->reportType . '_' . now()->format('Y-m-d_His') . '.xlsx';

        return match ($this->reportType) {
            'sales_summary' => Excel::download(
                new SalesSummaryExport($this->dateFrom ?: null, $this->dateTo ?: null, $this->orderTypeFilter ?: null),
                $filename,
            ),
            'orders' => Excel::download(
                new OrdersReportExport($this->dateFrom ?: null, $this->dateTo ?: null, $this->statusFilter ?: null, $this->orderTypeFilter ?: null),
                $filename,
            ),
            'product_sales' => Excel::download(
                new ProductSalesExport($this->dateFrom ?: null, $this->dateTo ?: null, $this->categoryFilter),
                $filename,
            ),
            'category_sales' => Excel::download(
                new CategorySalesExport($this->dateFrom ?: null, $this->dateTo ?: null),
                $filename,
            ),
            'customers' => Excel::download(
                new CustomerReportExport($this->dateFrom ?: null, $this->dateTo ?: null),
                $filename,
            ),
        };
    }

    public function with(): array
    {
        return [
            'previewData' => $this->getPreviewData(),
            'overallStats' => $this->getOverallStats(),
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
        ];
    }

    private function getOverallStats(): array
    {
        $stats = Order::query()
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->selectRaw("
                SUM(CASE WHEN status != 'cancelled' THEN total_amount ELSE 0 END) as revenue,
                SUM(CASE WHEN status != 'cancelled' THEN 1 ELSE 0 END) as active_orders,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'processing' THEN 1 ELSE 0 END) as processing,
                SUM(CASE WHEN status = 'ready' THEN 1 ELSE 0 END) as ready,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled,
                SUM(CASE WHEN type = 'delivery' AND status != 'cancelled' THEN 1 ELSE 0 END) as delivery_count,
                SUM(CASE WHEN type = 'pickup' AND status != 'cancelled' THEN 1 ELSE 0 END) as pickup_count
            ")
            ->first();

        $revenue = (float) ($stats->revenue ?? 0);
        $activeOrders = (int) ($stats->active_orders ?? 0);

        return [
            'revenue' => $revenue,
            'activeOrders' => $activeOrders,
            'avgOrderValue' => $activeOrders > 0 ? $revenue / $activeOrders : 0,
            'pending' => (int) ($stats->pending ?? 0),
            'processing' => (int) ($stats->processing ?? 0),
            'ready' => (int) ($stats->ready ?? 0),
            'completed' => (int) ($stats->completed ?? 0),
            'cancelled' => (int) ($stats->cancelled ?? 0),
            'deliveryCount' => (int) ($stats->delivery_count ?? 0),
            'pickupCount' => (int) ($stats->pickup_count ?? 0),
        ];
    }

    private function getPreviewData(): array
    {
        return match ($this->reportType) {
            'sales_summary' => $this->getSalesSummaryPreview(),
            'orders' => $this->getOrdersPreview(),
            'product_sales' => $this->getProductSalesPreview(),
            'category_sales' => $this->getCategorySalesPreview(),
            'customers' => $this->getCustomersPreview(),
            default => ['headings' => [], 'rows' => [], 'summary' => []],
        };
    }

    private function getSalesSummaryPreview(): array
    {
        $base = Order::query()
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->orderTypeFilter, fn ($q) => $q->where('type', $this->orderTypeFilter));

        $nonCancelled = (clone $base)->whereNotIn('status', ['cancelled']);
        $totalRevenue = (float) (clone $nonCancelled)->sum('total_amount');
        $totalOrders = (clone $nonCancelled)->count();
        $deliveryRevenue = (float) (clone $nonCancelled)->where('type', 'delivery')->sum('total_amount');
        $pickupRevenue = (float) (clone $nonCancelled)->where('type', 'pickup')->sum('total_amount');

        $rows = (clone $base)
            ->selectRaw("
                DATE(created_at) as date,
                SUM(CASE WHEN type = 'delivery' AND status != 'cancelled' THEN 1 ELSE 0 END) as delivery_count,
                SUM(CASE WHEN type = 'pickup' AND status != 'cancelled' THEN 1 ELSE 0 END) as pickup_count,
                SUM(CASE WHEN status != 'cancelled' THEN 1 ELSE 0 END) as order_count,
                SUM(CASE WHEN status = 'cancelled' THEN 1 ELSE 0 END) as cancelled_count,
                SUM(CASE WHEN status != 'cancelled' THEN total_amount ELSE 0 END) as revenue
            ")
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at) DESC')
            ->limit(15)
            ->get()
            ->map(fn ($row) => [
                Carbon::parse($row->date)->format('M d, Y'),
                $row->delivery_count ?: '—',
                $row->pickup_count ?: '—',
                $row->order_count,
                $row->cancelled_count ?: '—',
                '₱'.number_format((float) $row->revenue, 2),
                '₱'.number_format($row->order_count > 0 ? (float) $row->revenue / $row->order_count : 0, 2),
            ])
            ->toArray();

        return [
            'headings' => ['Date', 'Delivery', 'Pickup', 'Total Orders', 'Cancelled', 'Revenue', 'Avg / Order'],
            'rows' => $rows,
            'summary' => [
                ['label' => 'Total Revenue', 'value' => '₱'.number_format($totalRevenue, 2)],
                ['label' => 'Total Orders', 'value' => number_format($totalOrders)],
                ['label' => 'Delivery Revenue', 'value' => '₱'.number_format($deliveryRevenue, 2)],
                ['label' => 'Pickup Revenue', 'value' => '₱'.number_format($pickupRevenue, 2)],
                ['label' => 'Avg Order Value', 'value' => '₱'.number_format($totalOrders > 0 ? $totalRevenue / $totalOrders : 0, 2)],
            ],
        ];
    }

    private function getOrdersPreview(): array
    {
        $query = Order::with(['user', 'items.product'])
            ->latest()
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->orderTypeFilter, fn ($q) => $q->where('type', $this->orderTypeFilter));

        $totalOrders = (clone $query)->count();
        $totalRevenue = (float) (clone $query)->whereNotIn('status', ['cancelled'])->sum('total_amount');
        $pendingCount = (clone $query)->where('status', OrderStatus::Pending)->count();
        $completedCount = (clone $query)->where('status', OrderStatus::Completed)->count();
        $cancelledCount = (clone $query)->where('status', OrderStatus::Cancelled)->count();

        $rows = (clone $query)
            ->limit(15)
            ->get()
            ->map(fn (Order $o) => [
                '#'.$o->id,
                $o->created_at->format('M d, Y'),
                $o->created_at->format('h:i A'),
                $o->user->name,
                $o->user->email,
                $o->type->label(),
                $o->status->label(),
                Str::limit(
                    $o->items->map(fn ($i) => $i->quantity.'× '.$i->product->name)->implode(', '),
                    60,
                ),
                $o->notes ? Str::limit($o->notes, 30) : '—',
                '₱'.number_format((float) $o->total_amount, 2),
            ])
            ->toArray();

        return [
            'headings' => ['Order #', 'Date', 'Time', 'Customer', 'Email', 'Type', 'Status', 'Items Ordered', 'Notes', 'Total'],
            'rows' => $rows,
            'summary' => [
                ['label' => 'Total Orders', 'value' => number_format($totalOrders)],
                ['label' => 'Revenue', 'value' => '₱'.number_format($totalRevenue, 2)],
                ['label' => 'Pending', 'value' => number_format($pendingCount)],
                ['label' => 'Completed', 'value' => number_format($completedCount)],
                ['label' => 'Cancelled', 'value' => number_format($cancelledCount)],
            ],
        ];
    }

    private function getProductSalesPreview(): array
    {
        $items = OrderItem::query()
            ->selectRaw('product_id, SUM(quantity) as total_quantity, SUM(subtotal) as total_revenue')
            ->whereHas('order', function ($q) {
                $q->whereNotIn('status', ['cancelled']);
                if ($this->dateFrom) {
                    $q->whereDate('created_at', '>=', $this->dateFrom);
                }
                if ($this->dateTo) {
                    $q->whereDate('created_at', '<=', $this->dateTo);
                }
            })
            ->when($this->categoryFilter, fn ($q) => $q->whereHas('product', fn ($p) => $p->where('category_id', $this->categoryFilter)))
            ->groupBy('product_id')
            ->orderByRaw('SUM(subtotal) DESC')
            ->with('product.category')
            ->limit(15)
            ->get();

        $grandTotal = (float) $items->sum('total_revenue');

        $rows = $items->map(fn ($row, $index) => [
            $index + 1,
            $row->product->name,
            $row->product->category->name ?? '—',
            number_format((int) $row->total_quantity),
            '₱'.number_format((float) $row->total_revenue, 2),
            $grandTotal > 0 ? number_format((float) $row->total_revenue / $grandTotal * 100, 1).'%' : '—',
            '₱'.number_format($row->total_quantity > 0 ? (float) $row->total_revenue / $row->total_quantity : 0, 2),
            $row->product->stock.' left',
        ])->toArray();

        return [
            'headings' => ['#', 'Product', 'Category', 'Qty Sold', 'Revenue', '% Share', 'Avg Price', 'Stock'],
            'rows' => $rows,
            'summary' => [
                ['label' => 'Total Revenue', 'value' => '₱'.number_format($grandTotal, 2)],
                ['label' => 'Products Tracked', 'value' => number_format($items->count())],
            ],
        ];
    }

    private function getCategorySalesPreview(): array
    {
        $rows = OrderItem::query()
            ->selectRaw('products.category_id, categories.name as category_name, SUM(order_items.quantity) as total_quantity, SUM(order_items.subtotal) as total_revenue, COUNT(DISTINCT order_items.order_id) as order_count')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->when($this->dateFrom, fn ($q) => $q->whereDate('orders.created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('orders.created_at', '<=', $this->dateTo))
            ->groupBy('products.category_id', 'categories.name')
            ->orderByRaw('SUM(order_items.subtotal) DESC')
            ->get();

        $grandTotal = (float) $rows->sum('total_revenue');

        $mappedRows = $rows->map(fn ($row) => [
            $row->category_name,
            number_format((int) $row->order_count),
            number_format((int) $row->total_quantity),
            '₱'.number_format((float) $row->total_revenue, 2),
            $grandTotal > 0 ? number_format((float) $row->total_revenue / $grandTotal * 100, 1).'%' : '—',
            '₱'.number_format($row->order_count > 0 ? (float) $row->total_revenue / $row->order_count : 0, 2),
        ])->toArray();

        return [
            'headings' => ['Category', 'Orders', 'Qty Sold', 'Revenue', '% Share', 'Avg / Order'],
            'rows' => $mappedRows,
            'summary' => [
                ['label' => 'Total Revenue', 'value' => '₱'.number_format($grandTotal, 2)],
                ['label' => 'Categories Active', 'value' => number_format($rows->count())],
            ],
        ];
    }

    private function getCustomersPreview(): array
    {
        $query = User::query()
            ->where('role', 'customer')
            ->withCount(['orders as total_orders' => function ($q) {
                $q->whereNotIn('status', ['cancelled']);
                if ($this->dateFrom) {
                    $q->whereDate('created_at', '>=', $this->dateFrom);
                }
                if ($this->dateTo) {
                    $q->whereDate('created_at', '<=', $this->dateTo);
                }
            }])
            ->withSum(['orders as total_spent' => function ($q) {
                $q->whereNotIn('status', ['cancelled']);
                if ($this->dateFrom) {
                    $q->whereDate('created_at', '>=', $this->dateFrom);
                }
                if ($this->dateTo) {
                    $q->whereDate('created_at', '<=', $this->dateTo);
                }
            }], 'total_amount')
            ->withMax(['orders as last_order_at' => function ($q) {
                $q->whereNotIn('status', ['cancelled']);
            }], 'created_at')
            ->whereHas('orders', function ($q) {
                $q->whereNotIn('status', ['cancelled']);
                if ($this->dateFrom) {
                    $q->whereDate('created_at', '>=', $this->dateFrom);
                }
                if ($this->dateTo) {
                    $q->whereDate('created_at', '<=', $this->dateTo);
                }
            })
            ->orderByDesc('total_spent')
            ->limit(15);

        $rows = $query->get()->map(fn (User $u) => [
            $u->name,
            $u->email,
            number_format((int) $u->total_orders),
            '₱'.number_format((float) $u->total_spent, 2),
            '₱'.number_format($u->total_orders > 0 ? (float) $u->total_spent / $u->total_orders : 0, 2),
            $u->last_order_at ? Carbon::parse($u->last_order_at)->format('M d, Y') : '—',
            $u->created_at->format('M d, Y'),
        ])->toArray();

        return [
            'headings' => ['Customer', 'Email', 'Orders', 'Total Spent', 'Avg / Order', 'Last Order', 'Member Since'],
            'rows' => $rows,
            'summary' => [],
        ];
    }
}

?>

<div class="flex flex-col gap-6">
    {{-- Header --}}
    <div class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
        <div>
            <h1 class="text-2xl sm:text-3xl font-bold text-zinc-900 dark:text-white">Reports</h1>
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">
                @if($dateFrom && $dateTo)
                    Period: {{ \Illuminate\Support\Carbon::parse($dateFrom)->format('M d, Y') }} — {{ \Illuminate\Support\Carbon::parse($dateTo)->format('M d, Y') }}
                @else
                    All-time data. Use filters to narrow the period.
                @endif
            </p>
        </div>
        <flux:button wire:click="export" variant="primary" icon="arrow-down-tray">
            Export to Excel
        </flux:button>
    </div>

    {{-- Filters --}}
    <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-4 sm:p-6">
        <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-4">Filters</p>
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            <flux:field>
                <flux:label>Report Type</flux:label>
                <flux:select wire:model.live="reportType">
                    <flux:select.option value="sales_summary">Sales Summary</flux:select.option>
                    <flux:select.option value="orders">Orders Report</flux:select.option>
                    <flux:select.option value="product_sales">Product Sales</flux:select.option>
                    <flux:select.option value="category_sales">Category Sales</flux:select.option>
                    <flux:select.option value="customers">Customer Report</flux:select.option>
                </flux:select>
            </flux:field>

            <flux:field>
                <flux:label>Date From</flux:label>
                <flux:input type="date" wire:model.live="dateFrom" />
            </flux:field>

            <flux:field>
                <flux:label>Date To</flux:label>
                <flux:input type="date" wire:model.live="dateTo" />
            </flux:field>

            @if(in_array($reportType, ['sales_summary', 'orders']))
                <flux:field>
                    <flux:label>Order Type</flux:label>
                    <flux:select wire:model.live="orderTypeFilter">
                        <flux:select.option value="">All Types</flux:select.option>
                        @foreach(\App\Enums\OrderType::cases() as $type)
                            <flux:select.option value="{{ $type->value }}">{{ $type->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            @endif

            @if($reportType === 'orders')
                <flux:field>
                    <flux:label>Status</flux:label>
                    <flux:select wire:model.live="statusFilter">
                        <flux:select.option value="">All Statuses</flux:select.option>
                        @foreach(\App\Enums\OrderStatus::cases() as $status)
                            <flux:select.option value="{{ $status->value }}">{{ $status->label() }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            @endif

            @if($reportType === 'product_sales')
                <flux:field>
                    <flux:label>Category</flux:label>
                    <flux:select wire:model.live="categoryFilter">
                        <flux:select.option value="">All Categories</flux:select.option>
                        @foreach($categories as $category)
                            <flux:select.option value="{{ $category->id }}">{{ $category->name }}</flux:select.option>
                        @endforeach
                    </flux:select>
                </flux:field>
            @endif
        </div>
    </div>

    {{-- Period KPI Strip --}}
    <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
        <div class="col-span-2 sm:col-span-1 bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-4 sm:p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 bg-linear-to-br from-amber-500 to-orange-600 rounded-xl shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1" />
                    </svg>
                </div>
                <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Revenue</p>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-white">₱{{ number_format($overallStats['revenue'], 2) }}</p>
            <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">excl. cancelled</p>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-4 sm:p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 bg-linear-to-br from-blue-500 to-indigo-600 rounded-xl shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" />
                    </svg>
                </div>
                <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Orders</p>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($overallStats['activeOrders']) }}</p>
            <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">non-cancelled</p>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-4 sm:p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 bg-linear-to-br from-emerald-500 to-green-600 rounded-xl shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6" />
                    </svg>
                </div>
                <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Avg Value</p>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-white">₱{{ number_format($overallStats['avgOrderValue'], 2) }}</p>
            <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">per order</p>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-4 sm:p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 bg-linear-to-br from-violet-500 to-purple-600 rounded-xl shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13 16V6a1 1 0 00-1-1H4a1 1 0 00-1 1v10a1 1 0 001 1h1m8-1a1 1 0 01-1 1H9m4-1V8a1 1 0 011-1h2.586a1 1 0 01.707.293l3.414 3.414a1 1 0 01.293.707V16a1 1 0 01-1 1h-1m-6-1a1 1 0 001 1h1M5 17a2 2 0 104 0m-4 0a2 2 0 114 0m6 0a2 2 0 104 0m-4 0a2 2 0 114 0" />
                    </svg>
                </div>
                <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Delivery</p>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($overallStats['deliveryCount']) }}</p>
            <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">delivery orders</p>
        </div>

        <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-4 sm:p-5">
            <div class="flex items-center gap-3 mb-3">
                <div class="p-2 bg-linear-to-br from-rose-500 to-pink-600 rounded-xl shrink-0">
                    <svg class="w-5 h-5 text-white" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                    </svg>
                </div>
                <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">Pickup</p>
            </div>
            <p class="text-2xl font-bold text-zinc-900 dark:text-white">{{ number_format($overallStats['pickupCount']) }}</p>
            <p class="text-xs text-zinc-400 dark:text-zinc-500 mt-1">pickup orders</p>
        </div>
    </div>

    {{-- Order Status Breakdown --}}
    <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-4 sm:p-5">
        <p class="text-xs font-semibold text-zinc-500 dark:text-zinc-400 uppercase tracking-wide mb-4">Order Status Breakdown</p>
        <div class="flex flex-wrap gap-3">
            <div class="flex items-center gap-2 px-4 py-2 bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-800 rounded-xl">
                <span class="w-2 h-2 rounded-full bg-yellow-400 shrink-0"></span>
                <span class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Pending</span>
                <span class="text-sm font-bold text-yellow-900 dark:text-yellow-200">{{ $overallStats['pending'] }}</span>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-xl">
                <span class="w-2 h-2 rounded-full bg-blue-400 shrink-0"></span>
                <span class="text-sm font-medium text-blue-800 dark:text-blue-300">Processing</span>
                <span class="text-sm font-bold text-blue-900 dark:text-blue-200">{{ $overallStats['processing'] }}</span>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-green-50 dark:bg-green-900/20 border border-green-200 dark:border-green-800 rounded-xl">
                <span class="w-2 h-2 rounded-full bg-green-400 shrink-0"></span>
                <span class="text-sm font-medium text-green-800 dark:text-green-300">Ready</span>
                <span class="text-sm font-bold text-green-900 dark:text-green-200">{{ $overallStats['ready'] }}</span>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-zinc-100 dark:bg-zinc-700/50 border border-zinc-200 dark:border-zinc-600 rounded-xl">
                <span class="w-2 h-2 rounded-full bg-zinc-400 shrink-0"></span>
                <span class="text-sm font-medium text-zinc-700 dark:text-zinc-300">Completed</span>
                <span class="text-sm font-bold text-zinc-900 dark:text-zinc-100">{{ $overallStats['completed'] }}</span>
            </div>
            <div class="flex items-center gap-2 px-4 py-2 bg-red-50 dark:bg-red-900/20 border border-red-200 dark:border-red-800 rounded-xl">
                <span class="w-2 h-2 rounded-full bg-red-400 shrink-0"></span>
                <span class="text-sm font-medium text-red-800 dark:text-red-300">Cancelled</span>
                <span class="text-sm font-bold text-red-900 dark:text-red-200">{{ $overallStats['cancelled'] }}</span>
            </div>
        </div>
    </div>

    {{-- Per-Report Summary Cards --}}
    @if(!empty($previewData['summary']))
        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
            @foreach($previewData['summary'] as $stat)
                <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-4 text-center">
                    <p class="text-xs font-medium text-zinc-500 dark:text-zinc-400 uppercase tracking-wide">{{ $stat['label'] }}</p>
                    <p class="mt-1 text-xl font-bold text-zinc-900 dark:text-white">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Preview Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
        <div class="px-4 sm:px-6 py-4 border-b border-zinc-200 dark:border-zinc-700 flex items-center justify-between gap-4">
            @php
                $reportLabels = [
                    'sales_summary' => 'Sales Summary',
                    'orders' => 'Orders Report',
                    'product_sales' => 'Product Sales',
                    'category_sales' => 'Category Sales',
                    'customers' => 'Customer Report',
                ];
            @endphp
            <h2 class="text-sm font-semibold text-zinc-700 dark:text-zinc-300">
                {{ $reportLabels[$reportType] ?? 'Report' }} · Preview
            </h2>
            <span class="text-xs text-zinc-400 dark:text-zinc-500 shrink-0">Up to 15 rows · Export for full data</span>
        </div>

        @if(empty($previewData['rows']))
            <div class="flex flex-col items-center justify-center py-20 text-center">
                <div class="mb-4 text-6xl">📊</div>
                <h3 class="text-lg font-semibold text-zinc-700 dark:text-zinc-300">No data found</h3>
                <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Try adjusting your date range or filters.</p>
            </div>
        @else
            <div class="overflow-x-auto">
                <table class="w-full text-sm">
                    <thead>
                        <tr class="border-b border-zinc-200 dark:border-zinc-700 bg-zinc-50 dark:bg-zinc-900/50">
                            @foreach($previewData['headings'] as $heading)
                                <th class="px-4 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-300 whitespace-nowrap">{{ $heading }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-zinc-100 dark:divide-zinc-700/50">
                        @foreach($previewData['rows'] as $row)
                            <tr class="hover:bg-zinc-50 dark:hover:bg-zinc-700/30 transition">
                                @foreach($row as $cell)
                                    <td class="px-4 py-3 text-zinc-600 dark:text-zinc-300 whitespace-nowrap">{{ $cell }}</td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
