<?php

use App\Exports\CategorySalesExport;
use App\Exports\CustomerReportExport;
use App\Exports\OrdersReportExport;
use App\Exports\ProductSalesExport;
use App\Exports\SalesSummaryExport;
use App\Models\Category;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\User;
use Maatwebsite\Excel\Facades\Excel;

test('guests cannot access admin reports', function () {
    $this->get(route('admin.reports.index'))
        ->assertRedirect(route('login'));
});

test('customers cannot access admin reports', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.reports.index'))
        ->assertForbidden();
});

test('admin can view reports page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.reports.index'))
        ->assertOk()
        ->assertSee('Reports');
});

test('admin can see sales summary preview', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();
    $product = Product::factory()->create();
    $order = Order::factory()->for($customer)->create([
        'status' => 'completed',
        'total_amount' => 500.00,
    ]);
    OrderItem::factory()->for($order)->for($product)->create([
        'quantity' => 2,
        'unit_price' => 250.00,
        'subtotal' => 500.00,
    ]);

    $this->actingAs($admin)
        ->get(route('admin.reports.index'))
        ->assertOk()
        ->assertSee('₱500.00');
});

test('admin can export sales summary report', function () {
    $this->freezeTime();
    Excel::fake();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire\Livewire::test('pages::admin.reports.index')
        ->set('reportType', 'sales_summary')
        ->call('export');

    Excel::assertDownloaded('sales_summary_'.now()->format('Y-m-d_His').'.xlsx');
});

test('admin can export orders report', function () {
    $this->freezeTime();
    Excel::fake();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire\Livewire::test('pages::admin.reports.index')
        ->set('reportType', 'orders')
        ->call('export');

    Excel::assertDownloaded('orders_'.now()->format('Y-m-d_His').'.xlsx');
});

test('admin can export product sales report', function () {
    $this->freezeTime();
    Excel::fake();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire\Livewire::test('pages::admin.reports.index')
        ->set('reportType', 'product_sales')
        ->call('export');

    Excel::assertDownloaded('product_sales_'.now()->format('Y-m-d_His').'.xlsx');
});

test('admin can export category sales report', function () {
    $this->freezeTime();
    Excel::fake();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire\Livewire::test('pages::admin.reports.index')
        ->set('reportType', 'category_sales')
        ->call('export');

    Excel::assertDownloaded('category_sales_'.now()->format('Y-m-d_His').'.xlsx');
});

test('admin can export customer report', function () {
    $this->freezeTime();
    Excel::fake();

    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire\Livewire::test('pages::admin.reports.index')
        ->set('reportType', 'customers')
        ->call('export');

    Excel::assertDownloaded('customers_'.now()->format('Y-m-d_His').'.xlsx');
});

test('sales summary export returns correct data', function () {
    $customer = User::factory()->create();
    $product = Product::factory()->create();
    $order = Order::factory()->for($customer)->create([
        'status' => 'completed',
        'total_amount' => 300.00,
    ]);
    OrderItem::factory()->for($order)->for($product)->create([
        'subtotal' => 300.00,
    ]);

    $export = new SalesSummaryExport;
    $collection = $export->collection();

    expect($collection)->toHaveCount(1);
    expect((float) $collection->first()->revenue)->toBe(300.00);
});

test('orders report export filters by status', function () {
    $customer = User::factory()->create();
    Order::factory()->for($customer)->create(['status' => 'completed']);
    Order::factory()->for($customer)->create(['status' => 'pending']);

    $export = new OrdersReportExport(status: 'completed');
    $collection = $export->collection();

    expect($collection)->toHaveCount(1);
    expect($collection->first()->status->value)->toBe('completed');
});

test('product sales export returns products ranked by revenue', function () {
    $customer = User::factory()->create();
    $productA = Product::factory()->create();
    $productB = Product::factory()->create();

    $order = Order::factory()->for($customer)->create(['status' => 'completed']);
    OrderItem::factory()->for($order)->for($productA)->create(['subtotal' => 100.00]);
    OrderItem::factory()->for($order)->for($productB)->create(['subtotal' => 500.00]);

    $export = new ProductSalesExport;
    $collection = $export->collection();

    expect($collection)->toHaveCount(2);
    expect($collection->first()->product_id)->toBe($productB->id);
});

test('category sales export groups by category', function () {
    $customer = User::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create();

    $order = Order::factory()->for($customer)->create(['status' => 'completed']);
    OrderItem::factory()->for($order)->for($product)->create(['subtotal' => 200.00]);

    $export = new CategorySalesExport;
    $collection = $export->collection();

    expect($collection)->toHaveCount(1);
    expect($collection->first()->category_name)->toBe($category->name);
});

test('customer report export only includes customers with orders', function () {
    $customerWithOrder = User::factory()->create();
    User::factory()->create(); // customer without orders

    Order::factory()->for($customerWithOrder)->create(['status' => 'completed', 'total_amount' => 100.00]);

    $export = new CustomerReportExport;
    $collection = $export->collection();

    expect($collection)->toHaveCount(1);
    expect($collection->first()->id)->toBe($customerWithOrder->id);
});

test('cancelled orders are excluded from sales summary', function () {
    $customer = User::factory()->create();
    Order::factory()->for($customer)->create(['status' => 'completed', 'total_amount' => 200.00]);
    Order::factory()->for($customer)->create(['status' => 'cancelled', 'total_amount' => 100.00]);

    $export = new SalesSummaryExport;
    $collection = $export->collection();

    expect((float) $collection->first()->revenue)->toBe(200.00);
});

test('reports page filters by date range', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire\Livewire::test('pages::admin.reports.index')
        ->set('dateFrom', '2026-01-01')
        ->set('dateTo', '2026-01-31')
        ->assertOk();
});

test('report type change resets filters', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire\Livewire::test('pages::admin.reports.index')
        ->set('statusFilter', 'completed')
        ->set('orderTypeFilter', 'delivery')
        ->set('reportType', 'product_sales')
        ->assertSet('statusFilter', '')
        ->assertSet('orderTypeFilter', '');
});

test('overall stats returns all expected keys', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();

    Order::factory()->for($customer)->create(['status' => 'completed', 'total_amount' => 100.00, 'type' => 'delivery']);
    Order::factory()->for($customer)->create(['status' => 'pending', 'total_amount' => 50.00, 'type' => 'pickup']);
    Order::factory()->for($customer)->create(['status' => 'cancelled', 'total_amount' => 30.00, 'type' => 'delivery']);

    $this->actingAs($admin);

    $component = Livewire\Livewire::test('pages::admin.reports.index')
        ->set('dateFrom', '')
        ->set('dateTo', '');

    $stats = $component->viewData('overallStats');

    expect($stats)->toHaveKeys(['revenue', 'activeOrders', 'avgOrderValue', 'pending', 'processing', 'ready', 'completed', 'cancelled', 'deliveryCount', 'pickupCount']);
    expect((float) $stats['revenue'])->toBe(150.00);
    expect($stats['cancelled'])->toBe(1);
    expect($stats['deliveryCount'])->toBe(1);
    expect($stats['pickupCount'])->toBe(1);
});

test('customer report export includes last order column', function () {
    $customer = User::factory()->create();

    Order::factory()->for($customer)->create(['status' => 'completed', 'total_amount' => 100.00]);

    $export = new CustomerReportExport;

    expect($export->headings())->toContain('Last Order');

    $collection = $export->collection();
    $mapped = $export->map($collection->first());

    expect($mapped)->toHaveCount(7);
    expect($mapped[5])->not->toBeEmpty(); // Last Order date
});

test('orders report export includes time and items ordered columns', function () {
    $customer = User::factory()->create();
    $product = Product::factory()->create(['name' => 'Croissant']);
    $order = Order::factory()->for($customer)->create(['status' => 'completed', 'total_amount' => 100.00]);
    OrderItem::factory()->for($order)->for($product)->create(['quantity' => 2, 'subtotal' => 100.00]);

    $export = new OrdersReportExport;
    $headings = $export->headings();

    expect($headings)->toContain('Time');
    expect($headings)->toContain('Items Ordered');
    expect($headings)->toContain('Notes');
    expect($headings)->toHaveCount(11);

    $collection = $export->collection();
    $mapped = $export->map($collection->first());

    expect($mapped[8])->toContain('Croissant'); // Items Ordered column
});

test('sales summary export includes delivery and pickup columns', function () {
    $customer = User::factory()->create();
    Order::factory()->for($customer)->create(['status' => 'completed', 'total_amount' => 200.00, 'type' => 'delivery']);

    $export = new SalesSummaryExport;
    $headings = $export->headings();

    expect($headings)->toContain('Delivery');
    expect($headings)->toContain('Pickup');
    expect($headings)->toContain('Cancelled');
    expect($headings)->toHaveCount(7);
});

test('product sales export includes percentage share column', function () {
    $customer = User::factory()->create();
    $product = Product::factory()->create();
    $order = Order::factory()->for($customer)->create(['status' => 'completed']);
    OrderItem::factory()->for($order)->for($product)->create(['subtotal' => 300.00]);

    $export = new ProductSalesExport;

    expect($export->headings())->toContain('% Share');
    expect($export->headings())->toContain('Stock');
    expect($export->headings())->toHaveCount(8);
});

test('category sales export includes percentage share column', function () {
    $customer = User::factory()->create();
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create();
    $order = Order::factory()->for($customer)->create(['status' => 'completed']);
    OrderItem::factory()->for($order)->for($product)->create(['subtotal' => 200.00]);

    $export = new CategorySalesExport;

    expect($export->headings())->toContain('% Share');
    expect($export->headings())->toHaveCount(6);
});
