<?php
namespace App\Http\Controllers;

use App\Models\Layout;
use App\Models\Photo;
use App\Models\Project;
use App\Models\ArtworkPhotoState;
use App\Models\PhotoState;
use Illuminate\Http\Request;

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

            // Get the photo state
            $photoState = PhotoState::where('layout_id', $layoutId)
                ->where('photo_id', $photo->id)
                ->firstOrFail();

            // Fetch assigned artworks from artwork_photo_state table using photo_state_id
            $assignedArtworks = ArtworkPhotoState::where('photo_state_id', $photoState->id)
                ->with('artwork')
                ->get()
                ->map(function ($state) {
                    return [
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

            // Begin transaction
            \DB::beginTransaction();

            try {
                // Clear existing states for this photo_state
                ArtworkPhotoState::where('photo_state_id', $photoState->id)->delete();

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
                        'pos' => $position
                    ]);
                }

                // Commit transaction
                \DB::commit();
                
                return redirect()->route('photo.index')->with('success', 'Photo states updated successfully');

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
