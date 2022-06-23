<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Spot;
use App\Models\Surface;

class CanvasController extends Controller
{
    public function show(Surface $surface)
    {
        $data = array();
        $data['surface'] = $surface;
        $artworks = Artwork::take(30)->get();
        $data['artwork_scales'] = $artworks->pluck('data.scale', 'id')->toArray();

        $data['artworks'] = $artworks;
        return view('pages.editor', $data);
    }
}
