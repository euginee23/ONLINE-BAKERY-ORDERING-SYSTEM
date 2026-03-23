<?php

use App\Models\Category;
use App\Models\User;
use Livewire\Livewire;

beforeEach(function () {
    $this->admin = User::factory()->admin()->create();
    $this->customer = User::factory()->create();
});

test('admin can access categories page', function () {
    $this->actingAs($this->admin)
        ->get(route('admin.categories.index'))
        ->assertOk();
});

test('customer cannot access categories page', function () {
    $this->actingAs($this->customer)
        ->get(route('admin.categories.index'))
        ->assertForbidden();
});

test('guest cannot access categories page', function () {
    $this->get(route('admin.categories.index'))
        ->assertRedirect(route('login'));
});

test('admin can create a category', function () {
    Livewire::actingAs($this->admin)
        ->test('pages::admin.categories.index')
        ->call('create')
        ->assertSet('showModal', true)
        ->set('name', 'New Category')
        ->set('description', 'A new category for testing')
        ->set('is_active', true)
        ->set('sort_order', 1)
        ->call('save')
        ->assertSet('showModal', false);

    $this->assertDatabaseHas('categories', [
        'name' => 'New Category',
        'description' => 'A new category for testing',
        'is_active' => true,
        'sort_order' => 1,
    ]);
});

test('admin can edit a category', function () {
    $category = Category::factory()->create(['name' => 'Old Name']);

    Livewire::actingAs($this->admin)
        ->test('pages::admin.categories.index')
        ->call('edit', $category->id)
        ->assertSet('editingId', $category->id)
        ->assertSet('name', 'Old Name')
        ->set('name', 'Updated Name')
        ->call('save')
        ->assertSet('showModal', false);

    expect($category->fresh()->name)->toBe('Updated Name');
});

test('admin can delete a category', function () {
    $category = Category::factory()->create();

    Livewire::actingAs($this->admin)
        ->test('pages::admin.categories.index')
        ->call('confirmDelete', $category->id)
        ->assertSet('showDeleteModal', true)
        ->call('delete');

    $this->assertDatabaseMissing('categories', ['id' => $category->id]);
});

test('category name is required', function () {
    Livewire::actingAs($this->admin)
        ->test('pages::admin.categories.index')
        ->call('create')
        ->set('name', '')
        ->call('save')
        ->assertHasErrors(['name' => 'required']);
});

test('categories are listed in the table', function () {
    Category::factory()->create(['name' => 'Bread']);
    Category::factory()->create(['name' => 'Cakes']);

    Livewire::actingAs($this->admin)
        ->test('pages::admin.categories.index')
        ->assertSee('Bread')
        ->assertSee('Cakes');
});
