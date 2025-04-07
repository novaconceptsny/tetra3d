<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\ArtworkCollection;
use App\Models\Project;
use App\Models\Photo;

class PhotoController extends Controller
{
    public function index()
    {
        $artworkCollections = ArtworkCollection::forCurrentCompany()->get();
        $project = Project::with(['artworkCollections'])->first();
        $photos = [];
        $projectCollections = $artworkCollections->where('project_id', $project->id);
        
        return view('photo.index', compact('artworkCollections', 'project', 'photos', 'projectCollections'));
    }
} 