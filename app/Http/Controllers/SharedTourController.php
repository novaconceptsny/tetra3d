<?php

namespace App\Http\Controllers;

use App\Models\SharedTour;
use App\Models\Spot;

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

        $data = array();
        $data['spot'] = $spot;
        $data['tour'] = $tour;
        $data['layout'] = $layout;
        $data['project'] = $project;
        $data['shared_tour_id'] = $sharedTour->id;
        $data['shared_spot_id'] = $sharedTour->spot_id;
        $data['navEnabled'] = false;
        $data['navbarLight'] = true;
        $data['modelList'] = [];
        $data['test'] = "sharedtourcontroller";

        return view('pages.tour', $data);
    }
}
