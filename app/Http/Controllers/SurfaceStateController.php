<?php

namespace App\Http\Controllers;

use App\Models\Activity;
use App\Models\Artwork;
use App\Models\Layout;
use App\Models\Project;
use App\Models\Spot;
use App\Models\Surface;
use App\Models\SurfaceState;
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

        if ($spot_id = request('spot_id')) {
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

        if ($surface_state_id = request('surface_state_id')) {
            $selectedSurfaceState = SurfaceState::findOrFail($surface_state_id);
        }

        if (!$create_new_state && !$surface_state_id) {
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

        $states = SurfaceState::where('surface_id', $surface->id)
            ->where('layout_id', $layout->id)
            ->orderBy('id') // Ensure the lowest ID is first
            ->get();

        if ($states->count() === 0) {
            $newState = new SurfaceState();
            $newState->user_id = auth()->id();
            $newState->layout_id = $layout->id;
            $newState->surface_id = $surface->id;
            $newState->name = 'Version 1';
            $newState->save();

            $data['currentSurfaceStateId'] = $newState->id;
            $data['selectedSurfaceState'] = $newState;

            $surface->states[] = $newState;
        }


        foreach ($surface->states as $surfaceState) {
 
            $assignedArtworks = $surfaceState?->artworks->map(function ($artwork) {
                $artwork->image_url .= "?uuid=" . str()->uuid();
                return $artwork;
            });

            $canvases[$surfaceState->id ?? 'new'] = [
                'canvasId' => "artwork_canvas_" . ($surfaceState->id ?? 'new'),
                'surface' => $surface->only([
                    'id',
                    'name',
                    'background_url',
                    'data'
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

        $initial_artworks = json_decode($request->assigned_artwork, true);
        
        $assigned_artworks = array();

        $boundingBoxWidth = $surface->data["bounding_box_width"];
        $boundingBoxHeight = $surface->data["bounding_box_height"];
        $boundingBoxTop = $surface->data["bounding_box_top"];
        $boundingBoxLeft = $surface->data["bounding_box_left"];

        // Fetch surface information using surface_id
        $surfaceInfo = SurfaceInfo::where('surface_id', $surface->id)->first();

        foreach ($initial_artworks as $artwork) {

            $offset = 0.005;

            if ($surfaceInfo) {
                // If $surfaceInfo->start_pos is already an array:
                if (is_string($surfaceInfo->start_pos)) {
                    // Decode JSON string to an associative array
                    $topLeftCorner = json_decode($surfaceInfo->start_pos, true);
                } else {
                    // Directly assign if it's already an array
                    $topLeftCorner = $surfaceInfo->start_pos;
                }
                // Convert string values in $topLeftCorner to numbers
                $topLeftCorner = array_map('floatval', $topLeftCorner);

                $normal = $surfaceInfo->normalvector;

                // Convert string values in $normal to numbers
                $normal = array_map('floatval', $normal);

                $planeWidth = $surfaceInfo->width;                         // Width in meters
                $planeHeight = $surfaceInfo->height;                       // Length in meters

                // Calculate the target position in 3D space
                $xDistance = ($artwork['leftPosition'] - $boundingBoxLeft) / $boundingBoxWidth * $planeWidth;
                $yDistance = ($artwork['topPosition'] - $boundingBoxTop) / $boundingBoxHeight * $planeHeight;

                if ($normal['x'] == 0 && $normal['y'] == 0 && $normal['z'] == -1) {
                    $targetPosition = [
                        'x' => $topLeftCorner['x'] - $xDistance,
                        'y' => $topLeftCorner['y'] - $yDistance,
                        'z' => $topLeftCorner['z'] - $offset,
                    ];
                    $targetRotation = [
                        'x' => 0,
                        'y' => 0,
                        'z' => 0,
                    ];

                } elseif ($normal['x'] == 0 && $normal['y'] == 0 && $normal['z'] == 1) {
                    $targetPosition = [
                        'x' => $topLeftCorner['x'] + $xDistance,
                        'y' => $topLeftCorner['y'] - $yDistance,
                        'z' => $topLeftCorner['z'] + $offset
                    ];
                    $targetRotation = [
                        'x' => 0,
                        'y' => pi(),
                        'z' => 0,
                    ];
                } elseif ($normal['x'] == 1 && $normal['y'] == 0 && $normal['z'] == 0) {
                    $targetPosition = [
                        'x' => $topLeftCorner['x'] + $offset,
                        'y' => $topLeftCorner['y'] - $yDistance,
                        'z' => $topLeftCorner['z'] - $xDistance
                    ];
                    $targetRotation = [
                        'x' => 0,
                        'y' => pi() / 2,
                        'z' => 0,
                    ];
                } else {
                    // Default case if no known normal is matched
                    $targetPosition = [
                        'x' => $topLeftCorner['x'] - $offset,
                        'y' => $topLeftCorner['y'] - $yDistance,
                        'z' => $topLeftCorner['z'] + $xDistance
                    ];
                    $targetRotation = [
                        'x' => 0,
                        'y' => -pi() / 2,
                        'z' => 0,
                    ];
                }

                $assigned_artworks[] = array(
                    'artwork_id' => $artwork['artworkId'],
                    'top_position' => $artwork['topPosition'],
                    'left_position' => $artwork['leftPosition'],
                    'crop_data' => $artwork['cropData'],
                    'override_scale' => $artwork['overrideScale'],

                    'position_x' => $targetPosition['x'],
                    'position_y' => $targetPosition['y'],
                    'position_z' => $targetPosition['z'],
                    'rotation_x' => $targetRotation['x'],
                    'rotation_y' => $targetRotation['y'],
                    'rotation_z' => $targetRotation['z'],
                );
            } else {
                $assigned_artworks[] = array(
                    'artwork_id' => $artwork['artworkId'],
                    'top_position' => $artwork['topPosition'],
                    'left_position' => $artwork['leftPosition'],
                    'crop_data' => $artwork['cropData'],
                    'override_scale' => $artwork['overrideScale'],
                );
            }

        }

        // if ($request->new) {
        //     $state = $surface->createNewState($request->layout_id);
        //     $state->update([
        //         'name' => $request->name,
        //     ]);
        // } else {
        //     $state = $request->get('surface_state_id')
        //         ? SurfaceState::findOrFail($request->surface_state_id)
        //         : $surface->getCurrentState($request->layout_id);
        // }

        $state = $request->get('surface_state_id')
            ? SurfaceState::findOrFail($request->surface_state_id)
            : $surface->getCurrentState($request->layout_id);

        $state->update([
            'canvas' => json_decode($request->canvasState, true),
        ]);

        $state->setAsActive();

        // Only save screenshots if there are assigned artworks
        if (!empty($assigned_artworks)) {
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
        }

        $state->artworks()->detach();
        foreach ($assigned_artworks as $assigned_artwork) {
            $state->artworks()->attach(
                $assigned_artwork['artwork_id'],
                $assigned_artwork
            );
        }
        $state->artworks()->sync($assigned_artworks);

        $route = $request->return_to_versions ? "tours.surfaces" : "tours.show";

        $state->addActivity($request->new ? 'created' : 'updated');
        $state->save();

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
