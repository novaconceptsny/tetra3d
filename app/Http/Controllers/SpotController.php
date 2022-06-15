<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationRules;
use App\Models\Spot;
use App\Models\Tour;
use Illuminate\Http\Request;

class SpotController extends Controller
{
    public function index()
    {
        $spots = Spot::all();
        return view('backend.spot.index', compact('spots'));
    }

    public function create()
    {
        $data = array();
        $data['route'] = route('spots.store');
        $data['tour'] = Tour::find(1);

        return view('backend.spot.form', $data);
    }

    public function store(Request $request)
    {
        $request->validate(ValidationRules::storeSpot());

        $tour = Tour::find(1);

        $spot = $tour->spots()->create($request->only([
            'name'
        ]));

        $spot->surfaces()->sync($request->surfaces);

        return redirect()->back()->with('success', 'Spot created successfully');
    }

    public function show(Spot $spot)
    {
        //
    }

    public function edit(Spot $spot)
    {
        $data = array();
        $data['spot'] = $spot;
        $data['method'] = 'put';
        $data['route'] = route('spots.update', $spot);
        $data['tour'] = Tour::find(1);

        return view('backend.spot.form', $data);
    }

    public function update(Request $request, Spot $spot)
    {
        $request->validate(ValidationRules::updateSpot());

        $spot->update($request->only([
            'name'
        ]));

        $spot->surfaces()->sync($request->surfaces);

        return redirect()->back()->with('success', 'Spot updated successfully');
    }

    public function destroy(Spot $spot)
    {
        //
    }
}
