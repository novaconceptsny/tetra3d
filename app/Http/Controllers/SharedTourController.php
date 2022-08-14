<?php

namespace App\Http\Controllers;

use App\Models\SharedTour;
use App\Models\Spot;

class SharedTourController extends Controller
{
    public function show(SharedTour $sharedTour)
    {
        $tour = $sharedTour->tour;
        $project = $sharedTour->project;

        $spot_id = request('spot_id');

        $spotQuery = Spot::with([
            'surfaces',
            'surfaces.states.media',
            'surfaces.states.likes',
            'surfaces.states' => fn($query) => $query->forProject($project->id),
        ])->where('tour_id', $tour->id);

        $spot = $spot_id ? $spotQuery->findOrFail($spot_id) : $spotQuery->first();

        $spot->surfaces->map(function ($surface) use ($sharedTour){
            $surface_state_id = $sharedTour->surface_states->get($surface->id);

            if (!$surface_state_id){
                return $surface;
            }

            $surface->setRelation('state',
                $surface->states->where('id', $surface_state_id)->first()
            );
        });

        $data = array();
        $data['spot'] = $spot;
        $data['tour'] = $tour;
        $data['shared_tour_id'] = $sharedTour->id;

        return view('pages.tour', $data);
    }
}
