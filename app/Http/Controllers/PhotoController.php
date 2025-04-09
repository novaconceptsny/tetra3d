<?php
namespace App\Http\Controllers;

use App\Models\ArtworkCollection;
use App\Models\Project;
use App\Models\Photo;
use Illuminate\Http\Request;

class PhotoController extends Controller
{
    public function index(Project $project)
    {
        $project = Project::relevant()
            ->with(['company', 'tours', 'artworkCollections', 'layouts'])
            ->withCount('contributors')
            ->first();
        $artworkCollections = ArtworkCollection::forCompany($project->company_id)->get();
        
        // Load photos for this project
        $photos = Photo::where('project_id', $project->id)->get();

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

    public function duplicatePhotos(Request $request, Project $project)
    {
        $photos = $request->input('photos', []);
        $savedPhotos = [];

        foreach ($photos as $photoData) {
            $photo = new Photo([
                'project_id' => $project->id,
                'name' => $photoData['name'],
                'background_url' => $photoData['background_url'],
                'data' => $photoData['data'] ?? '{}',
            ]);
            
            $photo->save();
            $savedPhotos[] = $photo;
        }

        return response()->json([
            'success' => true,
            'message' => 'Photos duplicated successfully',
            'photos' => $savedPhotos
        ]);
    }
}
