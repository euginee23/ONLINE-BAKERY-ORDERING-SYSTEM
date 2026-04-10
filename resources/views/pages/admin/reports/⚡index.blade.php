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
        $previewData = $this->getPreviewData();

        return [
            'previewData' => $previewData,
            'categories' => Category::where('is_active', true)->orderBy('name')->get(),
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
        $query = Order::query()
            ->whereNotIn('status', ['cancelled'])
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->orderTypeFilter, fn ($q) => $q->where('type', $this->orderTypeFilter));

        $totalRevenue = (float) (clone $query)->sum('total_amount');
        $totalOrders = (clone $query)->count();

        $rows = (clone $query)
            ->selectRaw("DATE(created_at) as date, COUNT(*) as order_count, SUM(total_amount) as revenue")
            ->groupByRaw('DATE(created_at)')
            ->orderByRaw('DATE(created_at) DESC')
            ->limit(15)
            ->get()
            ->map(fn ($row) => [
                Carbon::parse($row->date)->format('M d, Y'),
                $row->order_count,
                '₱' . number_format((float) $row->revenue, 2),
                '₱' . number_format($row->order_count > 0 ? (float) $row->revenue / $row->order_count : 0, 2),
            ])
            ->toArray();

        return [
            'headings' => ['Date', 'Orders', 'Revenue', 'Avg Value'],
            'rows' => $rows,
            'summary' => [
                ['label' => 'Total Revenue', 'value' => '₱' . number_format($totalRevenue, 2)],
                ['label' => 'Total Orders', 'value' => number_format($totalOrders)],
                ['label' => 'Avg Order Value', 'value' => '₱' . number_format($totalOrders > 0 ? $totalRevenue / $totalOrders : 0, 2)],
            ],
        ];
    }

    private function getOrdersPreview(): array
    {
        $query = Order::with(['user', 'items'])
            ->latest()
            ->when($this->dateFrom, fn ($q) => $q->whereDate('created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('created_at', '<=', $this->dateTo))
            ->when($this->statusFilter, fn ($q) => $q->where('status', $this->statusFilter))
            ->when($this->orderTypeFilter, fn ($q) => $q->where('type', $this->orderTypeFilter));

        $total = (clone $query)->count();

        $rows = (clone $query)
            ->limit(15)
            ->get()
            ->map(fn (Order $o) => [
                '#' . $o->id,
                $o->created_at->format('M d, Y'),
                $o->user->name,
                $o->type->label(),
                $o->status->label(),
                $o->items->count() . ' items',
                '₱' . number_format((float) $o->total_amount, 2),
            ])
            ->toArray();

        return [
            'headings' => ['Order', 'Date', 'Customer', 'Type', 'Status', 'Items', 'Total'],
            'rows' => $rows,
            'summary' => [
                ['label' => 'Total Orders', 'value' => number_format($total)],
            ],
        ];
    }

    private function getProductSalesPreview(): array
    {
        $query = OrderItem::query()
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

        $rows = $query->map(fn ($row, $index) => [
            $index + 1,
            $row->product->name,
            $row->product->category->name ?? 'N/A',
            $row->total_quantity,
            '₱' . number_format((float) $row->total_revenue, 2),
        ])->toArray();

        return [
            'headings' => ['#', 'Product', 'Category', 'Qty Sold', 'Revenue'],
            'rows' => $rows,
            'summary' => [],
        ];
    }

    private function getCategorySalesPreview(): array
    {
        $rows = OrderItem::query()
            ->selectRaw('categories.name as category_name, SUM(order_items.quantity) as total_quantity, SUM(order_items.subtotal) as total_revenue, COUNT(DISTINCT order_items.order_id) as order_count')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->join('categories', 'products.category_id', '=', 'categories.id')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->where('orders.status', '!=', 'cancelled')
            ->when($this->dateFrom, fn ($q) => $q->whereDate('orders.created_at', '>=', $this->dateFrom))
            ->when($this->dateTo, fn ($q) => $q->whereDate('orders.created_at', '<=', $this->dateTo))
            ->groupBy('products.category_id', 'categories.name')
            ->orderByRaw('SUM(order_items.subtotal) DESC')
            ->get()
            ->map(fn ($row) => [
                $row->category_name,
                $row->order_count,
                $row->total_quantity,
                '₱' . number_format((float) $row->total_revenue, 2),
            ])
            ->toArray();

        return [
            'headings' => ['Category', 'Orders', 'Qty Sold', 'Revenue'],
            'rows' => $rows,
            'summary' => [],
        ];
    }

    private function getCustomersPreview(): array
    {
        $rows = User::query()
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
            ->limit(15)
            ->get()
            ->map(fn (User $u) => [
                $u->name,
                $u->email,
                $u->total_orders,
                '₱' . number_format((float) $u->total_spent, 2),
                '₱' . number_format($u->total_orders > 0 ? (float) $u->total_spent / $u->total_orders : 0, 2),
            ])
            ->toArray();

        return [
            'headings' => ['Customer', 'Email', 'Orders', 'Total Spent', 'Avg Value'],
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
            <p class="mt-1 text-sm text-zinc-500 dark:text-zinc-400">Generate and export business reports.</p>
        </div>
        <flux:button wire:click="export" variant="primary" icon="arrow-down-tray">
            Export to Excel
        </flux:button>
    </div>

    {{-- Report Type & Filters --}}
    <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-4 sm:p-6">
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
            {{-- Report Type --}}
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

            {{-- Date From --}}
            <flux:field>
                <flux:label>Date From</flux:label>
                <flux:input type="date" wire:model.live="dateFrom" />
            </flux:field>

            {{-- Date To --}}
            <flux:field>
                <flux:label>Date To</flux:label>
                <flux:input type="date" wire:model.live="dateTo" />
            </flux:field>

            {{-- Conditional Filters --}}
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

    {{-- Summary Cards --}}
    @if(!empty($previewData['summary']))
        <div class="grid grid-cols-1 gap-4 sm:grid-cols-3">
            @foreach($previewData['summary'] as $stat)
                <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 p-4 sm:p-6 text-center">
                    <p class="text-sm font-medium text-zinc-500 dark:text-zinc-400">{{ $stat['label'] }}</p>
                    <p class="mt-1 text-2xl font-bold text-zinc-900 dark:text-white">{{ $stat['value'] }}</p>
                </div>
            @endforeach
        </div>
    @endif

    {{-- Preview Table --}}
    <div class="bg-white dark:bg-zinc-800 rounded-2xl border border-zinc-200 dark:border-zinc-700 overflow-hidden">
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
                                <th class="px-4 py-3 text-left font-semibold text-zinc-700 dark:text-zinc-300">{{ $heading }}</th>
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
            @if(count($previewData['rows']) >= 15)
                <div class="px-4 py-3 text-center text-sm text-zinc-500 dark:text-zinc-400 border-t border-zinc-200 dark:border-zinc-700">
                    Showing first 15 rows. Export to see all data.
                </div>
            @endif
        @endif
    </div>
</div>
