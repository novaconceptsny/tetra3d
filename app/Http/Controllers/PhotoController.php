<?php
namespace App\Http\Controllers;

use App\Models\ArtworkCollection;
use App\Models\Photo;
use App\Models\Project;
use App\Models\Surface;
use App\Models\PhotoState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class PhotoController extends Controller
{
    public function index(Project $project)
    {
        $project = Project::relevant()
            ->with(['company', 'tours', 'artworkCollections', 'layouts'])
            ->withCount('contributors')
            ->first();
        $firstTour    = $project->tours->first();

        // Get surfaces and artwork collections
        $surfaces = Surface::where('company_id', $project->company_id)
            ->whereIn('tour_id', $project->tours->pluck('id'))
            ->get();

        $artworkCollections = ArtworkCollection::forCompany($project->company_id)
            ->withCount('artworks')
            ->get();

        // Check photo state and organize photos by layout
        $photoState = PhotoState::where('project_id', $project->id)->first();
        
        $photos = Photo::where('project_id', $project->id)->get();
        $layoutPhotos = [];
        
        if ($photoState) {
            // Get all photo states for this project
            $photoStates = PhotoState::where('project_id', $project->id)->get();
            
            // Group photos by layout
            foreach ($photoStates as $state) {
                $layout = $project->layouts()->find($state->layout_id);
                if ($layout) {
                    if (!isset($layoutPhotos[$layout->id])) {
                        $layoutPhotos[$layout->id] = [
                            'layout_id' => $layout->id,
                            'name' => $layout->name,
                            'thumbnail_urls' => [],
                            'photos' => []
                        ];
                    }
                    // Add thumbnail URL to the array if it exists and isn't already included
                    if ($state->thumbnail_url && !in_array($state->thumbnail_url, $layoutPhotos[$layout->id]['thumbnail_urls'])) {
                        $layoutPhotos[$layout->id]['thumbnail_urls'][] = $state->thumbnail_url;
                    }
                    // Add photo ID if not already included
                    if (!in_array($state->photo_id, $layoutPhotos[$layout->id]['photos'])) {
                        $layoutPhotos[$layout->id]['photos'][] = $state->photo_id;
                    }
                }
            }
        } else {
            $layoutPhotos = [];
        }


        return view('photo.index', compact(
            'artworkCollections',
            'project',
            'photos',
            'surfaces',
            'layoutPhotos'
        ));
    }

    public function updateCollections(Request $request, Project $project)
    {
        // Validate the request
        $request->validate([
            'collection_id' => 'required|exists:artwork_collections,id',
        ]);

        // Attach the collection to the project
        // This will add the collection if it doesn't exist in the relationship
        $project->artworkCollections()->attach($request->collection_id);

        return redirect()->back()->with('success', 'Collection added successfully');
    }

    public function update(Request $request, $id)
    {
        try {
            // Find the photo or fail with 404
            $photo = Photo::findOrFail($id);
            
            // Decode the data from the request
            $data = json_decode($request->input('data'), true);
            
            // Update the photo
            $photo->update([
                'surface_id' => $request->input('surface_id'),
                'data' => [
                    'corners' => $data['corners'],
                    'img_width' => $data['width'],
                    'img_height' => $data['height'],
                    'bounding_box_top' => $data['boundingBoxTop'],
                    'bounding_box_left' => $data['boundingBoxLeft'],
                    'bounding_box_width' => $data['boundingBoxWidth'],
                    'bounding_box_height' => $data['boundingBoxHeight']
                ]
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo updated successfully',
                'photo' => $photo
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Photo not found'
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update photo: ' . $e->getMessage()
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            DB::beginTransaction();
            
            // Delete the photo state first
            PhotoState::where('photo_id', $id)->delete();
            
            // Then delete the photo
            Photo::destroy($id);
            
            DB::commit();

            return [
                'status' => true,
                'message' => 'Delete Success!'
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            
            return [
                'status' => false,
                'message' => 'Delete Failed: ' . $e->getMessage()
            ];
        }
    }

    public function store(Request $request)
    {
        try {
            $images  = $request->file('images');
            $names   = $request->input('names');
            $widths  = $request->input('widths');
            $heights = $request->input('heights');
            $boundingBoxTop = $request->input('boundingBoxTop');
            $boundingBoxLeft = $request->input('boundingBoxLeft');
            $boundingBoxWidth = $request->input('boundingBoxWidth');
            $boundingBoxHeight = $request->input('boundingBoxHeight');

            $cornersData = [];

            $savedPhotos = [];

            foreach ($images as $index => $image) {
                // Get corners data for this image
                if (isset($request->corners) && is_array($request->corners) && isset($request->corners[$index])) {
                    $cornersData = json_decode($request->corners[$index], true) ?? [];
                }
                
                // Generate unique filename
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();

                // Store the file in storage/app/public/media/photos
                $path = $image->storeAs('media/photos', $filename, 'public');

                // Create data array with image dimensions
                $data = [
                    'img_width'  => (string) $widths[$index],
                    'img_height' => (string) $heights[$index],

                    'bounding_box_top'    => (string) $boundingBoxTop[$index],
                    'bounding_box_left'   => (string) $boundingBoxLeft[$index],
                    'bounding_box_width'  => (string) $boundingBoxWidth[$index],
                    'bounding_box_height' => (string) $boundingBoxHeight[$index],
                    'corners'             => $cornersData, // Store parsed corners data 
                ];

                // Create new photo record
                $photo = new Photo([
                    'name'           => $names[$index],
                    'background_url' => '/storage/' . $path,
                    'data'           => $data,
                    'project_id'     => $request->input('project_id'), // Make sure to pass project_id from frontend
                ]);

                $photo->save();

                $savedPhotos[] = [
                    'id'   => $photo->id,
                    'name' => $photo->name,
                    'url'  => asset($photo->background_url),
                ];
            }

            return response()->json([
                'success' => true,
                'message' => 'Images saved successfully',
                'photos'  => $savedPhotos,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function storePhotoState(Request $request)
    {
        try {
            DB::beginTransaction();

            $project = Project::findOrFail($request->project_id);
            $firstTour = $project->tours->first();

            if (!$firstTour) {
                return response()->json([
                    'success' => false,
                    'message' => 'No tours found for this project'
                ], 422);
            }

            // Create a new layout
            $layout = $project->layouts()->create([
                'name' => 'Layout_' . ($project->layouts()->count() + 1),
                'tour_id' => $firstTour->id,
                'user_id' => auth()->id()
            ]);

            // Create new photo states without deleting existing ones
            $photoStates = [];
            foreach ($request->photos as $photo) {
                $photoStates[] = [
                    'photo_id' => $photo['id'],
                    'project_id' => $request->project_id,
                    'layout_id' => $layout->id,
                    'thumbnail_url' => $photo['src']
                ];
            }

            PhotoState::insert($photoStates);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Photo state saved successfully',
                'layout' => $layout
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save photo state: ' . $e->getMessage()
            ], 500);
        }
    }

    public function storeSurface(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'width' => 'required|numeric',
                'height' => 'required|numeric',
                'project_id' => 'required|exists:projects,id',
                'surface_id' => 'nullable|exists:surfaces,id'
            ]);

            // Get project directly without relationships since we only need company_id
            $project = Project::findOrFail($validated['project_id']);
            $firstTour = $project->tours()->first();

            if (!$firstTour) {
                throw new \Exception('No tour found for this project');
            }

            if ($request->has('surface_id')) {
                // Update existing surface
                $surface = Surface::findOrFail($request->surface_id);
                $surface->name = $validated['name'];
                $surface->data = array_merge($surface->data ?? [], [
                    'img_width' => $validated['width'],
                    'img_height' => $validated['height']
                ]);
                $surface->save();
            } else {
                // Create new surface with explicit company_id and tour_id
                $surface = Surface::create([
                    'name' => $validated['name'],
                    'company_id' => $project->company_id,
                    'tour_id' => $firstTour->id,  // Use first tour's ID
                    'data' => [
                        'img_width' => $validated['width'],
                        'img_height' => $validated['height']
                    ]
                ]);
            }

            return response()->json([
                'success' => true,
                'message' => 'Surface saved successfully',
                'surface' => $surface
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving surface: ' . $e->getMessage()
            ], 500);
        }
    }
}
