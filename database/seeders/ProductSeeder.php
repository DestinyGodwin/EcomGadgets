<?php

namespace Database\Seeders;

use App\Models\Store;
use App\Models\Product;
use App\Models\Category;
use App\Models\ProductImage;
use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Faker\Factory as Faker;


class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faker = Faker::create();

        // Get some existing stores and categories
        $storeIds = Store::pluck('id');
        $categoryIds = Category::pluck('id');

        if ($storeIds->isEmpty() || $categoryIds->isEmpty()) {
            $this->command->warn('No stores or categories found. Skipping product seeding.');
            return;
        }

        foreach (range(1, 20) as $i) {
            $name = $faker->words(3, true);
            // $slug = Str::slug($name . '-' . Str::random(5));

            $product = Product::create([
                // 'id' => Str::uuid(),
                'store_id' => $faker->randomElement($storeIds),
                'category_id' => $faker->randomElement($categoryIds),
                'name' => $name,
                // 'slug' => $slug,
                'description' => $faker->paragraph,
                'specifications' => json_encode([
                    'color' => $faker->safeColorName(),
                    'weight' => $faker->randomFloat(2, 0.1, 10) . ' kg',
                    'dimensions' => $faker->randomNumber(2) . 'x' . $faker->randomNumber(2) . 'x' . $faker->randomNumber(2) . ' cm',
                ]),
                'brand' => $faker->company,
                'price' => $faker->randomFloat(2, 10, 500),
                'wholesale_price' => $faker->randomFloat(2, 5, 200),
                'is_featured' => $faker->boolean,
                'featured_expires_at' => $faker->optional()->dateTimeBetween('now', '+1 month'),
            ]);

            // Add 1–3 images per product
            foreach (range(1, rand(1, 3)) as $j) {
                ProductImage::create([
                    // 'id' => Str::uuid(),
                    'product_id' => $product->id,
                    'image_path' => 'products/sample-' . rand(1, 10) . '.jpg',
                ]);
            }
        }
    }
}
