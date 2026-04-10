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
