<?php
namespace App\Http\Controllers;

use App\Models\Layout;
use App\Models\Photo;
use App\Models\Project;
use App\Models\ArtworkPhotoState;
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

            $layout  = Layout::findOrFail($layoutId);
            $project = Project::findOrFail($layout->project_id);

            // Fetch assigned artworks from artwork_photo_state table
            $assignedArtworks = ArtworkPhotoState::where('photo_id', $photo->id)
                ->with('artwork') // Make sure you have the relationship defined in the model
                ->get()
                ->map(function ($state) {
                    return [
                        'pos' => $state->pos,
                        'title' => $state->artwork->title,
                        'imgUrl' => $state->artwork->image_url, // Adjust this field name based on your artwork model
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
            $assignedArtworks = json_decode($request->assigned_artwork, true);
            
            // Log the incoming data
            \Log::info('Assigned Artwork:', ['data' => $assignedArtworks]);

            // Begin transaction
            \DB::beginTransaction();

            // Clear existing states for this photo
            ArtworkPhotoState::where('photo_id', $photo->id)->delete();

            // Store each artwork state
            foreach ($assignedArtworks as $artwork) {
                // Ensure pos is in the correct format
                $position = [
                    'x' => $artwork['pos']['x'],
                    'y' => $artwork['pos']['y']
                ];

                ArtworkPhotoState::create([
                    'artwork_id' => $artwork['artworkId'],
                    'photo_id' => $photo->id,
                    'pos' => $position  // This will be stored as {"x": 48.905914306640625, "y": 239.74022674560547}
                ]);
            }

            // Commit transaction
            \DB::commit();
            
            // Log the stored data for verification
            \Log::info('Stored position data:', ['last_position' => $position]);
            
            return redirect()->route('photo.index')->with('success', 'Photo states updated successfully');
        } catch (\Exception $e) {
            // Rollback transaction on error
            \DB::rollBack();
            
            \Log::error('Error updating photo states: ' . $e->getMessage());
            return redirect()->route('photo.index')->with('error', 'Failed to update photo states');
        }
    }

}
