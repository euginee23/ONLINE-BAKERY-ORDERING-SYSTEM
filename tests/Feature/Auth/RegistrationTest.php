<?php

use App\Enums\UserRole;

test('registration screen redirects to home', function () {
    $response = $this->get(route('register'));

    $response->assertRedirect(route('home'));
});

test('new users can register', function () {
    $response = $this->post(route('register.store'), [
        'name' => 'John Doe',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasNoErrors()
        ->assertRedirect(route('profile.edit', absolute: false));

    $this->assertAuthenticated();
});

test('new users are assigned the customer role on registration', function () {
    $this->post(route('register.store'), [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = \App\Models\User::where('email', 'jane@example.com')->firstOrFail();

    expect($user->role)->toBe(UserRole::Customer);
});
