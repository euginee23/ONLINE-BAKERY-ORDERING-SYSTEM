<?php

use App\Models\User;
use Laravel\Fortify\Features;

test('email verification is not required', function () {
    expect(Features::enabled(Features::emailVerification()))->toBeFalse();
});

test('unverified users can access protected routes without verifying email', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});

test('unverified users can access settings', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('profile.edit'))
        ->assertOk();
});
