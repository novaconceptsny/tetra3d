<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ValidationRules;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Tour;
use App\Models\TourModel;
use App\Models\Company;
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
        $data['companies'] = Company::all();

        return view('backend.tour.form', $data);
    }

    public function store(Request $request)
    {
        $request->validate(ValidationRules::storeTour());

        $company_ids = (array) $request->input('company_id', []);
        $main_company_id = array_shift($company_ids); // first is main

        $data = $request->only(['name']);
        $data['name'] = is_array($data['name']) ? $data['name'][0] : $data['name'];
        $data['company_id'] = $main_company_id;

        $tour = Tour::create($data);

        // Sync additional companies to pivot table
        if (!empty($company_ids)) {
            $tour->companies()->sync($company_ids);
        }

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
        $data['companies'] = Company::all();

        return view('backend.tour.form', $data);
    }

    public function update(Request $request, Tour $tour)
    {
        $request->validate(ValidationRules::updateTour());

        $company_ids = (array) $request->input('company_id', []);
        $main_company_id = array_shift($company_ids); // first is main

        $company_has_changed = $tour->company_id != $main_company_id;

        if ($company_has_changed && $tour->projects->count()) {
            return redirect()->back()
                ->withInput()
                ->with('remove_projects_alert', true);
        }

        $tour->update([
            'name' => is_array($request->name) ? $request->name[0] : $request->name,
            'company_id' => $main_company_id,
        ]);

        // Sync additional companies to pivot table
        $tour->companies()->sync($company_ids);

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

    public function toggleModel(Tour $tour)
    {
        $tour->update([
            'has_model' => !$tour->has_model
        ]);

        return response()->json(['success' => true]);
    }

    public function reGenerateXML()
    {
        try {
            // Get all tours
            $tours = Tour::with('spots')->get();

            // Loop through each tour and its spots
            foreach ($tours as $tour) {
                foreach ($tour->spots as $spot) {
                    // Generate XML for each spot
                    $spot->generateXml();
                }
            }

            return response()->json([
                'success' => true,
                'message' => 'XML files regenerated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'error' => 'Error regenerating XML files: ' . $e->getMessage()
            ], 500);
        }
    }
}
