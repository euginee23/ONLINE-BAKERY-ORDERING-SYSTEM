<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductSeeder extends Seeder
{
    /**
     * Maps each product name to an Unsplash photo ID for a relevant bakery image.
     *
     * @var array<string, string>
     */
    private array $imageIds = [
        'Pandesal' => 'photo-1619566636858-adf3ef46400b', // bread rolls
        'Sourdough Loaf' => 'photo-1509440159596-0249088772ff', // sourdough
        'Wheat Bread' => 'photo-1549931319-a545dcf3bc7c', // wheat bread
        'Garlic Bread' => 'photo-1573140247632-f8fd74997d5c', // garlic bread
        'Chocolate Cake' => 'photo-1578985545062-69928b1d9587', // chocolate cake
        'Ube Cake' => 'photo-1464349095431-e9a21285b5f3', // purple cake
        'Mango Chiffon Cake' => 'photo-1618426703623-c1b335803f96', // mango cake
        'Red Velvet Cake' => 'photo-1586788680434-30d324b2d46f', // red velvet
        'Ensaymada' => 'photo-1555507036-ab1f4038808a', // brioche bun
        'Croissant' => 'photo-1555507036-ab1f4038808a', // croissant
        'Danish Pastry' => 'photo-1558961363-fa8fdf82db35', // danish
        'Cinnamon Roll' => 'photo-1609085583085-26a70a440d3c', // cinnamon roll
        'Chocolate Chip Cookies' => 'photo-1499636136210-6f4ee915583e', // choc chip
        'Oatmeal Raisin Cookies' => 'photo-1558961363-fa8fdf82db35', // oatmeal cookie
        'Peanut Butter Cookies' => 'photo-1558961363-fa8fdf82db35', // pb cookies
        'Ube Crinkles' => 'photo-1464349095431-e9a21285b5f3', // crinkles
    ];

    public function run(): void
    {
        Storage::disk('public')->makeDirectory('products');

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
                $imagePath = $this->downloadImage($item['name']);

                Product::updateOrCreate(
                    ['name' => $item['name'], 'category_id' => $category->id],
                    array_merge(
                        $item,
                        ['category_id' => $category->id],
                        $imagePath ? ['image_path' => $imagePath] : [],
                    ),
                );
            }
        }
    }

    private function downloadImage(string $productName): ?string
    {
        $photoId = $this->imageIds[$productName] ?? null;

        if (! $photoId) {
            return null;
        }

        $url = "https://images.unsplash.com/{$photoId}?w=600&q=80&auto=format&fit=crop";

        try {
            $response = Http::timeout(15)->get($url);

            if (! $response->successful()) {
                return null;
            }

            $slug = Str::slug($productName);
            $filename = "products/{$slug}.jpg";

            Storage::disk('public')->put($filename, $response->body());

            return $filename;
        } catch (\Throwable) {
            return null;
        }
    }
}
