<?php

namespace App\Http\Controllers;

use App\Models\Artwork;

class CanvasController extends Controller
{
    public function index()
    {
        $data = array();

        $data['artworks'] = Artwork::paginate(25);
        return view('pages.editor', $data);
    }
}
