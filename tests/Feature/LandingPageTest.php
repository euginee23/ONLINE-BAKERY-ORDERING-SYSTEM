<?php

use App\Models\Setting;
use App\Models\User;

beforeEach(function () {
    Setting::create(['key' => 'bakery_name', 'value' => 'ONLINE BAKERY ORDERING SYSTEM']);
});

it('loads the landing page successfully', function () {
    $response = $this->get(route('home'));

    $response->assertOk();
});

it('displays the bakery name from settings', function () {
    $response = $this->get(route('home'));

    $response->assertSee('ONLINE BAKERY ORDERING SYSTEM');
});

it('displays all landing page sections', function () {
    $response = $this->get(route('home'));

    $response->assertSee('Fresh from the Oven');
    $response->assertSee('Our Products');
    $response->assertSee('How It Works');
    $response->assertSee('Why Choose Us');
    $response->assertSee('Ready to Order?');
});

it('displays menu categories', function () {
    $response = $this->get(route('home'));

    $response->assertSee('Bread');
    $response->assertSee('Cakes');
    $response->assertSee('Pastries');
    $response->assertSee('Cookies');
});

it('displays feature cards', function () {
    $response = $this->get(route('home'));

    $response->assertSee('Online Ordering');
    $response->assertSee('Real-time Inventory');
    $response->assertSee('Order Confirmation');
    $response->assertSee('Admin Dashboard');
});

it('shows login and register modals markup for guests', function () {
    $response = $this->get(route('home'));

    $response->assertSee('open-login-modal');
    $response->assertSee('open-register-modal');
    $response->assertSee('Welcome back');
    $response->assertSee('Create an account');
});

it('does not show auth modals for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertDontSee('Welcome back');
    $response->assertDontSee('Create an account');
});

it('shows dashboard link for authenticated users', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get(route('home'));

    $response->assertSee('Dashboard');
});

it('displays a custom bakery name when setting is changed', function () {
    Setting::set('bakery_name', 'Sweet Delights Bakery');

    $response = $this->get(route('home'));

    $response->assertSee('Sweet Delights Bakery');
});
