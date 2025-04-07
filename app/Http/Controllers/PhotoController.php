<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArtworkCollection;
use App\Models\Project;

class PhotoController extends Controller
{
    public function index()
    {
        $artworkCollections = ArtworkCollection::all();
        $project = Project::first();
        
        return view('photo.index', compact('artworkCollections', 'project'));
    }
} 