<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\ArtworkCollection;
use Illuminate\Http\Request;

class ArtworksController extends Controller
{
    public function index()
    {
        $artworks = Artwork::paginate(25);
        return view('artworks.index', compact('artworks'));
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Artwork $artwork)
    {
        //
    }

    public function edit(Artwork $artwork)
    {
        //
    }

    public function update(Request $request, Artwork $artwork)
    {
        //
    }

    public function destroy(Artwork $artwork)
    {
       //
    }

    public function destroyCollection($id)
    {
        ArtworkCollection::destroy($id);

        return [
            'status' => true,
            'message' => 'Delete Success!'
        ];
    }
}
