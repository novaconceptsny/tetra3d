<?php

namespace App\Http\Controllers;

use App\Models\Artwork;

class CanvasController extends Controller
{
    public function index()
    {
        $data = array();

        $data['artworks'] = Artwork::all();
        return view('pages.editor', $data);
    }
}
