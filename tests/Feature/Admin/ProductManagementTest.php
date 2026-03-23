<?php

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->customer = User::factory()->create();
    $this->category = Category::factory()->create();
});

test('admin can access products page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.products.index'))
        ->assertOk();
});

test('customer cannot access products page', function () {
    $this->actingAs($this->customer)
        ->get(route('admin.products.index'))
        ->assertForbidden();
});

test('guest cannot access products page', function () {
    $this->get(route('admin.products.index'))
        ->assertRedirect(route('login'));
});

test('admin can create a product', function () {
    Livewire::actingAs($this->admin)
        ->test('pages::admin.products.index')
        ->call('create')
        ->assertSet('showModal', true)
        ->set('name', 'Test Pandesal')
        ->set('category_id', $this->category->id)
        ->set('price', '5.00')
        ->set('stock', 100)
        ->set('is_available', true)
        ->call('save')
        ->assertSet('showModal', false);

    $this->assertDatabaseHas('products', [
        'name' => 'Test Pandesal',
        'category_id' => $this->category->id,
        'price' => '5.00',
        'stock' => 100,
    ]);
});

test('admin can edit a product', function () {
    $product = Product::factory()->for($this->category)->create(['name' => 'Old Product']);

    Livewire::actingAs($this->admin)
        ->test('pages::admin.products.index')
        ->call('edit', $product->id)
        ->assertSet('editingId', $product->id)
        ->assertSet('name', 'Old Product')
        ->set('name', 'Updated Product')
        ->call('save')
        ->assertSet('showModal', false);

    expect($product->fresh()->name)->toBe('Updated Product');
});

test('admin can delete a product', function () {
    $product = Product::factory()->for($this->category)->create();

    Livewire::actingAs($this->admin)
        ->test('pages::admin.products.index')
        ->call('confirmDelete', $product->id)
        ->assertSet('showDeleteModal', true)
        ->call('delete');

    $this->assertDatabaseMissing('products', ['id' => $product->id]);
});

test('product name and category are required', function () {
    Livewire::actingAs($this->admin)
        ->test('pages::admin.products.index')
        ->call('create')
        ->set('name', '')
        ->set('category_id', null)
        ->set('price', '')
        ->call('save')
        ->assertHasErrors(['name', 'category_id', 'price']);
});

test('products are listed in the table', function () {
    Product::factory()->for($this->category)->create(['name' => 'Pandesal']);
    Product::factory()->for($this->category)->create(['name' => 'Ensaymada']);

    Livewire::actingAs($this->admin)
        ->test('pages::admin.products.index')
        ->assertSee('Pandesal')
        ->assertSee('Ensaymada');
});

test('products can be filtered by search', function () {
    Product::factory()->for($this->category)->create(['name' => 'Pandesal']);
    Product::factory()->for($this->category)->create(['name' => 'Ensaymada']);

    Livewire::actingAs($this->admin)
        ->test('pages::admin.products.index')
        ->set('search', 'Pandesal')
        ->assertSee('Pandesal')
        ->assertDontSee('Ensaymada');
});

test('products can be filtered by category', function () {
    $otherCategory = Category::factory()->create(['name' => 'Pastries']);
    Product::factory()->for($this->category)->create(['name' => 'Filipino Bread']);
    Product::factory()->for($otherCategory)->create(['name' => 'Croissant']);

    Livewire::actingAs($this->admin)
        ->test('pages::admin.products.index')
        ->set('categoryFilter', (string) $otherCategory->id)
        ->assertSee('Croissant')
        ->assertDontSee('Filipino Bread');
});
