<?php

namespace Database\Seeders;

use App\Models\Stage;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\File;


class StageSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $json = File::get('database/json/stages.json');
        $data = json_decode($json);

        foreach ($data->stages as $stage) {
            Stage::create([
                'day_id'=> $stage->day_id,
                'name'=> $stage->name,
                'slug'=> $stage->slug,
                'cover'=> $stage->cover,
                'notes'=> $stage->notes,
                'description'=> $stage->description,
                'curiosities'=> $stage->curiosities,
                'rating'=> $stage->rating,
                'address'=> $stage->address,
                'lat'=> $stage->lat,
                'lon'=> $stage->lon
            ]);
        }
    }
}
