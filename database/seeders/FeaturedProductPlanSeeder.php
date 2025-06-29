<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class FeaturedProductPlanSeeder extends Seeder
{
    public function run()
    {
        $now = Carbon::now();

        $plans = [
            [
                'id' => (string) Str::uuid(),
                'name' => 'Basic Plan',
                'duration_days' => 7,
                'price' => 1000.00,
                'description' => 'Feature your product for 7 days.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Standard Plan',
                'duration_days' => 14,
                'price' => 1800.00,
                'description' => 'Feature your product for 14 days.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'id' => (string) Str::uuid(),
                'name' => 'Premium Plan',
                'duration_days' => 30,
                'price' => 3500.00,
                'description' => 'Feature your product for 30 days.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ];

        DB::table('featured_product_plans')->insert($plans);
    }
}
