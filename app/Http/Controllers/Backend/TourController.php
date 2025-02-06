<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ValidationRules;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Tour;
use App\Models\TourModel;
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
        
        foreach($tours as $tour) {
            $models = TourModel::where('tour_id', $tour->id)->get();
            $hasModel = !$models->isEmpty();
            
            // Update the has_model column in database
            $tour->update(['has_model' => $hasModel]);
        }
        
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

        $tour->addFromMediaLibraryRequest($request->thumbnail)
            ->toMediaCollection('thumbnail');


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

        $company_has_changed = $tour->company_id != $request->company_id;

        if ($company_has_changed && $tour->projects->count()) {
            return redirect()->back()
                ->withInput()
                ->with('remove_projects_alert', true);
        }

        $tour->update($request->only([
            'name' , 'company_id'
        ]));

        if ($company_has_changed){
            $tour->reflectCompanyChanges();
        }

        $tour->addFromMediaLibraryRequest($request->thumbnail)
            ->toMediaCollection('thumbnail');

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
