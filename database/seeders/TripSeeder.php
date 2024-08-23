<?php

namespace Database\Seeders;

use App\Models\Trip;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;

class TripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // leggere il file json
        $json = File::get('database/json/trips.json');
        $data = json_decode($json);

        foreach ($data->trips as $trip) {
            Trip::create([
                'name' => $trip->name,
                'slug' => $trip->slug,
                'cover' => $trip->cover,
                'description' => $trip->description,
                'notes' => $trip->notes,
                'startDate' => $trip->startDate,
                'endDate' => $trip->endDate
            ]);
        }
    }
}
