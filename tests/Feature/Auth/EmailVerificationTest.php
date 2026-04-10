<?php

use App\Mail\VerifyEmailCode;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Mail;
use Laravel\Fortify\Features;

test('email verification feature is enabled', function () {
    expect(Features::enabled(Features::emailVerification()))->toBeTrue();
});

test('email verification screen can be rendered', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('verification.notice'))
        ->assertOk();
});

test('unverified customers are redirected to verification page', function () {
    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertRedirect(route('verification.notice'));
});

test('verification code email is sent on registration', function () {
    Mail::fake();

    $this->post(route('register.store'), [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    Mail::assertSent(VerifyEmailCode::class, function ($mail) {
        return $mail->hasTo('test@example.com');
    });
});

test('email can be verified with valid code', function () {
    $user = User::factory()->unverified()->create();

    Cache::put("email_verify_code_{$user->id}", '123456', now()->addMinutes(10));

    $this->actingAs($user)
        ->post(route('verification.code'), ['code' => '123456'])
        ->assertRedirect(config('fortify.home'));

    expect($user->fresh()->hasVerifiedEmail())->toBeTrue();
});

test('email cannot be verified with invalid code', function () {
    $user = User::factory()->unverified()->create();

    Cache::put("email_verify_code_{$user->id}", '123456', now()->addMinutes(10));

    $this->actingAs($user)
        ->post(route('verification.code'), ['code' => '999999'])
        ->assertSessionHasErrors('code');

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('email cannot be verified with expired code', function () {
    $user = User::factory()->unverified()->create();

    // Don't put any code in cache — simulates expiry

    $this->actingAs($user)
        ->post(route('verification.code'), ['code' => '123456'])
        ->assertSessionHasErrors('code');

    expect($user->fresh()->hasVerifiedEmail())->toBeFalse();
});

test('resend sends a new verification code', function () {
    Mail::fake();

    $user = User::factory()->unverified()->create();

    $this->actingAs($user)
        ->post(route('verification.send'))
        ->assertRedirect();

    Mail::assertSent(VerifyEmailCode::class, function ($mail) use ($user) {
        return $mail->hasTo($user->email);
    });
});

test('admin routes do not require email verification', function () {
    $admin = User::factory()->unverified()->create(['role' => \App\Enums\UserRole::Admin]);

    $this->actingAs($admin)
        ->get(route('admin.dashboard'))
        ->assertOk();
});

test('verified users can access customer routes', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('dashboard'))
        ->assertOk();
});
