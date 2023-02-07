<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Spot;
use App\Models\Tour;

class TourController extends Controller
{
    public function surfaces(Tour $tour)
    {
        $project = Project::with('contributors.media')->findOrFail(request('project_id'));

        $tour->load([
            'surfaces' => fn($query) => $query->with([
                'states.user',
                'states.media',
                'states.likes',
                'states' => fn($query) => $query->forProject($project->id),
                'media',
            ]),
        ]);

        $data = array();
        $data['project'] = $project;
        $data['tour'] = $tour;
        $data['surfaces'] = $tour->surfaces;

        return view('pages.surfaces', $data);
    }

    public function show(Tour $tour)
    {
        $this->redirectIfTourIsShared();

        $spot_id = request('spot_id');

        if ($project_id = request('project_id')) {
            $project = Project::relevant()->findOrFail($project_id);
        } else {
            abort_if(!user()->isAdmin(), 404);
        }

        $spotQuery = Spot::with([
            'surfaces',
            'surfaces.states.media',
            'surfaces.states.likes',
        ])->when($project_id, fn($query) => $query->with([
            'surfaces.states' => fn($query) => $query->forProject($project->id),
        ]))->where('tour_id', $tour->id);

        $spot = $spot_id ? $spotQuery->findOrFail($spot_id)
            : $spotQuery->firstOrFail();

        $spot->surfaces->map(function ($surface) use ($project_id) {
            if ( ! $project_id) {
                return $surface;
            }

            $surface->setRelation('state',
                $surface->states
                    ->where('active', 1)
                    ->first()
            );
        });

        $data = array();
        $data['tour'] = $tour;
        $data['spot'] = $spot;
        $data['project'] = $project ?? null;
        $data['navEnabled'] = false;
        $data['navbarLight'] = true;

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
