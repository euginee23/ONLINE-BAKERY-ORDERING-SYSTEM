<?php

use App\Models\Category;
use App\Models\Product;

test('category has fillable attributes', function () {
    $category = Category::factory()->create([
        'name' => 'Test Category',
        'description' => 'A test description',
        'is_active' => true,
        'sort_order' => 5,
    ]);

    expect($category->name)->toBe('Test Category')
        ->and($category->description)->toBe('A test description')
        ->and($category->is_active)->toBeTrue()
        ->and($category->sort_order)->toBe(5);
});

test('category casts attributes correctly', function () {
    $category = Category::factory()->create();

    expect($category->is_active)->toBeBool()
        ->and($category->sort_order)->toBeInt();
});

test('category has many products', function () {
    $category = Category::factory()->create();
    Product::factory()->count(3)->for($category)->create();

    expect($category->products)->toHaveCount(3)
        ->each->toBeInstanceOf(Product::class);
});

test('inactive factory state sets is_active to false', function () {
    $category = Category::factory()->inactive()->create();

    expect($category->is_active)->toBeFalse();
});

test('deleting category deletes associated products', function () {
    $category = Category::factory()->create();
    Product::factory()->count(2)->for($category)->create();

    expect(Product::count())->toBe(2);

    $category->delete();

    expect(Product::count())->toBe(0);
});
