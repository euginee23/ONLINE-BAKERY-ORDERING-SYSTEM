<?php

use App\Models\User;
use Laravel\Fortify\Features;

test('two factor authentication is not enabled', function () {
    expect(Features::canManageTwoFactorAuthentication())->toBeFalse();
});

test('two factor settings route is not accessible', function () {
    $this->actingAs(User::factory()->create())
        ->withSession(['auth.password_confirmed_at' => time()])
        ->get('/settings/two-factor')
        ->assertNotFound();
});
