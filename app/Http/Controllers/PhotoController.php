<?php
namespace App\Http\Controllers;

use App\Models\ArtworkCollection;
use App\Models\Project;
use App\Models\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

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
        try {
            return DB::transaction(function () use ($request, $project) {
                // First, delete all existing photos for this project
                Photo::where('project_id', $project->id)->delete();
                
                $savedPhotos = [];
                
                if ($request->hasFile('images')) {
                    foreach($request->file('images') as $index => $image) {
                        // Generate unique filename
                        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                        
                        // Store the file in the storage/app/public/media/photos directory
                        $path = $image->storeAs('media/photos', $filename, 'public');
                        
                        // Create data array with image dimensions as strings
                        $data = [
                            'img_width' => (string)$request->widths[$index],
                            'img_height' => (string)$request->heights[$index]
                        ];

                        // Create new photo record
                        $photo = new Photo([
                            'project_id' => $project->id,
                            'name' => $request->names[$index],
                            'background_url' => '/storage/' . $path,
                            'data' => $data,
                        ]);
                        
                        $photo->save();
                        $savedPhotos[] = [
                            'id' => $photo->id,
                            'name' => $photo->name,
                            'url' => asset($photo->background_url)
                        ];
                    }
                }

                return response()->json([
                    'success' => true,
                    'message' => 'Photos duplicated successfully',
                    'photos' => $savedPhotos
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate photos: ' . $e->getMessage()
            ], 500);
        }
    }

}
