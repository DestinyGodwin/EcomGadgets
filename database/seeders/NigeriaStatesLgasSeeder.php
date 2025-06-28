<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NigeriaStatesLgasSeeder extends Seeder
{
    public function run(): void
    {
        $json = file_get_contents(database_path('data/states.json'));
        $states = json_decode($json, true);

        foreach ($states as $entry) {
            $stateName = trim($entry['state']);
            $stateSlug = trim($entry['alias']);
            $stateId = Str::uuid();

            DB::table('states')->insert([
                'id' => $stateId,
                'name' => $stateName,
                'slug' => $stateSlug,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            $uniqueLgas = collect($entry['lgas'])
                ->map(fn($lga) => ucwords(strtolower(trim($lga))))
                ->unique();

            foreach ($uniqueLgas as $lgaName) {
                $lgaSlug = Str::slug($stateSlug . '-' . $lgaName);

                DB::table('lgas')->insert([
                    'id' => Str::uuid(),
                    'state_id' => $stateId,
                    'name' => $lgaName,
                    'slug' => $lgaSlug,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }
    }
}
