<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ValidationRules;
use App\Http\Controllers\Controller;
use App\Models\Tour;
use Illuminate\Http\Request;

class TourController extends Controller
{
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

        Tour::create($request->all());

        return redirect()->route('backend.tours.index')
            ->with('success', 'Tour created successfully');
    }

    public function show(Tour $tour)
    {
        //
    }

    public function edit(Tour $tour)
    {
        $data = array();
        $data['route'] = route('backend.tours.update', $tour);
        $data['method'] = 'put';
        $data['tour'] = $tour;

        return view('backend.tour.form', $data);
    }

    public function update(Request $request, Tour $tour)
    {
        $request->validate(ValidationRules::updateTour());

        $tour->update($request->all());

        return redirect()->route('backend.tours.index')
            ->with('success', 'Tour updated successfully');


    }

    public function destroy(Tour $tour)
    {
        $tour->delete();
        $tour->projects()->delete();

        return redirect()->back()->with('success', 'Tour deleted successfully');

    }
}
