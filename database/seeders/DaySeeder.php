<?php

namespace Database\Seeders;

use App\Models\Day;
use App\Models\Trip;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class DaySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Recupero tutti i viaggi
        $trips = Trip::all();

        // Per ogni viaggio
        foreach ($trips as $trip) {
            // Uso Carbon per trasformare le stringhe in date per le date di inizio e fine, per ogni viaggio esistente
            $startDate = Carbon::parse($trip->startDate);
            $endDate = Carbon::parse($trip->endDate);

            // Per ogni data creo un dato con lo stesso id del viaggio a cui corrisponde la data
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {                
                Day::create([
                    'trip_id' => $trip->id, 
                    'date' => $date->format('Y-m-d'),
                    'notes' => 'Note per ' . $date->format('Y-m-d'),
                ]);
            }
        }
    }
}
