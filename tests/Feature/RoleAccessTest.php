<?php

use App\Enums\UserRole;
use App\Models\User;

test('new users are assigned customer role by default', function () {
    $user = User::factory()->create();

    expect($user->role)->toBe(UserRole::Customer)
        ->and($user->isCustomer())->toBeTrue()
        ->and($user->isAdmin())->toBeFalse();
});

test('admin factory state creates admin user', function () {
    $admin = User::factory()->admin()->create();

    expect($admin->role)->toBe(UserRole::Admin)
        ->and($admin->isAdmin())->toBeTrue()
        ->and($admin->isCustomer())->toBeFalse();
});

test('user hasRole method works correctly', function () {
    $admin = User::factory()->admin()->create();
    $customer = User::factory()->create();

    expect($admin->hasRole('admin'))->toBeTrue()
        ->and($admin->hasRole('customer'))->toBeFalse()
        ->and($customer->hasRole('customer'))->toBeTrue()
        ->and($customer->hasRole('admin'))->toBeFalse()
        ->and($admin->hasRole('admin', 'customer'))->toBeTrue();
});

test('admin can access admin dashboard', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk();
});

test('customer cannot access admin dashboard', function () {
    $customer = User::factory()->create();

    $this->actingAs($customer)
        ->get(route('admin.dashboard'))
        ->assertForbidden();
});

test('guest cannot access admin dashboard', function () {
    $this->get(route('admin.dashboard'))
        ->assertRedirect(route('login'));
});

test('registration creates customer role by default', function () {
    $this->post(route('register.store'), [
        'name' => 'New Customer',
        'email' => 'newcustomer@bakery.test',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'newcustomer@bakery.test')->first();

    expect($user)->not->toBeNull()
        ->and($user->role)->toBe(UserRole::Customer);
});

test('user role enum has correct labels', function () {
    expect(UserRole::Admin->label())->toBe('Administrator')
        ->and(UserRole::Customer->label())->toBe('Customer');
});

test('user role enum has correct colors', function () {
    expect(UserRole::Admin->color())->toBe('amber')
        ->and(UserRole::Customer->color())->toBe('zinc');
});
