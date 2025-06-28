<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            'Smartphones',
            'Laptops & Computers',
            'Tablets',
            'Smartwatches & Wearables',
            'Phone Accessories',
            'Computer Accessories',
            'Chargers & Cables',
            'Headphones & Earbuds',
            'Televisions',
            'Gaming Consoles & Accessories',
            'Smart Home Devices',
            'Cameras & Photography',
            'Printers & Scanners',
            'Power Banks & Batteries',
            'Networking Devices',
            'Monitors & Displays',
            'Drones & RC Electronics',
            'Car Electronics',
            'Home Audio Systems',
            'Projectors',
        ];

        foreach ($categories as $name) {
            Category::create([
                'name' => $name,
            ]);
        }
    }
}
