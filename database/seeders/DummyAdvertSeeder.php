<?php

namespace Database\Seeders;

use App\Models\State;
use App\Models\AdvertPlan;
use Illuminate\Support\Str;
use App\Models\AdvertBooking;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

class DummyAdvertSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
   
        public function run(): void
    {
        $plan = AdvertPlan::first();

        

        $states = State::all();

        foreach ($states as $state) {
            for ($i = 0; $i < 5; $i++) {
                $start = now()->addDays(rand(0, 10))->startOfDay();
                $end = (clone $start)->addDays($plan->duration_days)->endOfDay();

                AdvertBooking::create([
                    'state_id' => $state->id,
                    'plan_id' => $plan->id,
                    'reference' => 'DUMMY_' . strtoupper(Str::random(10)),
                    'title' => "Explore " . $state->name . " Deals",
                    'link' => 'https://example.com',
                    'image' => $this->getDummyImagePath(),
                    'starts_at' => $start,
                    'ends_at' => $end,
                    'amount' => 0.00,
                    'is_dummy' => true,
                ]);
            }
        }

        $this->command->info('Dummy adverts seeded successfully.');
    }

    protected function getDummyImagePath(): ?string
    {
        $dummyFile = public_path('images/default_advert.jpg');

        if (!file_exists($dummyFile)) {
            return null;
        }

        $filename = Str::uuid() . '.jpg';
        $storedPath = Storage::disk('public')->putFileAs('advert_images', $dummyFile, $filename);
        return $storedPath;
    }
   
}
