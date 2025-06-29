<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AdvertPlansTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        $plans = [
            [
                'id' => Str::uuid(),
                'name' => 'Basic Plan',
                'duration_days' => 7,
                'price' => 9.99,
                'description' => 'A basic plan for short-term visibility.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Standard Plan',
                'duration_days' => 30,
                'price' => 29.99,
                'description' => 'Best for consistent monthly exposure.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
            [
                'id' => Str::uuid(),
                'name' => 'Premium Plan',
                'duration_days' => 90,
                'price' => 79.99,
                'description' => 'High-value plan with extended visibility.',
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now(),
            ],
        ];

        DB::table('advert_plans')->insert($plans);
    }
}
