<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Bread',
                'description' => 'Freshly baked artisan breads made daily with premium ingredients.',
                'sort_order' => 1,
            ],
            [
                'name' => 'Cakes',
                'description' => 'Beautifully crafted cakes for every occasion, from birthdays to weddings.',
                'sort_order' => 2,
            ],
            [
                'name' => 'Pastries',
                'description' => 'Delicate pastries and flaky treats baked to golden perfection.',
                'sort_order' => 3,
            ],
            [
                'name' => 'Cookies',
                'description' => 'Handmade cookies in a variety of classic and creative flavors.',
                'sort_order' => 4,
            ],
        ];

        foreach ($categories as $category) {
            Category::updateOrCreate(
                ['name' => $category['name']],
                $category,
            );
        }
    }
}
