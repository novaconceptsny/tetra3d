<?php
namespace App\Http\Controllers;

use App\Models\ArtworkCollection;
use App\Models\Curate2dSurface;
use App\Models\Photo;
use App\Models\PhotoState;
use App\Models\Project;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function index()
    {
        $projects = Project::where('is_curate_2d', true)->get();
        // Get favorite photo states
        $favorites = PhotoState::where('is_favorite', true)
            ->with('photo.project')
            ->get();

        $project = $projects->first();

        // Get all surfaces for the company's tours
        $surfaces = [];

        $allCollections = $project ? ArtworkCollection::forCompany($project->company_id)
            ->withCount('artworks')
            ->get() : [];

        $photos       = [];
        $layoutPhotos = [];

        return view('photo.index', compact(
            'projects',
            'favorites',
            'allCollections',
            'project',
            'photos',
            'surfaces',
            'layoutPhotos'
        ));
    }

    public function updateCollections(Request $request, Project $project)
    {
        try {
            // Validate the request
            $request->validate([
                'collection_name' => 'required|string',
                'project_id' => 'required|exists:projects,id',
            ]);

            $project = Project::findOrFail($request->project_id);

            // Find the collection by name and company
            $collection = ArtworkCollection::where('name', $request->collection_name)
                ->where('company_id', $project->company_id)
                ->firstOrFail();

            // Attach the collection to the project only if not already attached
            if (! $project->artworkCollections->contains($collection->id)) {
                $project->artworkCollections()->attach($collection->id);
            }

            $updatedCollections = $project
                ? $project->artworkCollections()->withCount('artworks')->get()
                : [];

            return response()->json([
                'success'       => true,
                'message'       => 'Collection added successfully',
                'updatedCollections' => $updatedCollections,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
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
                'data'       => [
                    'corners'             => $data['corners'],
                    'img_width'           => $data['width'],
                    'img_height'          => $data['height'],
                    'bounding_box_top'    => $data['boundingBoxTop'],
                    'bounding_box_left'   => $data['boundingBoxLeft'],
                    'bounding_box_width'  => $data['boundingBoxWidth'],
                    'bounding_box_height' => $data['boundingBoxHeight'],
                ],
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Photo updated successfully',
                'photo'   => $photo,
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Photo not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update photo: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($module, $id)
    {
        try {
            DB::beginTransaction();

            if ($module === 'artworks') {
                // Find the artwork collection
                $artworkCollection = ArtworkCollection::findOrFail($id);

                // Find the project (you may need to pass project_id as a request param or infer it)
                $projectId = request()->input('project_id');
                $project   = Project::findOrFail($projectId);

                // Detach the artwork collection from the project
                $project->artworkCollections()->detach($artworkCollection->id);

            } elseif ($module === 'photo') {
                // Delete the photo state first
                PhotoState::where('photo_id', $id)->delete();

                // Then delete the photo
                Photo::destroy($id);
            }

            DB::commit();

            return [
                'status'  => true,
                'message' => 'Delete Success!',
            ];

        } catch (\Exception $e) {
            DB::rollBack();

            return [
                'status'  => false,
                'message' => 'Delete Failed: ' . $e->getMessage(),
            ];
        }
    }

    public function store(Request $request)
    {
        try {
            $images            = $request->file('images');
            $names             = $request->input('names');
            $widths            = $request->input('widths');
            $heights           = $request->input('heights');
            $boundingBoxTop    = $request->input('boundingBoxTop');
            $boundingBoxLeft   = $request->input('boundingBoxLeft');
            $boundingBoxWidth  = $request->input('boundingBoxWidth');
            $boundingBoxHeight = $request->input('boundingBoxHeight');

            $cornersData = [];

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
                    'img_width'           => (string) $widths[$index],
                    'img_height'          => (string) $heights[$index],

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

                $updatedPhotos = Photo::where('project_id', $request->input('project_id'))->get();
            }

            return response()->json([
                'success'       => true,
                'message'       => 'Images saved successfully',
                'updatedPhotos' => $updatedPhotos,
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

            // Create a new layout
            $layout = $project->layouts()->create([
                'name'    => 'Layout_' . ($project->layouts()->count() + 1),
                'tour_id' => $project->tour_id,
                'user_id' => auth()->id(),
            ]);

            // Create new photo states without deleting existing ones
            $photoStates = [];
            foreach ($request->photos as $photo) {
                $photoStates[] = [
                    'photo_id'      => $photo['id'],
                    'project_id'    => $request->project_id,
                    'layout_id'     => $layout->id,
                    'thumbnail_url' => $photo['background_url'],
                ];
            }

            PhotoState::insert($photoStates);

            $layoutPhotos = $this->getLayoutPhotos($project);

            DB::commit();

            return response()->json([
                'success'      => true,
                'message'      => 'Photo state saved successfully',
                'layoutPhotos' => $layoutPhotos,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to save photo state: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function storeSurface(Request $request)
    {
        try {
            $validated = $request->validate([
                'name'       => 'required|string|max:255',
                'width'      => 'required|numeric',
                'height'     => 'required|numeric',
                'project_id' => 'required|exists:projects,id',
                'surface_id' => 'nullable|exists:curate2d_surface,id',
            ]);

            // Get project directly without relationships since we only need company_id
            $project = Project::findOrFail($validated['project_id']);

            if ($request->has('surface_id')) {
                // Update existing surface
                $surface       = Curate2dSurface::findOrFail($request->surface_id);
                $surface->name = $validated['name'];
                $surface->data = array_merge($surface->data ?? [], [
                    'img_width'  => $validated['width'],
                    'img_height' => $validated['height'],
                ]);
                $surface->save();
            } else {
                // Create new surface with explicit company_id and tour_id
                $surface = Curate2dSurface::create([
                    'name'         => $validated['name'],
                    'display_name' => $validated['name'],
                    'project_id'   => $validated['project_id'],
                    'data'         => [
                        'img_width'  => $validated['width'],
                        'img_height' => $validated['height'],
                    ],
                ]);
            }

            $updatedSurfaces = Curate2dSurface::where('project_id', $validated['project_id'])->get();

            return response()->json([
                'success'         => true,
                'message'         => 'Surface saved successfully',
                'updatedSurfaces' => $updatedSurfaces,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving surface: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit(Request $request, $id)
    {
        try {
            // Find the photo
            $photo = Photo::findOrFail($id);

            // Validate the request
            $request->validate([
                'name'  => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png|max:2048',
            ]);

            // Update the photo name
            $photo->name = $request->name;

            // Handle image upload if provided
            if ($request->hasFile('image')) {
                $image = $request->file('image');

                // Generate unique filename
                $filename = uniqid() . '.' . $image->getClientOriginalExtension();

                // Store the new image
                $path = $image->storeAs('media/photos', $filename, 'public');

                // Delete old image if exists
                if ($photo->background_url) {
                    $oldPath = str_replace('/storage/', '', $photo->background_url);
                    if (Storage::disk('public')->exists($oldPath)) {
                        Storage::disk('public')->delete($oldPath);
                    }
                }

                // Update photo with new image path
                $photo->background_url = '/storage/' . $path;
            }

            // Save the changes
            $photo->save();

            return response()->json([
                'success' => true,
                'message' => 'Photo updated successfully',
                'photo'   => $photo,
            ]);

        } catch (ModelNotFoundException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Photo not found',
            ], 404);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update photo: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function toggleFavorite(Request $request, $id)
    {
        try {
            // Find the photo state based on photo_id and layout_id
            $photoState = PhotoState::where('photo_id', $id)
                ->where('layout_id', $request->layout_id)
                ->firstOrFail();

            // Toggle the is_favorite status
            $photoState->is_favorite = ! $photoState->is_favorite;
            $photoState->save();

            return response()->json([
                'success'     => true,
                'message'     => 'Favorite status updated successfully',
                'is_favorite' => $photoState->is_favorite,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating favorite status: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function getProject($id)
    {
        try {
            $project = Project::findOrFail($id);

            // Get surfaces for the specific project's company and tour
            $surfaces = $project
            ? Curate2dSurface::where('project_id', $project->id)->get()
            : [];

            $artworkCollections = $project
            ? $project->artworkCollections()->withCount('artworks')->get()
            : [];

            $allCollections = $project ? ArtworkCollection::forCompany($project->company_id)
                ->withCount('artworks')
                ->get() : [];

            $photos       = Photo::where('project_id', $project->id)->get();
            $layoutPhotos = $this->getLayoutPhotos($project);

            return response()->json([
                'success'            => true,
                'id'                 => $project->id,
                'name'               => $project->name,
                'surfaces'           => $surfaces,
                'artworkCollections' => $artworkCollections,
                'allCollections'     => $allCollections,
                'layoutPhotos'       => $layoutPhotos,
                'photos'             => $photos,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Project not found',
            ], 404);
        }
    }

    /**
     * Get layout photos for a project
     *
     * @param Project $project
     * @return array
     */
    private function getLayoutPhotos(Project $project): array
    {
        $photoState = PhotoState::where('project_id', $project->id)->first();

        if (! $photoState) {
            return [];
        }

        $layoutPhotos = [];
        $photoStates  = PhotoState::where('project_id', $project->id)->get();

        foreach ($photoStates as $state) {
            $layout = $project->layouts()->find($state->layout_id);
            if ($layout) {
                if (! isset($layoutPhotos[$layout->id])) {
                    $layoutPhotos[$layout->id] = [
                        'layout_id'      => $layout->id,
                        'name'           => $layout->name,
                        'thumbnail_urls' => [],
                        'photos'         => [],
                        'is_favorites'   => [],
                    ];
                }

                if ($state->thumbnail_url && ! in_array($state->thumbnail_url, $layoutPhotos[$layout->id]['thumbnail_urls'])) {
                    $layoutPhotos[$layout->id]['thumbnail_urls'][] = $state->thumbnail_url;
                }

                if (! in_array($state->photo_id, $layoutPhotos[$layout->id]['photos'])) {
                    $layoutPhotos[$layout->id]['photos'][] = $state->photo_id;
                }

                $layoutPhotos[$layout->id]['is_favorites'][] = $state->is_favorite;
            }
        }

        return $layoutPhotos;
    }

    public function storeProject(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'name'  => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png|max:2048',
            ]);

            $image = $request->file('image');
            $path  = $image->store('media/project-thumbnails', 'public');

            // Create the project
            $project = Project::create([
                'company_id'     => auth()->user()->company_id ?? 3, // Get company_id from authenticated user or default to 1
                'tour_id'        => 0,
                'name'           => $request->input('name'), // Use input() method to get the name
                'is_curate_2d'   => true,
                'background_url' => '/storage/' . $path,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Project created successfully',
                'project' => $project,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function updateProject(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'name'       => 'required|string|max:255',
                'image'      => 'nullable|image|mimes:jpeg,png|max:2048',
                'project_id' => 'required|exists:projects,id',
            ]);

            $image = $request->file('image');
            $path  = $image->store('media/project-thumbnails', 'public');

            $project                 = Project::findOrFail($request->project_id);
            $project->name           = $request->input('name');
            $project->background_url = '/storage/' . $path;
            $project->save();

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully',
                'project' => $project,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

}
