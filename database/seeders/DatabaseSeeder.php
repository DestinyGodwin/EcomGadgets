<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\CategorySeeder;
use Database\Seeders\AdvertPlanSeeder;
use Database\Seeders\SubscriptionPlanSeeder;
use Database\Seeders\NigeriaStatesLgasSeeder;
use Database\Seeders\FeaturedProductPlanSeeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);
         $this->call([
        NigeriaStatesLgasSeeder::class,
        SubscriptionPlanSeeder::class,
        CategorySeeder::class,
        FeaturedProductPlanSeeder::class,
        AdvertPlanSeeder::class,
        
    ]);
    }
}
