<?php

namespace App\Http\Controllers;

use App\Models\Layout;
use App\Models\Project;
use App\Models\Spot;
use App\Models\Tour;

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

        $spotQuery = Spot::with([
            'surfaces',
            'surfaces.states.media',
            'surfaces.states.likes',
        ])->when($layout_id, fn($query) => $query->with([
            'surfaces.states' => fn($query) => $query->forLayout($layout->id),
        ]))->where('tour_id', $tour->id);

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

        $data = array();
        $data['tour'] = $tour;
        $data['spot'] = $spot;
        $data['project'] = $project ?? null;
        $data['layout'] = $layout ?? null;
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
