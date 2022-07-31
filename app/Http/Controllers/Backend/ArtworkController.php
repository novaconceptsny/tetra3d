<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ValidationRules;
use App\Http\Controllers\Controller;
use App\Models\Artwork;
use Illuminate\Http\Request;

class ArtworkController extends Controller
{
    public function index()
    {
        return view('backend.artwork.index');
    }

    public function create()
    {
        $data = array();

        $data['route'] = route('backend.artworks.store');
        return view('backend.artwork.form', $data);
    }

    public function store(Request $request)
    {
        $request->validate(ValidationRules::storeArtwork());

        $artwork = Artwork::create($request->only([
            'name', 'artist', 'type', 'data'
        ]));

        $artwork->addFromMediaLibraryRequest($request->image)
            ->toMediaCollection('image');

        return redirect()->back()->with('success', 'Artwork created successfully');
    }

    public function show(Artwork $artwork)
    {
        //
    }

    public function edit(Artwork $artwork)
    {
        $data = array();

        $data['route'] = route('backend.artworks.update', $artwork);
        $data['method'] = 'put';
        $data['artwork'] = $artwork;

        return view('backend.artwork.form', $data);
    }

    public function update(Request $request, Artwork $artwork)
    {
        $request->validate(ValidationRules::updateArtwork());

        $artwork->update($request->only([
            'name', 'artist', 'type', 'data'
        ]));

        $artwork->addFromMediaLibraryRequest($request->image)
            ->toMediaCollection('image');

        return redirect()->back()->with('success', 'Artwork updated successfully');
    }

    public function destroy(Artwork $artwork)
    {
        $artwork->delete();

        return redirect()->back()->with('success', 'Artwork deleted successfully');
    }
}
