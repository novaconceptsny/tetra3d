<?php

namespace App\Http\Controllers;

use App\Models\SharedTour;
use App\Models\SpotsPosition;
use App\Models\TourModel;
use App\Models\Sculpture;
use App\Models\Spot;
use App\Models\SculptureModel;
use App\Models\ArtworkProject;

class SharedTourController extends Controller
{
    public function show(SharedTour $sharedTour)
    {
        $layout = $sharedTour->layout;
        $tour = $sharedTour->layout->tour;
        $project = $sharedTour->layout->project;

        $spot_id = request('spot_id', $sharedTour->spot_id);

        if ($sharedTour->spot_id && $sharedTour->spot_id != $spot_id){
            abort(404);
        }

        $spotQuery = Spot::query()
            ->with([
                'surfaces',
                'surfaces.states.media',
                'surfaces.states.likes',
                'surfaces.states' => fn($query) => $query->forLayout($layout->id),
            ])
            ->where('tour_id', $tour->id);

        $spot = $spot_id ? $spotQuery->findOrFail($spot_id) : $spotQuery->first();

        $spot->surfaces->map(function ($surface) use ($sharedTour){
            $surface->setRelation('state',
                $surface->states
                    ->where('active', 1)
                    ->first()
            );
        });

        $artwork_collections = $project ? ArtworkProject::where('project_id', $project->id)->get() : array();

        $sculpture_list = array();

        foreach($artwork_collections as $artwork_collection) {
            $sculpture_list[] = $artwork_collection->artwork_collection_id;
        }

        $sculptures = !empty($sculpture_list) ? SculptureModel::whereIn('artwork_collection_id', $sculpture_list)->get() : array();

        $tourModel = $tour ? TourModel::where('tour_id', $tour->id)->get() : null;
        if ($tourModel !== null && !$tourModel->isEmpty()) {
            $tourModel = $tourModel[0];
        } else {
            $tourModel = null;
            $sculptures = array();
        }

        $sculptureData = $layout ? Sculpture::where('layout_id', $layout->id)->get() : null;

        $spotPosition = $spot ? SpotsPosition::where('spot_id', $spot->id)->get() : null;
        if ($spotPosition !== null && !$spotPosition->isEmpty()) {
            $spotPosition = $spotPosition[0];
        } else {
            $spotPosition = null;
        }

        $data = array();
        $data['spot'] = $spot;
        $data['tour'] = $tour;
        $data['layout'] = $layout;
        $data['project'] = $project;
        $data['shared_tour_id'] = $sharedTour->id;
        $data['shared_spot_id'] = $sharedTour->spot_id;
        $data['navEnabled'] = false;
        $data['navbarLight'] = true;
        $data['tourModel'] = $tourModel;
        $data['sculptureData'] = $sculptureData;
        $data['spotPosition'] = $spotPosition;
        $data['sculptures'] = $sculptures;

        return view('pages.tour', $data);
    }
}
