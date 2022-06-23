<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Spot;
use App\Models\Surface;

class CanvasController extends Controller
{
    public function show(Surface $surface)
    {
        $surface->background_url = $surface->getFirstMediaUrl('background');

        $surface = $surface->only([
            'id', 'name', 'background_url', 'data'
        ]);

        $data = array();
        $data['surface'] = $surface;
        $data['spot'] = Spot::findOrFail(request('spot_id'));
        $artworks = Artwork::take(30)->get();
        $data['artwork_scales'] = $artworks->pluck('data.scale', 'id')->toArray();

        $data['artworks'] = $artworks;
        return view('pages.editor', $data);
    }
}
