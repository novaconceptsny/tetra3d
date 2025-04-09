<?php
namespace App\Http\Controllers;

use App\Models\Layout;
use App\Models\Photo;
use App\Models\Project;
use App\Models\Spot;
use App\Models\SurfaceState;
use Illuminate\Http\Request;

class PhotoStateController extends Controller
{
    public function show(Photo $photo)
    {
        $layout  = Layout::findOrFail(request('layout_id'));
        $project = Project::findOrFail($layout->project_id);

        $surface->load([
            'states' => fn($query) => $query->forLayout($layout->id),
            'states.artworks.media',
            'states.comments.user',
            'states.likes.user',
        ]);

        if ($spot_id = request('spot_id')) {
            $spot = Spot::findOrFail($spot_id);
        } else {
            $spot = $surface->tour->spots->first();
        }

        $referer      = str(request()->headers->get('referer'))->before('?');
        $versions_url = str(
            route('tours.surfaces', $spot->tour_id)
        )->before('?');

        $return_to_versions = request(
            'return_to_versions',
            $referer == $versions_url
        );

        $selectedSurfaceState = null;
        $create_new_state     = request('new');

        if ($surface_state_id = request('surface_state_id')) {
            $selectedSurfaceState = SurfaceState::findOrFail($surface_state_id);
        }

        if (! $create_new_state && ! $surface_state_id) {
            $selectedSurfaceState = $surface->getCurrentState($layout->id);
        }

        $surface->background_url = $surface->getFirstMediaUrl('background');

        $data            = [];
        $data['project'] = $project;
        $data['layout']  = $layout;
        $data['photo']   = $photo;

        $data['navEnabled']  = false;
        $data['navbarLight'] = true;

        $canvases = [];

        $canvases[$photo->id ?? 'new'] = [
            'canvasId'         => "artwork_canvas_" . ($photo->id ?? 'new'),
            'photo'            => $photo->only([
                'id',
                'name',
                'background_url',
                'data',
            ]),
            'assignedArtworks' => $assignedArtworks,
            'photoStateId'     => $photo?->id,
            'userId'           => auth()->id(),
            'spotId'           => $spot->id,
            'latestState'      => $photo ? $photo->canvas : [],
            'layoutId'         => $layout->id,
            'updateEndpoint'   => route('surfaces.update', [$surface, 'return_to_versions' => $return_to_versions]),
            'hlookat'          => request('hlookat', $spot->xml->view['hlookat']),
            'vlookat'          => request('vlookat', $spot->xml->view['vlookat']),
            'photoStateName'   => $photo->name ?? 'Untitled',
        ];

        $data['canvases'] = $canvases;

        return view('pages.photoeditor', $data);
    }

}
