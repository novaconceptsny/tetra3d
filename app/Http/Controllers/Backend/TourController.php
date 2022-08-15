<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ValidationRules;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Tour;
use Arr;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Tour::class, 'tour');
    }

    public function index()
    {
        $tours = Tour::withCount('surfaces', 'spots')->get();
        return view('backend.tour.index', compact('tours'));
    }

    public function create()
    {
        $data = array();
        $data['route'] = route('backend.tours.store');

        return view('backend.tour.form', $data);
    }

    public function store(Request $request)
    {
        $request->validate(ValidationRules::storeTour());

        $tour = Tour::create($request->only([
            'name' , 'company_id'
        ]));

        $map = $tour->map()->create($request->map);

        $map->addFromMediaLibraryRequest($request->map_image)
            ->toMediaCollection('image');

        return redirect()->route('backend.tours.index')
            ->with('success', 'Tour created successfully');
    }

    public function show(Tour $tour)
    {
        //
    }

    public function edit(Tour $tour)
    {
        $tour->load('map', 'spots.maps');

        $data = array();
        $data['route'] = route('backend.tours.update', $tour);
        $data['method'] = 'put';
        $data['tour'] = $tour;

        return view('backend.tour.form', $data);
    }

    public function update(Request $request, Tour $tour)
    {
        $request->validate(ValidationRules::updateTour());

        $tour->update($request->only([
            'name' , 'company_id'
        ]));


        $map = $tour->map()->updateOrCreate([
            'tour_id' => $tour->id,
        ], Arr::except($request->map, 'spots'));


        $map->spots()->sync(Arr::keyByAndForget($request->map['spots'], 'id'));

        $map->addFromMediaLibraryRequest($request->map_image)
            ->toMediaCollection('image');

        return redirect()->route('backend.tours.index')
            ->with('success', 'Tour updated successfully');

    }

    public function destroy(Tour $tour)
    {
        $tour->delete();
        $tour->projects()->delete();
        $tour->spots()->delete();

        return redirect()->back()->with('success', 'Tour deleted successfully');

    }
}
