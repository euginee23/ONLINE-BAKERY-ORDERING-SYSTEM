<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            'bakery_name' => 'ONLINE BAKERY ORDERING SYSTEM',
            'bakery_tagline' => 'Freshly baked goods delivered to your doorstep',
            'bakery_address' => '123 Baker Street, Manila, Philippines',
            'bakery_phone' => '+63 912 345 6789',
            'bakery_email' => 'info@bakerysystem.com',
        ];

        foreach ($settings as $key => $value) {
            Setting::query()->updateOrCreate(
                ['key' => $key],
                ['value' => $value],
            );
        }
    }
}
