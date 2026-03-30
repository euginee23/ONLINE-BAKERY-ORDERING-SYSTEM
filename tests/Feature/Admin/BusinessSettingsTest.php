<?php

use App\Models\Setting;
use App\Models\User;
use Livewire\Livewire;

test('guests cannot access business settings', function () {
    $this->get(route('admin.business-settings'))
        ->assertRedirect(route('login'));
});

test('customers cannot access business settings', function () {
    $user = User::factory()->create();

    $this->actingAs($user)
        ->get(route('admin.business-settings'))
        ->assertForbidden();
});

test('admin can view business settings page', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin)
        ->get(route('admin.business-settings'))
        ->assertOk();
});

test('admin can save business settings', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire::test('pages::admin.business-settings')
        ->set('businessName', 'Sweet Bites Bakery')
        ->set('businessOwner', 'Maria Santos')
        ->set('contactNumber', '+63 917 123 4567')
        ->set('businessEmail', 'info@sweetbites.com')
        ->set('businessAddress', '123 Rizal Street, Marikina City')
        ->set('businessDescription', 'Fresh baked goods daily.')
        ->set('businessHours', 'Mon–Sat: 6:00 AM – 8:00 PM')
        ->call('save')
        ->assertHasNoErrors()
        ->assertDispatched('settings-saved');

    expect(Setting::get('business_name'))->toBe('Sweet Bites Bakery');
    expect(Setting::get('business_owner'))->toBe('Maria Santos');
    expect(Setting::get('contact_number'))->toBe('+63 917 123 4567');
    expect(Setting::get('business_email'))->toBe('info@sweetbites.com');
});

test('business name and owner and contact are required', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire::test('pages::admin.business-settings')
        ->set('businessName', '')
        ->set('businessOwner', '')
        ->set('contactNumber', '')
        ->call('save')
        ->assertHasErrors(['businessName', 'businessOwner', 'contactNumber']);
});

test('business email must be a valid email', function () {
    $admin = User::factory()->admin()->create();

    $this->actingAs($admin);

    Livewire::test('pages::admin.business-settings')
        ->set('businessName', 'Bakery')
        ->set('businessOwner', 'Owner')
        ->set('contactNumber', '123')
        ->set('businessEmail', 'not-an-email')
        ->call('save')
        ->assertHasErrors(['businessEmail']);
});
