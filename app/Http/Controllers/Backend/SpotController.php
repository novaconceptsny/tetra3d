<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ValidationRules;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Spot;
use App\Models\Tour;
use Illuminate\Http\Request;

class SpotController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Spot::class, 'spot');
    }

    public function index(Tour $tour)
    {
        $data = array();

        $data['spots'] = $tour->spots()->withCount('surfaces')->get();
        $data['tour'] = $tour;

        return view('backend.spot.index', $data);
    }

    public function create(Tour $tour)
    {
        $data = array();
        $data['route'] = route('backend.tours.spots.store', $tour);
        $data['tour'] = $tour;

        return view('backend.spot.form', $data);
    }

    public function store(Request $request, Tour $tour)
    {
        $request->validate(ValidationRules::storeSpot());

        $request->merge([
            'company_id' => $tour->company_id
        ]);

        $spot = $tour->spots()->create($request->only([
            'name', 'company_id'
        ]));

        $spot->surfaces()->sync($request->surfaces);

        $spot
            ->addFromMediaLibraryRequest($request->image_360)
            ->toMediaCollection('image_360');

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
        $data['route'] = route('backend.spots.update', $spot);
        $data['tour'] = $spot->tour;

        return view('backend.spot.form', $data);
    }

    public function update(Request $request, Spot $spot)
    {
        $request->validate(ValidationRules::updateSpot());

        $spot->update($request->only([
            'name'
        ]));

        $spot->surfaces()->sync($request->surfaces);

        $spot
            ->addFromMediaLibraryRequest($request->image_360)
            ->toMediaCollection('image_360');

        return redirect()->route('backend.tours.spots.index', $spot->tour)->with('success', 'Spot updated successfully');
    }

    public function destroy(Spot $spot)
    {
        //
    }
}
