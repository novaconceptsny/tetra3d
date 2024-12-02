<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Artwork;
use App\Models\Layout;
use App\Models\Project;
use App\Models\Spot;
use App\Models\Surface;
use App\Models\SurfaceState;
use App\Models\ArtworkModel;
use App\Models\SurfaceInfo;
use Illuminate\Http\Request;

class SurfaceStateController extends Controller
{
    public function show(Surface $surface)
    {
        $layout = Layout::findOrFail(request('layout_id'));
        $project = Project::findOrFail($layout->project_id);

        $surface->load([
            'states' => fn($query) => $query->forLayout($layout->id),
            'states.artworks.media',
            'states.comments.user',
            'states.likes.user'
        ]);

        if ($spot_id = request('spot_id')){
            $spot = Spot::findOrFail($spot_id);
        } else {
            $spot = $surface->tour->spots->first();
        }


        $referer = str(request()->headers->get('referer'))->before('?');
        $versions_url = str(
            route('tours.surfaces', $spot->tour_id)
        )->before('?');

        $return_to_versions = request(
            'return_to_versions',
            $referer == $versions_url
        );

        $selectedSurfaceState = null;
        $create_new_state = request('new');

        if ($surface_state_id = request('surface_state_id')){
            $selectedSurfaceState = SurfaceState::findOrFail($surface_state_id);
        }

        if (!$create_new_state && !$surface_state_id){
            $selectedSurfaceState = $surface->getCurrentState($layout->id);
        }

        $surface->background_url = $surface->getFirstMediaUrl('background');


        $data = array();
        $data['project'] = $project;
        $data['layout'] = $layout;
        $data['tour'] = $surface->tour;
        $data['spot'] = $spot;
        $data['surface'] = $surface;
        $data['selectedSurfaceState'] = $selectedSurfaceState;
        $data['currentSurfaceStateId'] = $surface->getCurrentState($layout->id)?->id;

        $data['navEnabled'] = false;
        $data['navbarLight'] = true;

        $canvases = array();

        if (!$surface->states->count() || $create_new_state){
            $newState = new SurfaceState();

            // initialize new state
            if (!$surface->states->count()){
                $newState->user_id = auth()->id();
                $newState->layout_id = $layout->id;
                $newState->surface_id = $surface->id;
                $newState->name = 'Version 1';
                $newState->save();

                $data['currentSurfaceStateId'] = $newState->id;
                $data['selectedSurfaceState'] = $newState;
            }

            $surface->states[] = $newState;
        }

        foreach ($surface->states as $surfaceState){

            $assignedArtworks = $surfaceState?->artworks->map(function ($artwork){
                $artwork->image_url.= "?uuid=". str()->uuid();
                return $artwork;
            });

            $canvases[$surfaceState->id ?? 'new'] = [
                'canvasId' => "artwork_canvas_". ($surfaceState->id ?? 'new'),
                'surface' => $surface->only([
                    'id', 'name', 'background_url', 'data'
                ]),
                'assignedArtworks' => $assignedArtworks,
                'surfaceStateId' => $surfaceState?->id,
                'userId' => auth()->id(),
                'spotId' => $spot->id,
                'latestState' => $surfaceState ? $surfaceState->canvas : [],
                'layoutId' => $layout->id,
                'updateEndpoint' => route('surfaces.update', [$surface, 'return_to_versions' => $return_to_versions]),
                'hlookat' => request('hlookat', $spot->xml->view['hlookat']),
                'vlookat' => request('vlookat', $spot->xml->view['vlookat']),
                'surfaceStateName' => $surfaceState->name ?? 'Untitled',
            ];
        }

        $data['canvases'] = $canvases;

        return view('pages.editor', $data);
    }

    /**
     * @throws \Exception
     */
    public function update(Request $request, Surface $surface)
    {
        $layout = Layout::findOrFail(request('layout_id'));
    
        $request->validate([
            'layout_id' => 'required',
            'assigned_artwork' => 'required',
        ]);
    
        // Update the `updated_at` field of the `$layout` to the current time
        $layout->touch();

        $boundingBoxWidth = $surface->data["bounding_box_width"];
        $boundingBoxHeight = $surface->data["bounding_box_height"];
        $boundingBoxTop = $surface->data["bounding_box_top"];
        $boundingBoxLeft = $surface->data["bounding_box_left"];
    
        $assigned_artworks = array();
        foreach (json_decode($request->assigned_artwork, true) as $artwork){
             // Calculate top and left distances

          
            $assigned_artworks[] = array(
                'artwork_id' => $artwork['artworkId'],
                'top_position' => $artwork['topPosition'],
                'left_position' => $artwork['leftPosition'],
                'crop_data' => $artwork['cropData'],
                'override_scale' => $artwork['overrideScale'],
            );

             // Fetch surface information using surface_id
            $surfaceInfo = SurfaceInfo::where('surface_id', $surface->id)->first();
            $artworkInfo = Artwork::where('id', $artwork['artworkId'])->first();

            $artWidth =  $artworkInfo->data["width_inch"] ;
            $artHeight =  $artworkInfo->data["height_inch"];
            $artScale =  $artworkInfo->data["scale"];

            if ($surfaceInfo) {
                $topLeftCorner = json_decode($surfaceInfo->start_pos, true); // Start position as ['x', 'y', 'z']
                $normal = json_decode($surfaceInfo->normalvector, true);    // Normal vector as ['x', 'y', 'z']
                $planeWidth = $surfaceInfo->width;                         // Width in meters
                $planeHeight = $surfaceInfo->height;                       // Length in meters
         
                // Normalize the normal vector
                $normalizedNormal = $this->normalize($normal);

                // Create the basis vectors (u, v)
                $arbitraryVector = ['x' => 1, 'y' => 0, 'z' => 0];
                $dotProduct = $normalizedNormal['x'] * $arbitraryVector['x'] +
                            $normalizedNormal['y'] * $arbitraryVector['y'] +
                            $normalizedNormal['z'] * $arbitraryVector['z'];
                if (abs($dotProduct) === 1) {
                    $arbitraryVector = ['x' => 0, 'y' => 1, 'z' => 0];
                }

                // Compute u and v vectors based on the normal
                $u = $this->normalize($this->crossProduct($normalizedNormal, $arbitraryVector));
                $v = $this->normalize($this->crossProduct($normalizedNormal, $u));

                // Adjust the direction of u and v vectors to match the coordinate system
                $u = [
                    'x' => -$u['x'], // Reverse the x direction for "Right = -x"
                    'y' => -$u['y'], // Reverse the y direction for "Up = -y"
                    'z' => -$u['z'], // (z-direction for u is not relevant here)
                ];
                $v = [
                    'x' => -$v['x'], // (x-direction for v is not relevant here)
                    'y' => -$v['y'], // Reverse the y direction for "Up = -y"
                    'z' => -$v['z'], // Reverse the z direction for "Forward = +z"
                ];


                // Calculate the target position in 3D space
                $xDistance = ($artwork['leftPosition'] - $boundingBoxLeft + $artWidth / 2) / $boundingBoxWidth * $planeWidth;
                $yDistance = ($artwork['topPosition'] - $boundingBoxTop + $artHeight / 2) / $boundingBoxHeight * $planeHeight;


                $targetPosition = [
                    'x' => $topLeftCorner['x'] + $u['x'] * $xDistance + $v['x'] * $yDistance,
                    'y' => $topLeftCorner['y'] + $u['y'] * $xDistance + $v['y'] * $yDistance,
                    'z' => $topLeftCorner['z'] + $u['z'] * $xDistance + $v['z'] * $yDistance,
                ];

                // $targetPosition = [
                //     'x' => $topLeftCorner['x'] -  $xDistance ,
                //     'y' => $topLeftCorner['y'] -   $yDistance,
                //     'z' => $topLeftCorner['z'],
                // ];

                 // Insert into ArtworkModel table
                ArtworkModel::updateOrCreate(
                    [
                        'layout_id' => $layout->id,
                        'artwork_id' => $artwork['artworkId'],
                    ],
                    [
                        'position_x' => $targetPosition['x'],
                        'position_y' => $targetPosition['y'],
                        'position_z' => $targetPosition['z'],
                    ]
                );
            }

        }

        if ($request->new) {
            $state = $surface->createNewState($request->layout_id);
            $state->update([
                'name' => $request->name,
            ]);
        } else {
            $state = $request->get('surface_state_id')
                ? SurfaceState::findOrFail($request->surface_state_id)
                : $surface->getCurrentState($request->layout_id);
        }

        $state->update([
            'canvas' => json_decode($request->canvasState, true),
        ]);

        $state->setAsActive();

        $state->addMediaFromBase64(resizeBase64Image(
            $request->thumbnail,
            $request->reverseScale
        ))
            ->usingFileName('thumbnail.png')
            ->toMediaCollection('thumbnail');

        $state->addMediaFromBase64(resizeBase64Image(
            $request->hotspot,
            $request->reverseScale
        ))
            ->usingFileName('hotspot.png')
            ->toMediaCollection('hotspot');

        $state->artworks()->detach();
        foreach ($assigned_artworks as $assigned_artwork){
            $state->artworks()->attach(
                $assigned_artwork['artwork_id'],
                $assigned_artwork
            );
        }
        $state->artworks()->sync($assigned_artworks);

        $route = $request->return_to_versions ? "tours.surfaces" : "tours.show";

        $state->addActivity($request->new ? 'created': 'updated');


        // return response()->json([
        //     'success' => true,
        // ]);
        return redirect()->route($route, [
            $surface->tour,
            'spot_id' => $request->spot_id,
            'layout_id' => $request->layout_id,
            'hlookat' => $request->hlookat,
            'vlookat' => $request->vlookat,
        ])->with('success', 'Surface updated');
    }

    public function destroy(SurfaceState $state)
    {
        $state->remove();
        return redirect()->back()->with('success', 'State removed');
    }

    public function active(SurfaceState $state)
    {
        $state->setAsActive();
        return redirect()->back()->with('success', 'Active set updated');
    }
// Helper Functions
    private function normalize($vector)
    {
        $length = sqrt($vector['x'] ** 2 + $vector['y'] ** 2 + $vector['z'] ** 2);
        return [
            'x' => $vector['x'] / $length,
            'y' => $vector['y'] / $length,
            'z' => $vector['z'] / $length,
        ];
    }

    private function crossProduct($v1, $v2)
    {
        return [
            'x' => $v1['y'] * $v2['z'] - $v1['z'] * $v2['y'],
            'y' => $v1['z'] * $v2['x'] - $v1['x'] * $v2['z'],
            'z' => $v1['x'] * $v2['y'] - $v1['y'] * $v2['x'],
        ];
    }

}
