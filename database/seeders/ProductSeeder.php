<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    public function run(): void
    {
        $products = [
            'Bread' => [
                ['name' => 'Pandesal', 'description' => 'Classic Filipino bread rolls, soft and fluffy.', 'price' => 5.00, 'stock' => 100],
                ['name' => 'Sourdough Loaf', 'description' => 'Artisan sourdough with a crispy crust and tangy flavor.', 'price' => 120.00, 'stock' => 20],
                ['name' => 'Wheat Bread', 'description' => 'Healthy whole wheat bread, perfect for sandwiches.', 'price' => 85.00, 'stock' => 30],
                ['name' => 'Garlic Bread', 'description' => 'Buttery garlic bread with herbs, great as a side.', 'price' => 65.00, 'stock' => 25],
            ],
            'Cakes' => [
                ['name' => 'Chocolate Cake', 'description' => 'Rich and moist chocolate cake with ganache frosting.', 'price' => 450.00, 'stock' => 10],
                ['name' => 'Ube Cake', 'description' => 'Classic Filipino purple yam cake with cream cheese frosting.', 'price' => 500.00, 'stock' => 8],
                ['name' => 'Mango Chiffon Cake', 'description' => 'Light and airy chiffon cake topped with fresh mangoes.', 'price' => 420.00, 'stock' => 12],
                ['name' => 'Red Velvet Cake', 'description' => 'Velvety red cake with cream cheese frosting.', 'price' => 480.00, 'stock' => 6],
            ],
            'Pastries' => [
                ['name' => 'Ensaymada', 'description' => 'Soft and buttery brioche-style pastry topped with cheese.', 'price' => 35.00, 'stock' => 50],
                ['name' => 'Croissant', 'description' => 'Flaky and buttery French croissant, baked fresh daily.', 'price' => 55.00, 'stock' => 40],
                ['name' => 'Danish Pastry', 'description' => 'Layered pastry with fruit filling and icing drizzle.', 'price' => 60.00, 'stock' => 30],
                ['name' => 'Cinnamon Roll', 'description' => 'Warm cinnamon roll with cream cheese glaze.', 'price' => 45.00, 'stock' => 35],
            ],
            'Cookies' => [
                ['name' => 'Chocolate Chip Cookies', 'description' => 'Classic cookies loaded with chocolate chips.', 'price' => 25.00, 'stock' => 60],
                ['name' => 'Oatmeal Raisin Cookies', 'description' => 'Chewy oatmeal cookies with plump raisins.', 'price' => 25.00, 'stock' => 45],
                ['name' => 'Peanut Butter Cookies', 'description' => 'Rich and crumbly peanut butter cookies.', 'price' => 28.00, 'stock' => 40],
                ['name' => 'Ube Crinkles', 'description' => 'Purple yam crinkle cookies, a Filipino favorite.', 'price' => 30.00, 'stock' => 50],
            ],
        ];

        foreach ($products as $categoryName => $items) {
            $category = Category::where('name', $categoryName)->first();

            if (! $category) {
                continue;
            }

            foreach ($items as $item) {
                Product::updateOrCreate(
                    ['name' => $item['name'], 'category_id' => $category->id],
                    array_merge($item, ['category_id' => $category->id]),
                );
            }
        }
    }
}
