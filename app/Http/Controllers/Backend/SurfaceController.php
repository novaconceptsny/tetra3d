<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Tour;
use App\Models\Surface;
use Illuminate\Http\Request;

class SurfaceController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Surface::class, 'surface');
    }

    public function index(Tour $tour)
    {
        $data = array();
        $data['surfaces'] = $tour->surfaces;
        $data['tour'] = $tour;

        return view('backend.surface.index', $data);
    }

    public function create(Tour $tour)
    {
        $data = array();
        $data['tour'] = $tour;
        $data['route'] = route('backend.tours.surfaces.store', $tour);

        return view('backend.surface.form', $data);
    }

    public function store(Request $request, Tour $tour)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'data' => 'array',
            'company_id' => 'required|exists:companies,id'
        ]);

        $surface = $tour->surfaces()->create([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'data' => $validated['data'] ?? [],
            'company_id' => $validated['company_id']
        ]);

        $surface->addFromMediaLibraryRequest($request->main)
            ->toMediaCollection('main');
        $surface->addFromMediaLibraryRequest($request->background)
            ->toMediaCollection('background');
        $surface->addFromMediaLibraryRequest($request->layout)
            ->toMediaCollection('layout');

        return redirect()
            ->back()
            /*->route('backend.tours.surfaces.index', $tour)*/
            ->with('success', 'Surface created successfully');
    }

    public function show(Surface $surface)
    {
        //
    }

    public function edit(Surface $surface)
    {
        $data = array();
        $data['surface'] = $surface;
        $data['tour'] = $surface->tour;
        $data['route'] = route('backend.surfaces.update', $surface);
        $data['method'] = 'put';

        return view('backend.surface.form', $data);
    }

    public function update(Request $request, Surface $surface)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'display_name' => 'required|string|max:255',
            'data' => 'array',
        ]);

        $surface->update([
            'name' => $validated['name'],
            'display_name' => $validated['display_name'],
            'data' => $validated['data'] ?? [],
        ]);

        $surface->addFromMediaLibraryRequest($request->main)
            ->toMediaCollection('main');
        $surface->addFromMediaLibraryRequest($request->background)
            ->toMediaCollection('background');
        $surface->addFromMediaLibraryRequest($request->layout)
            ->toMediaCollection('layout');

        return redirect()
            ->route('backend.tours.surfaces.index', $surface->tour)
            ->with('success', 'Surface updated successfully');
    }

    public function destroy(Surface $surface)
    {
        $surface->delete();

        return redirect()->back()->with('success', 'Surface deleted successfully');
    }
}
