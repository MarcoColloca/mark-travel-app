<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\PatchStageCoverRequest;
use App\Http\Requests\StoreStageRequest;
use App\Http\Requests\UpdateStageRequest;
use App\Models\Image;
use App\Models\Stage;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;


class StageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreStageRequest $request)
    {           
        // recupero dei dati validati
        $form_data = $request->validated();

        // creazione dello slug unico
        $form_data['slug'] = Stage::getUniqueSlug($form_data['name']);
        // $form_data['day_id'] = $request['day_id'];

        
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

        $new_stage = Stage::create($form_data);


        return back();
        //return dump($request);
    }

    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $stage = Stage::with('day', 'day.trip')->where('id', $id)->get();        
        $stage[0]->day->date = formatItalianDate($stage[0]->day->date);

        $images = Image::where('stage_id', $id)->get();
        

        return view('admin.stages.show', compact('stage', 'images'));
    }



    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateStageRequest $request, $id)
    {
        $stage = Stage::findOrFail($id);
        $form_data = $request->validated();

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
            if ($stage->cover) {
                $existingFileName = basename(parse_url($stage->cover, PHP_URL_PATH));
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


        $stage->update($form_data);

        return back();
    }

    public function addCover(PatchStageCoverRequest $request, $id)
    {
        $stage = Stage::findOrFail($id);

        $form_data = $request->validated();
       
        
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
            'predefinedAcl' => 'publicRead', 
        ]);

        // Ottiengo l'URL pubblico del file caricato
        $publicUrl = "https://storage.googleapis.com/{$bucket->name()}/{$fileName}";

        $form_data['cover'] = $publicUrl;


        $stage->update($form_data);

        return back();
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy($id)
    {
        $stage = Stage::findOrFail($id);

        $stage->delete();
        
        return back();
    }
}



/** Metodi Non Utilizzati */
     /**
     * Display a listing of the resource.
     */
    // public function index()
    // {
    //     //
    // }

    /**
     * Show the form for creating a new resource.
     */
    // public function create()
    // {
    //     //
    // }
    /**
     * Show the form for editing the specified resource.
     */
    // public function edit(Stage $stage)
    // {
    //     //
    // }