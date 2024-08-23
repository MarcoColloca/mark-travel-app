<?php

namespace App\Http\Controllers\Admin;

use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Log;
use App\Http\Requests\StoreTripRequest;
use App\Http\Requests\UpdateTripRequest;
use App\Models\Day;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Kreait\Firebase\Factory;



class TripController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $trips = Trip::orderBy('startDate', 'asc')->get();

        foreach ($trips as $trip) {
            $trip->startDate = formatItalianDate($trip->startDate);
            $trip->endDate = formatItalianDate($trip->endDate);
        }

        return view('admin.trips.index', compact('trips'));
    }


    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreTripRequest $request)
    {
        // Log dei dati ricevuti
        //\Log::info($request->all());
        \Log::info('Inizio del Metodo Store');
        try {
            $form_data = $request->validated();
            //\Log::info($form_data);

            // Trasformo le date in reali date e non stringhe
            $startDate = Carbon::parse($form_data['startDate']);
            $endDate = Carbon::parse($form_data['endDate']);

            if ($endDate->lt($startDate)) {
                // Risposta se endDate è prima di startDate
                return response()->json(['error' => 'La data di fine deve essere successiva alla data di inizio'], 400);
            }

            // Creazione dello slug unico
            $form_data['slug'] = Trip::getUniqueSlug($form_data['name']);

            // Gestione del caricamento dell'immagine di copertina
            if ($request->hasFile('cover')) {
                // Ottiengo il file dal request
                $file = $request->file('cover');
                // Ottiengo il percorso del file
                $fileContent = file_get_contents($file->getRealPath());

                // Ottiengo il nome del file
                $fileName = $file->getClientOriginalName();

                // Configura Firebase
                $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
                $storage = $factory->createStorage();
                $bucket = $storage->getBucket();

                // Carica il file nel bucket
                $bucket->upload($fileContent, [
                    'name' => $fileName,
                    'predefinedAcl' => 'publicRead', // Imposta il file come pubblico
                ]);

                // Ottiengo l'URL pubblico del file caricato
                $publicUrl = "https://storage.googleapis.com/{$bucket->name()}/{$fileName}";

                $form_data['cover'] = $publicUrl;
            }


            // Creazione del viaggio
            $new_trip = Trip::create($form_data);

            // Creazione dei giorni del viaggio
            for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
                Day::create([
                    'trip_id' => $new_trip->id,
                    'date' => $date->format('Y-m-d'),
                    'notes' => null
                ]);
            }

            return to_route('admin.trips.index');
        } catch (\Exception $e) {
            \Log::error('Errore nel metodo Store: ' . $e->getMessage());
            // Risposta con errore
            return response()->json(['error' => 'Errore nella creazione del viaggio'], 400);
        }
    }

    /**
     * Display the specified resource.
     */
    public function show(Trip $trip)
    {
        //
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateTripRequest $request, $id)
    {


        $trip = Trip::findOrFail($id);


        $form_data = $request->validated();


        $form_data['slug'] = Trip::getUniqueSlug($form_data['name']);


        if ($request->hasFile('cover')) {

            // Ottiengo il file dal request
            $file = $request->file('cover');
            // Ottiengo il contenuto del file
            $fileContent = file_get_contents($file->getRealPath());

            // Ottiengo il nome del file
            $fileName = $file->getClientOriginalName();

            // Configura Firebase
            $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
            $storage = $factory->createStorage();
            $bucket = $storage->getBucket();

            // Se il viaggio ha già un'immagine di copertura, elimina il file esistente
            if ($trip->cover) {
                $existingFileName = basename(parse_url($trip->cover, PHP_URL_PATH));
                $bucket->object($existingFileName)->delete();
            }            

            // Carica il nuovo file nel bucket
            $bucket->upload($fileContent, [
                'name' => $fileName,
                'predefinedAcl' => 'publicRead',
            ]);

            // Ottiengo l'URL pubblico del file caricato
            $publicUrl = "https://storage.googleapis.com/{$bucket->name()}/{$fileName}";

            // Aggiorna l'URL della copertura nel form_data
            $form_data['cover'] = $publicUrl;
        }


        $trip->update($form_data);

        // Trasformo le date ricevute da stringhe a date reali
        $startDate = Carbon::parse($form_data['startDate']);
        $endDate = Carbon::parse($form_data['endDate']);

        // Recupero tutte le date del viaggio in questione, trasformandolo in un Array
        $existingDays = Day::where('trip_id', $trip->id)->pluck('date')->toArray();

        //Elimino i giorni che non sono più tra startDate e endDate
        Day::where('trip_id', $trip->id)
            ->where(function ($query) use ($startDate, $endDate) {
                $query->where('date', '<', $startDate->format('Y-m-d'))
                    ->orWhere('date', '>', $endDate->format('Y-m-d'));
            })->delete();


        // Popolo il database con le date, solo se non sono già presenti nel DB 
        for ($date = $startDate; $date->lte($endDate); $date->addDay()) {
            if (!in_array($date->format('Y-m-d'), $existingDays)) {
                Day::create([
                    'trip_id' => $trip->id,
                    'date' => $date->format('Y-m-d'),
                    'notes' => null
                ]);
            }
        }

        return to_route("admin.trips.index");
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $trip = Trip::findOrFail($id);

        // Configura Firebase
        $factory = (new Factory)->withServiceAccount(config('firebase.credentials'));
        $storage = $factory->createStorage();
        $bucket = $storage->getBucket();

        // Se il viaggio ha già un'immagine di copertura, elimina il file esistente
        if ($trip->cover) {
            $existingFileName = basename(parse_url($trip->cover, PHP_URL_PATH));
            $bucket->object($existingFileName)->delete();
        }

        $trip->delete();
        return back();
    }
}




/**
 * Rotte inutilizzate.
 */

// public function edit(Trip $trip)
// {

// }

// public function create()
// {
//     return 'FUNZIONA!';
// }