<?php

namespace Database\Factories;

use App\Models\User;
use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Store>
 */
class StoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
       
        return [
            'user_id' => User::factory(),
            'store_name' => $this->faker->company,
            'slug' => Str::slug($this->faker->company . '-' . Str::uuid()),
            'store_description' => $this->faker->paragraph,
            'store_image' => $this->faker->imageUrl(640, 480, 'business'),
            'phone' => $this->faker->phoneNumber,
            'email' => $this->faker->companyEmail,
            'state_id' => '8b9a382f-f3f7-49ef-8068-20fb4e321252', 
            'lga_id' => '00c8dcad-e463-4a06-9d16-b578fc61931f',
            'address' => $this->faker->address,
            'subscription_expires_at' => now()->addDays(7),
            'is_active' => true,
        ];
    }
}
