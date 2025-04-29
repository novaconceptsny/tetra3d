<?php
namespace App\Http\Controllers;

use App\Models\ArtworkCollection;
use App\Models\Photo;
use App\Models\Project;
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
        $firstTour    = $project->tours->first();
        $latestLayout = $project->layouts()->latest()->first();

        if (! $latestLayout || $latestLayout->photos()->count() >= 4) {
            $project->layouts()->create([
                'name'    => 'Layout_' . ($project->layouts()->count() + 1),
                'tour_id' => $firstTour->id,
                'user_id' => auth()->id(),
            ]);

            $project = Project::relevant()
                ->with(['company', 'tours', 'artworkCollections', 'layouts'])
                ->withCount('contributors')
                ->first();
        }

        // Get surfaces based on company_id and tour_ids
        $surfaces = Surface::where('company_id', $project->company_id)
            ->whereIn('tour_id', $project->tours->pluck('id'))
            ->get();

        $artworkCollections = ArtworkCollection::forCompany($project->company_id)
            ->withCount('artworks')
            ->get();
        $photos = Photo::where('project_id', $project->id)
            ->groupBy('name')
            ->get();

        return view('photo.index', compact('artworkCollections', 'project', 'photos', 'surfaces'));
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

    public function duplicatePhotos(Request $request, Project $project)
    {
        try {
            return DB::transaction(function () use ($request, $project) {
                $firstTour          = $project->tours->first();
                $countLayoutProject = count($project->layouts);
                if ($project->layouts->isEmpty()) {
                    if (! $firstTour) {
                        return response()->json([
                            'success' => false,
                            'message' => 'No tours found for this project',
                        ], 422);
                    }

                    $layout = $project->layouts()->create([
                        'name'    => 'Layout_1',
                        'tour_id' => $firstTour->id,
                        'user_id' => auth()->id(),
                    ]);
                } else {
                    $layout_id    = $request->input('layout_id');
                    $layout_click = $project->layouts()->where('id', $layout_id)->with('photos')->first();

                    if ($layout_click->photos()->count() < 4) {
                        $layout = $layout_click;
                    } else {
                        $latestLayout = $project->layouts()->latest()->first();

                        if (! $latestLayout || $latestLayout->photos()->count() > 4) {
                            $layout = $project->layouts()->create([
                                'name'    => 'Layout_' . ($project->layouts()->count() + 1),
                                'tour_id' => $firstTour->id,
                                'user_id' => auth()->id(),
                            ]);
                        } else {
                            $layout = $latestLayout;
                        }
                    }
                }

                // Check if project has no layouts and create default layout
//                if ($project->layouts->isEmpty()) {
//                    if (!$firstTour) {
//                        return response()->json([
//                            'success' => false,
//                            'message' => 'No tours found for this project'
//                        ], 422);
//                    }
//
//                    $layout = $project->layouts()->create([
//                        'name' => 'Layout_1',
//                        'tour_id' => $firstTour->id,
//                        'user_id' => auth()->id()
//                    ]);
//                } elseif ($request->input('layout_id') !== 'null' && !empty($request->input('layout_id'))) {
//                    $layout_id = $request->layout_id;
//                    $layout_click = $project->layouts()->where('id', $layout_id)->with('photos')->first();
//                    $count_layout = count($layout_click->photos);
//                    if ($count_layout < 4){
//                        $layout = $layout_click;
//                    }else{
//                        $layout = $project->layouts()->create([
//                            'name' => 'Layout_'.$countLayoutProject,
//                            'tour_id' => $firstTour->id,
//                            'user_id' => auth()->id()
//                        ]);
//                    }
//                }else{
//                    $layout = $project->layouts()->create([
//                        'name' => 'Layout_'.$countLayoutProject,
//                        'tour_id' => $firstTour->id,
//                        'user_id' => auth()->id()
//                    ]);
//                }

                // First, delete all existing photos for this project
//                Photo::where('project_id', $project->id)->delete();

                $savedPhotos = [];

                if ($request->hasFile('images')) {
                    foreach ($request->file('images') as $index => $image) {
                        // Generate unique filename
                        $filename = uniqid() . '.' . $image->getClientOriginalExtension();

                        // Store the file in the storage/app/public/media/photos directory
                        $path = $image->storeAs('media/photos', $filename, 'public');

                        // Parse corners JSON string into array
                        $cornersData = json_decode($request->corners[$index], true) ?? [];

                        // Create data array with image dimensions and corners
                        $data = [
                            'img_width'           => (string) $request->widths[$index],
                            'img_height'          => (string) $request->heights[$index],
                            'bounding_box_top'    => (string) $request->boundingBoxTop[$index],
                            'bounding_box_left'   => (string) $request->boundingBoxLeft[$index],
                            'bounding_box_width'  => (string) $request->boundingBoxWidth[$index],
                            'bounding_box_height' => (string) $request->boundingBoxHeight[$index],
                            'corners'             => $cornersData, // Store parsed corners data
                        ];

                        // Create new photo record with layout_id
                        $photo = new Photo([
                            'project_id'     => $project->id,
                            'layout_id'      => $layout->id, // Add layout_id here
                            'name'           => $request->names[$index],
                            'background_url' => '/storage/' . $path,
                            'data'           => $data,
                        ]);

                        $photo->save();
                        $savedPhotos[] = [
                            'id'   => $photo->id,
                            'name' => $photo->name,
                            'url'  => asset($photo->background_url),
                        ];
                    }
                }

                $latestLayoutAfter = $project->layouts()->latest()->first();
                if (! $latestLayoutAfter || $latestLayoutAfter->photos()->count() == 4) {
                    $project->layouts()->create([
                        'name'    => 'Layout_' . ($project->layouts()->count() + 1),
                        'tour_id' => $firstTour->id,
                        'user_id' => auth()->id(),
                    ]);
                }

                return response()->json([
                    'success'      => true,
                    'message'      => 'Photos duplicated successfully',
                    'photos'       => $savedPhotos,
                    'layout'       => $layout,
                    'hasNewLayout' => $project->layouts->isEmpty(),
                ]);
            });
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to duplicate photos: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update($id)
    {

        return redirect()->back()->with('success', 'Photo updated');
    }
    public function destroy($id)
    {
        Photo::destroy($id);

        return [
            'status'  => true,
            'message' => 'Delete Success!',
        ];
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
}
