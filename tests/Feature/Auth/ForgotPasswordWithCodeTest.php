<?php

use App\Mail\PasswordResetCode;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Password;

test('forgot password screen can be rendered', function () {
    $this->get(route('password.request'))
        ->assertOk();
});

test('reset code is sent to registered email', function () {
    Mail::fake();

    $user = User::factory()->create();

    $this->post(route('password.code.send'), ['email' => $user->email])
        ->assertRedirect(route('password.code.verify'));

    Mail::assertSent(PasswordResetCode::class, fn ($mail) => $mail->hasTo($user->email));
    expect(session('password_reset_user_id'))->toBe($user->id);
});

test('reset code is not sent to unregistered email', function () {
    Mail::fake();

    $this->post(route('password.code.send'), ['email' => 'notregistered@example.com'])
        ->assertSessionHasErrors('email');

    Mail::assertNotSent(PasswordResetCode::class);
});

test('verify code screen requires active session', function () {
    $this->get(route('password.code.verify'))
        ->assertRedirect(route('password.request'));
});

test('verify code screen renders when session is active', function () {
    $user = User::factory()->create();

    session(['password_reset_user_id' => $user->id]);

    $this->get(route('password.code.verify'))
        ->assertOk();
});

test('valid code redirects to reset password page', function () {
    $user = User::factory()->create();

    Cache::put("password_reset_code_{$user->id}", '654321', now()->addMinutes(15));
    session(['password_reset_user_id' => $user->id]);

    $response = $this->post(route('password.code.verify.store'), ['code' => '654321']);

    $response->assertRedirect();
    $response->assertSessionHasNoErrors();

    expect(Cache::get("password_reset_code_{$user->id}"))->toBeNull();
    expect(session('password_reset_user_id'))->toBeNull();
});

test('invalid code returns error', function () {
    $user = User::factory()->create();

    Cache::put("password_reset_code_{$user->id}", '654321', now()->addMinutes(15));
    session(['password_reset_user_id' => $user->id]);

    $this->post(route('password.code.verify.store'), ['code' => '000000'])
        ->assertSessionHasErrors('code');
});

test('expired code returns error', function () {
    $user = User::factory()->create();

    // No cache entry — simulates expiry
    session(['password_reset_user_id' => $user->id]);

    $this->post(route('password.code.verify.store'), ['code' => '123456'])
        ->assertSessionHasErrors('code');
});

test('code verification fails without session', function () {
    $this->post(route('password.code.verify.store'), ['code' => '123456'])
        ->assertRedirect(route('password.request'));
});

test('password can be reset after valid code verification', function () {
    $user = User::factory()->create();

    $token = Password::broker()->createToken($user);

    $this->post(route('password.update'), [
        'token' => $token,
        'email' => $user->email,
        'password' => 'newpassword',
        'password_confirmation' => 'newpassword',
    ])->assertSessionHasNoErrors()
        ->assertRedirect();
});
