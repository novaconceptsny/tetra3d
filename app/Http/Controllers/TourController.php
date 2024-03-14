<?php

namespace App\Http\Controllers;

use App\Models\Layout;
use App\Models\Project;
use App\Models\Spot;
use App\Models\Tour;
use App\Models\Sculpture;
use App\Models\SculptureModel;
use App\Models\TourModel;
use App\Models\SpotsPosition;
use App\Models\ArtworkProject;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function surfaces(Tour $tour)
    {
        $layout = Layout::with('project')->findOrFail(request('layout_id'));

        $tour->load([
            'surfaces' => fn($query) => $query->with([
                'states.user',
                'states.media',
                'states.likes',
                'states' => fn($query) => $query->forLayout($layout->id),
                'media',
            ]),
        ]);

        $data = array();
        $data['layout'] = $layout;
        $data['tour'] = $tour;
        $data['surfaces'] = $tour->surfaces;
        $data['navEnabled'] = false;

        return view('pages.surfaces', $data);
    }

    public function show(Tour $tour)
    {
        // redirect if tour is shared
        if (request('shared_tour_id')) {
            return $this->redirectIfTourIsShared();
        }

        // else, we check auth
        if (!auth()->check()){
            return redirect()->route('login');
        }

        $spot_id = request('spot_id');

        if ($layout_id = request('layout_id')) {
            $layout = Layout::findOrFail($layout_id);
            $project = Project::relevant()->findOrFail($layout->project_id);
        } else {
            abort_if(!user()->isAdmin(), 404);
        }

        $spotQuery = Spot::query()
            ->with([
                'surfaces',
                'surfaces.states.media',
                'surfaces.states.likes',
            ])
            ->when($layout_id, fn($query) => $query->with([
                'surfaces.states' => fn($query) => $query->forLayout($layout->id),
            ]))
            ->where('tour_id', $tour->id);

        $spot = $spot_id ? $spotQuery->findOrFail($spot_id)
            : $spotQuery->firstOrFail();

        $spot->surfaces->map(function ($surface) use ($layout_id) {
            if ( ! $layout_id) {
                return $surface;
            }

            $surface->setRelation('state',
                $surface->states
                    ->where('active', 1)
                    ->first()
            );
        });

        $artwork_collections = ArtworkProject::where('project_id', $project->id)->get();
        $sculpture_list = array();

        foreach($artwork_collections as $artwork_collection) {
            $sculpture_list[] = $artwork_collection->artwork_collection_id;
        }
        $sculptures = SculptureModel::whereIn('artwork_collection_id', $sculpture_list)->get();
        foreach($sculptures as $row) {
            $row->data = json_decode($row->data);
            $row->data->length = number_format((float)$row->data->length, 2);
            $row->data->width = number_format((float)$row->data->width, 2);
            $row->data->height = number_format((float)$row->data->height, 2);
            $row->data = '['.$row->data->length.'x'.$row->data->width.'x'.$row->data->height.' meter'.']';
        }

        $tourModel = $tour ? TourModel::where('tour_id', $tour->id)->get() : null;
        if ($tourModel !== null && !$tourModel->isEmpty()) {
            $tourModel = $tourModel[0];
        } else {
            $tourModel = null;
        }

        $sculptureData = $layout? Sculpture::where('layout_id', $layout->id)->get() : null;

        $spotPosition = $spot? SpotsPosition::where('spot_id', $spot->id)->get() : null;
        if ($spotPosition !== null && !$spotPosition->isEmpty()) {
            $spotPosition = $spotPosition[0];
        } else {
            $spotPosition = null;
        }

        $data = array();
        $data['tour'] = $tour;
        $data['spot'] = $spot;
        $data['project'] = $project ?? null;
        $data['layout'] = $layout ?? null;
        $data['navEnabled'] = false;
        $data['navbarLight'] = true;
        $data['sculptures'] = $sculptures;
        $data['tourModel'] = $tourModel;
        $data['sculptureData'] = $sculptureData;
        $data['spotPosition'] = $spotPosition;

        return view('pages.tour', $data);
    }

    private function redirectIfTourIsShared()
    {
        if (request('shared') && request('shared_tour_id')) {
            return redirect()->route('shared-tours.show', [
                request('shared_tour_id'),
                'spot_id' => request('spot_id'),
            ]);
        }
    }

}
