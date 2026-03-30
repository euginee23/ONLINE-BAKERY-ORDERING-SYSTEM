<?php

use Laravel\Fortify\Features;

test('two factor authentication is not enabled', function () {
    expect(Features::canManageTwoFactorAuthentication())->toBeFalse();
});

test('two factor challenge route does not exist', function () {
    $this->get('/two-factor-challenge')
        ->assertNotFound();
});
