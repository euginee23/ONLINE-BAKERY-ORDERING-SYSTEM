<?php

use App\Models\Setting;

it('returns the default value when setting does not exist', function () {
    expect(Setting::get('nonexistent_key', 'fallback'))->toBe('fallback');
});

it('returns null when setting does not exist and no default is provided', function () {
    expect(Setting::get('nonexistent_key'))->toBeNull();
});

it('returns the stored value when setting exists', function () {
    Setting::create(['key' => 'test_key', 'value' => 'test_value']);

    expect(Setting::get('test_key'))->toBe('test_value');
});

it('can set and retrieve a setting', function () {
    Setting::set('new_key', 'new_value');

    expect(Setting::query()->where('key', 'new_key')->value('value'))->toBe('new_value');
});

it('updates existing setting when using set method', function () {
    Setting::create(['key' => 'update_key', 'value' => 'old_value']);

    Setting::set('update_key', 'updated_value');

    expect(Setting::query()->where('key', 'update_key')->value('value'))->toBe('updated_value');
});
