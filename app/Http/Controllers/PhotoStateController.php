<?php
namespace App\Http\Controllers;

use App\Models\Layout;
use App\Models\Photo;
use App\Models\Project;
use App\Models\ArtworkPhotoState;
use App\Models\PhotoState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class PhotoStateController extends Controller
{
    public function show(Photo $photo)
    {
        try {
            $layoutId = request('layout_id');
            if (! $layoutId) {
                throw new \Exception('Layout ID is required');
            }

            $layout = Layout::findOrFail($layoutId);
            $project = Project::findOrFail($layout->project_id);

            // Fetch assigned artworks from artwork_photo_state table using photo_state_id
            $assignedArtworks = ArtworkPhotoState::where('surface_id', $photo->surface_id)
                ->where('layout_id', $layoutId)
                ->with('artwork')
                ->get()
                ->map(function ($state) {
                    return [
                        'id' => $state->id,
                        'pos' => $state->pos,
                        'title' => $state->artwork->title,
                        'imgUrl' => $state->artwork->image_url,
                        'artworkId' => (string) $state->artwork_id,
                    ];
                })
                ->toArray();

            $data                = [];
            $data['project']     = $project;
            $data['layout']      = $layout;
            $data['surface']     = $photo;
            $data['navEnabled']  = false;
            $data['navbarLight'] = true;

            $canvases         = [];

            $canvases[$photo->id ?? 'new'] = [
                'canvasId'         => "artwork_canvas_" . ($photo->id ?? 'new'),
                'surface'          => $photo->only([
                    'id',
                    'name',
                    'background_url',
                    'data',
                ]),
                'assignedArtworks' => $assignedArtworks,
                'userId'           => auth()->id(),
                'projectId'        => $project->id,
                'photoId'          => $photo->id,
                'layoutId'         => $layout->id,
                'updateEndpoint'   => route('photos.update', [$photo]),
                'photoEditable'    => true,
            ];

            $data['canvases'] = $canvases;
            return view('pages.photoeditor', $data);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error in PhotoStateController@show: ' . $e->getMessage());

            // Redirect back with error message
            return redirect()->back()->with('error', 'Unable to load photo editor: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Photo $photo)
    {


        try {
            // Validate the request
            $request->validate([
                'layout_id' => 'required',
                'assigned_artwork' => 'required|string'
            ]);

            $assignedArtworks = json_decode($request->assigned_artwork, true);
            $layoutId = $request->layout_id;

            // Get the photo state
            $photoState = PhotoState::where('layout_id', $layoutId)
                ->where('photo_id', $photo->id)
                ->firstOrFail();
                         
            // Log the incoming data
            \Log::info('Assigned Artwork:', ['data' => $assignedArtworks]);

            // Decode the base64 thumbnail
            $thumbnailData = $request->input('thumbnail');
            
            if ($thumbnailData) {
                // Remove the data:image/jpeg;base64, part
                $thumbnailData = preg_replace('/^data:image\/\w+;base64,/', '', $thumbnailData);
                $thumbnailData = base64_decode($thumbnailData);
                
                // Create directory path using photo_state_id with thumbnail folder
                $dirPath = 'media/photo_states/' . $photoState->id . '/thumbnail';
                
                // Use thumbnail.jpg as filename
                $filename = $dirPath . '/thumbnail.jpg';
                
                // Ensure the directory exists
                Storage::disk('public')->makeDirectory($dirPath);
                
                // Store the thumbnail
                Storage::disk('public')->put($filename, $thumbnailData);
                
                // Update your photo state model with the thumbnail path
                $photoState->thumbnail_url = '/storage/' . $filename;
                $photoState->save();
            }


            try {
                // Clear existing states for this photo_state
                ArtworkPhotoState::where('layout_id', $layoutId)
                    ->where('surface_id', $photo->surface_id)
                    ->delete();

                // Store each artwork state
                foreach ($assignedArtworks as $artwork) {
                    // Ensure pos is in the correct format
                    $position = [
                        'x' => $artwork['pos']['x'],
                        'y' => $artwork['pos']['y']
                    ];

                    ArtworkPhotoState::create([
                        'artwork_id' => $artwork['artworkId'],
                        'photo_state_id' => $photoState->id,
                        'surface_id' => $photo->surface_id,
                        'layout_id' => $layoutId,
                        'pos' => $position
                    ]);
                }

                
                return response()->json([
                    'success' => true,
                    'message' => 'Photo states updated successfully',
                ]);

             //   return redirect()->route('photo.index')->with('success', 'Photo states updated successfully');

                
            } catch (\Exception $e) {
                \DB::rollBack();
                throw $e;
            }
        } catch (\Exception $e) {
            \Log::error('Error updating photo states: ' . $e->getMessage());
            
            return response()->json([
                'success' => false,
                'message' => 'Failed to update photo states: ' . $e->getMessage()
            ], 500);
        }
    }

}