<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreDayRequest;
use App\Http\Requests\UpdateDayRequest;
use App\Models\Day;
use App\Models\Trip;
use Illuminate\Http\Request;

class DayController extends Controller
{

    public function showOne($id)
    {
       
        $days = Day::with(['stages'])->where('trip_id', $id)->get();
        $trip = Trip::where('id', $id)->get();
        
        foreach ($days as $day) {        
            $day->date = formatItalianDate($day->date);
        }

        return view('admin.days.showOne', compact('days', 'trip'));
    }


    /**
     * Display the specified resource.
     */
    public function show($id)
    {
        $day = Day::with('stages', 'trip')->where('id', $id)->get();

       
        $day[0]->date = formatItalianDate($day[0]->date);

        return view('admin.days.show', compact('day'));
    }

    

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateDayRequest $request, $id)
    {
        $day = Day::findOrFail($id);

        $form_data = $request->validated();

        $day->update($form_data);

        return back();
    }




    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreDayRequest $request)
    {
        //
    }



    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Day $day)
    {
        //
    }


    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Day $day)
    {
        //
    }
}
