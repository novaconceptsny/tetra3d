<?php
namespace App\Http\Controllers;

use App\Models\ArtworkCollection;
use App\Models\Project;
use App\Models\Photo;
use App\Models\Surface;
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

        // Get surfaces based on company_id and tour_ids
        $surfaces = Surface::where('company_id', $project->company_id)
            ->whereIn('tour_id', $project->tours->pluck('id'))
            ->get();
        
        $artworkCollections = ArtworkCollection::forCompany($project->company_id)->get();
        $photos = Photo::where('project_id', $project->id)
            ->groupBy('name')
            ->get();

        return view('photo.index', compact('artworkCollections', 'project', 'photos', 'surfaces'));
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
                // Get the first tour's ID from the project
                $firstTour = $project->tours->first();
                
                if (!$firstTour) {
                    return response()->json([
                        'success' => false,
                        'message' => 'No tours found for this project'
                    ], 422);
                }

                // Check if project has layouts
                if ($project->layouts->isEmpty()) {
                    // Create first layout if none exist
                    $layout = $project->layouts()->create([
                        'name' => 'Layout_1',
                        'tour_id' => $firstTour->id,
                        'user_id' => auth()->id()
                    ]);
                } else {
                    // Create a new layout with incremented number
                    $lastLayoutNumber = $project->layouts()
                        ->where('name', 'like', 'Layout_%')
                        ->get()
                        ->map(function ($layout) {
                            return (int) str_replace('Layout_', '', $layout->name);
                        })
                        ->max();
                    
                    $newLayoutNumber = $lastLayoutNumber + 1;
                    
                    $layout = $project->layouts()->create([
                        'name' => "Layout_{$newLayoutNumber}",
                        'tour_id' => $firstTour->id,
                        'user_id' => auth()->id()
                    ]);
                }

                $savedPhotos = [];
                
                if ($request->hasFile('images')) {
                    foreach($request->file('images') as $index => $image) {
                        // Generate unique filename
                        $filename = uniqid() . '.' . $image->getClientOriginalExtension();
                        
                        // Store the file
                        $path = $image->storeAs('media/photos', $filename, 'public');
                        
                        // Parse corners JSON string into array
                        $cornersData = json_decode($request->corners[$index], true) ?? [];
                        
                        // Create data array
                        $data = [
                            'img_width' => (string)$request->widths[$index],
                            'img_height' => (string)$request->heights[$index],
                            'bounding_box_top' => (string)$request->boundingBoxTop[$index],
                            'bounding_box_left' => (string)$request->boundingBoxLeft[$index],
                            'bounding_box_width' => (string)$request->boundingBoxWidth[$index],
                            'bounding_box_height' => (string)$request->boundingBoxHeight[$index],
                            'corners' => $cornersData
                        ];

                        // Create new photo record with the new layout_id
                        $photo = new Photo([
                            'project_id' => $project->id,
                            'layout_id' => $layout->id,
                            'name' => $request->names[$index],
                            'background_url' => '/storage/' . $path,
                            'data' => $data,
                        ]);
                        
                        $photo->save();
                        $savedPhotos[] = [
                            'id' => $photo->id,
                            'name' => $photo->name,
                            'url' => asset($photo->background_url),
                            'created_at' => $photo->created_at->format('Y-m-d')
                        ];
                    }
                }

                // Refresh the project to get the updated layouts
                $project->refresh();

                return response()->json([
                    'success' => true,
                    'message' => 'Photos duplicated successfully',
                    'photos' => $savedPhotos,
                    'layout' => $layout,
                    'hasNewLayout' => false
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
