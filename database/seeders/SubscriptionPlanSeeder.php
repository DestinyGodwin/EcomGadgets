<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SubscriptionPlan;

class SubscriptionPlanSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
          $plans = [
            ['name' => '1 Month', 'duration_days' => 30, 'price' => 9.99],
            ['name' => '2 Months', 'duration_days' => 60, 'price' => 18.99],
            ['name' => '3 Months', 'duration_days' => 90, 'price' => 26.99],
            ['name' => '4 Months', 'duration_days' => 120, 'price' => 34.99],
            ['name' => '5 Months', 'duration_days' => 150, 'price' => 42.99],
            ['name' => '6 Months', 'duration_days' => 180, 'price' => 49.99],
            ['name' => '7 Months', 'duration_days' => 210, 'price' => 56.99],
            ['name' => '8 Months', 'duration_days' => 240, 'price' => 63.99],
            ['name' => '9 Months', 'duration_days' => 270, 'price' => 69.99],
            ['name' => '10 Months', 'duration_days' => 300, 'price' => 74.99],
            ['name' => '11 Months', 'duration_days' => 330, 'price' => 79.99],
            ['name' => '12 Months', 'duration_days' => 365, 'price' => 84.99],
        ];
        foreach ($plans as $plan) {
            SubscriptionPlan::create([
                'name' => $plan['name'],
                'duration_days' => $plan['duration_days'],
                'price' => $plan['price'],
                'description' => 'Subscription for ' . $plan['name'],
           
            ]);
        }
    }
}
