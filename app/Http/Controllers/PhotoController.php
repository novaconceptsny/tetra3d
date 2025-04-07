<?php
namespace App\Http\Controllers;

use App\Models\ArtworkCollection;
use App\Models\Project;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
    public function index(Project $project)
    {
        $project = Project::relevant()
            ->with(['company', 'tours', 'artworkCollections'])
            ->withCount('contributors')
            ->first();
        $artworkCollections = ArtworkCollection::forCompany($project->company_id)->get();
        $photos = [];

        return view('photo.index', compact('artworkCollections', 'project', 'photos'));
    }

    public function updateCollections(Request $request, Project $project)
    {
        // Validate the request
        $request->validate([
            'collection_id' => 'required|exists:artwork_collections,id'
        ]);

        // Attach the collection to the project
        // This will add the collection if it doesn't exist in the relationship
        $project->artworkCollections()->attach($request->collection_id);

        return redirect()->back()->with('success', 'Collection added successfully');
    }
}
