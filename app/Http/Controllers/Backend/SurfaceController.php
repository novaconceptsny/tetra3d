<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Tour;
use App\Models\Surface;
use Illuminate\Http\Request;

class SurfaceController extends Controller
{
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
        $data['route'] = route('backend.tours.surfaces.store', $tour);

        return view('backend.surface.form', $data);
    }

    public function store(Request $request, Tour $tour)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $tour->surfaces()->create([
            'name' => $request->name
        ]);

        return redirect()
            ->route('backend.tours.surfaces.index', $tour)
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
        $data['route'] = route('backend.surfaces.update', $surface);
        $data['method'] = 'put';

        return view('backend.surface.form', $data);
    }

    public function update(Request $request, Surface $surface)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $surface->update($request->only([
            'name'
        ]));

        return redirect()->back()->with('success', 'Surface updated successfully');
    }

    public function destroy(Surface $surface)
    {
        $surface->delete();

        return redirect()->back()->with('success', 'Surface deleted successfully');
    }
}