<?php

use App\Models\User;
use App\Models\UserAddress;
use Livewire\Livewire;

test('addresses page is displayed', function () {
    $this->actingAs(User::factory()->create());

    $this->get(route('addresses.edit'))->assertOk();
});

test('user can add a philippine address', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::settings.addresses')
        ->set('houseStreet', '123 Rizal Street')
        ->set('barangay', 'San Jose')
        ->set('city', 'Marikina City')
        ->set('province', 'Metro Manila')
        ->set('region', 'NCR')
        ->set('zipCode', '1800')
        ->set('label', 'Home')
        ->call('addAddress')
        ->assertHasNoErrors();

    expect($user->addresses()->count())->toBe(1);

    $addr = $user->addresses()->first();
    expect($addr->house_street)->toBe('123 Rizal Street');
    expect($addr->barangay)->toBe('San Jose');
    expect($addr->city)->toBe('Marikina City');
    expect($addr->province)->toBe('Metro Manila');
    expect($addr->zip_code)->toBe('1800');
    expect($addr->label)->toBe('Home');
});

test('required address fields are validated', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::settings.addresses')
        ->set('houseStreet', '')
        ->set('barangay', '')
        ->set('city', '')
        ->set('province', '')
        ->set('zipCode', '')
        ->call('addAddress')
        ->assertHasErrors(['houseStreet', 'barangay', 'city', 'province', 'zipCode']);
});

test('zip code must be 4 digits', function () {
    $user = User::factory()->create();

    $this->actingAs($user);

    Livewire::test('pages::settings.addresses')
        ->set('houseStreet', '123 Rizal St')
        ->set('barangay', 'San Jose')
        ->set('city', 'Marikina City')
        ->set('province', 'Metro Manila')
        ->set('zipCode', '12345')
        ->call('addAddress')
        ->assertHasErrors(['zipCode']);
});

test('address formatted attribute returns correct string', function () {
    $addr = UserAddress::factory()->create([
        'house_street' => '123 Rizal Street',
        'barangay' => 'San Jose',
        'city' => 'Marikina City',
        'province' => 'Metro Manila',
        'region' => 'NCR',
        'zip_code' => '1800',
    ]);

    expect($addr->formatted)->toBe('123 Rizal Street, Brgy. San Jose, Marikina City, Metro Manila, NCR, 1800');
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
