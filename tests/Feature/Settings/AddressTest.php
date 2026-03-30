<?php

use App\Models\User;
use App\Models\UserAddress;
use Livewire\Livewire;

test('addresses page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('addresses.edit'))->assertOk();
});

test('user can add an address', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::settings.addresses')
        ->set('address', '123 Bakery Lane, Manila')
        ->set('label', 'Home')
        ->call('addAddress')
        ->assertHasNoErrors();

    expect($user->addresses()->count())->toBe(1);

    $addr = $user->addresses()->first();
    expect($addr->address)->toBe('123 Bakery Lane, Manila');
    expect($addr->label)->toBe('Home');
});

test('address field is required', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::settings.addresses')
        ->set('address', '')
        ->call('addAddress')
        ->assertHasErrors(['address' => 'required']);
});

test('user can set a default address', function () {
    $user = User::factory()->create();
    $first = UserAddress::factory()->for($user)->create(['is_default' => false]);
    $second = UserAddress::factory()->for($user)->create(['is_default' => false]);

    $this->actingAs($user);

    Livewire::test('pages::settings.addresses')
        ->call('setDefault', $second->id)
        ->assertHasNoErrors();

    expect($second->fresh()->is_default)->toBeTrue();
    expect($first->fresh()->is_default)->toBeFalse();
});

test('user can delete an address', function () {
    $user = User::factory()->create();
    $addr = UserAddress::factory()->for($user)->create();

    $this->actingAs($user);

    Livewire::test('pages::settings.addresses')
        ->call('deleteAddress', $addr->id)
        ->assertHasNoErrors();

    expect($addr->fresh())->toBeNull();
});

test('user cannot delete another users address', function () {
    $owner = User::factory()->create();
    $other = User::factory()->create();
    $addr = UserAddress::factory()->for($owner)->create();

    $this->actingAs($other);

    Livewire::test('pages::settings.addresses')
        ->call('deleteAddress', $addr->id);

    expect($addr->fresh())->not->toBeNull();
});
