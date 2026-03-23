<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SettingSeeder::class);
        $this->call(CategorySeeder::class);
        $this->call(ProductSeeder::class);

        User::factory()->admin()->create([
            'name' => 'Admin User',
            'email' => 'admin@bakery.test',
        ]);

        User::factory()->create([
            'name' => 'Test Customer',
            'email' => 'customer@bakery.test',
        ]);
    }
}
