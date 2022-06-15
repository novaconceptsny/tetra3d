<?php

namespace App\Http\Controllers;

use App\Models\Surface;
use App\Models\Tour;
use Illuminate\Http\Request;

class SurfaceController extends Controller
{
    public function index()
    {
        $surfaces = Surface::all();

        return view('backend.surface.index', compact('surfaces'));
    }

    public function create()
    {
        $data = array();
        $data['route'] = route('surfaces.store');

        return view('backend.surface.form', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        $tour = Tour::find(1);

        $tour->surfaces()->create([
            'tour_id' => 1,
            'name' => $request->name
        ]);

        return redirect()->back()->with('success', 'Surface created successfully');
    }

    public function show(Surface $surface)
    {
        //
    }

    public function edit(Surface $surface)
    {
        $data = array();
        $data['surface'] = $surface;
        $data['route'] = route('surfaces.update', $surface);
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
        //
    }
}
