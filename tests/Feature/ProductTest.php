<?php

use App\Models\Category;
use App\Models\Product;

test('product has fillable attributes', function () {
    $category = Category::factory()->create();
    $product = Product::factory()->for($category)->create([
        'name' => 'Pandesal',
        'price' => 5.00,
        'stock' => 100,
        'is_available' => true,
    ]);

    expect($product->name)->toBe('Pandesal')
        ->and($product->price)->toBe('5.00')
        ->and($product->stock)->toBe(100)
        ->and($product->is_available)->toBeTrue()
        ->and($product->category_id)->toBe($category->id);
});

test('product belongs to a category', function () {
    $category = Category::factory()->create(['name' => 'Bread']);
    $product = Product::factory()->for($category)->create();

    expect($product->category)->toBeInstanceOf(Category::class)
        ->and($product->category->name)->toBe('Bread');
});

test('product available scope filters correctly', function () {
    Product::factory()->count(2)->create(['is_available' => true]);
    Product::factory()->unavailable()->create();

    expect(Product::available()->count())->toBe(2);
});

test('product in stock scope filters correctly', function () {
    Product::factory()->count(2)->create(['stock' => 10]);
    Product::factory()->outOfStock()->create();

    expect(Product::inStock()->count())->toBe(2);
});

test('product isInStock method works correctly', function () {
    $available = Product::factory()->create(['is_available' => true, 'stock' => 10]);
    $unavailable = Product::factory()->unavailable()->create(['stock' => 10]);
    $outOfStock = Product::factory()->outOfStock()->create(['is_available' => true]);

    expect($available->isInStock())->toBeTrue()
        ->and($unavailable->isInStock())->toBeFalse()
        ->and($outOfStock->isInStock())->toBeFalse();
});

test('product casts attributes correctly', function () {
    $product = Product::factory()->create();

    expect($product->stock)->toBeInt()
        ->and($product->is_available)->toBeBool();
});
