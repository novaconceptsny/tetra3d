<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\ArtworkCollection;
use App\Models\Project;
use App\Models\SurfaceState;
use Illuminate\Http\Request;

class ArtworkCollectionController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(ArtworkCollection::class, 'collection');
    }

    public function index()
    {
        $collections = ArtworkCollection::with('company')->withCount('artworks')->withCount('sculptureModels')->get();

        return view('backend.artwork-collection.index', compact('collections'));
    }

    public function create()
    {
        $data = array();
        $data['route'] = route('backend.artwork-collections.store');

        return view('backend.artwork-collection.form', $data);
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required'
        ]);

        ArtworkCollection::create($request->only([
            'name'
        ]));

        return redirect()->route('backend.artwork-collections.index')
            ->with('success', 'Collection created successfully');
    }

    public function show(ArtworkCollection $collection)
    {
    }

    public function edit(ArtworkCollection $collection)
    {
        $data = array();
        $data['route'] = route('backend.artwork-collections.update', $collection);
        $data['method'] = 'put';
        $data['collection'] = $collection;

        return view('backend.artwork-collection.form', $data);
    }

    public function update(Request $request, ArtworkCollection $collection) {

        $request->validate([
            'name' => 'required'
        ]);

        $collection->update($request->only([
            'name'
        ]));

        return redirect()->route('backend.artwork-collections.index')
            ->with('success', 'Collection updated successfully');
    }

    public function destroy(ArtworkCollection $collection)
    {
        $collection->delete();
        $collection->projects()->detach();
        
        $collection->artworks()->cursor()->each(
            fn (Artwork $artwork) => $artwork->delete()
        );
        return redirect()->back()->with('success', 'Collection deleted successfully');
    }
}
