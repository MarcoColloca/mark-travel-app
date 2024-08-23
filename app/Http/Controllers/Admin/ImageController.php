<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreImageRequest;
use App\Http\Requests\UpdateImageRequest;
use App\Models\Image;
use Illuminate\Http\Request;
use Kreait\Firebase\Factory;


class ImageController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreImageRequest $request)
    {

        $form_data = $request->validated();

        // Gestione del caricamento dell'immagine di copertina
        
            // Ottiengo il file dal request
            $file = $request->file('url');
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

            $form_data['url'] = $publicUrl;

        //


        $new_image = Image::create($form_data);

        return back();
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Image $image)
    {
        //
    }
}



/*Metodi Inutilizzati */

    // /**
    //  * Display a listing of the resource.
    //  */
    // public function index()
    // {
    //     //
    // }

    // /**
    //  * Show the form for creating a new resource.
    //  */
    // public function create()
    // {
    //     //
    // }
        // /**
    //  * Display the specified resource.
    //  */
    // public function show(Image $image)
    // {
    //     //
    // }

    // /**
    //  * Show the form for editing the specified resource.
    //  */
    // public function edit(Image $image)
    // {
    //     //
    // }

    // /**
    //  * Update the specified resource in storage.
    //  */
    // public function update(UpdateImageRequest $request, Image $image)
    // {
    //     //
    // }