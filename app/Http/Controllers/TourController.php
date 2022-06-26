<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Spot;
use App\Models\Tour;
use App\Services\SpotXmlGenerator;
use Illuminate\Http\Request;
use SimpleXMLElement;

class TourController extends Controller
{
    public function surfaces(Tour $tour)
    {
        $project = Project::findOrFail(request('project_id'));
        $data = array();
        $data['project'] = $project;
        $data['tour'] = $tour;
        $data['surfaces'] = $tour->surfaces()->with([
            'states' => fn($query) => $query->project($project->id),
            'media'
        ])->get();

        return view('pages.surfaces', $data);
    }

    public function show(Tour $tour)
    {
        $request = request();

        if ($spot_id = request('spot_id')){
            $spot = $tour->spots()->findOrFail($spot_id);
        } else {
            $spot = $tour->spots->first();
        }

        if ($project_id = request('project_id')){
            $project = Project::findOrFail($project_id);
        }

        $data = array();
        $data['tour'] = $tour;
        $data['spot'] = $spot;
        $data['project'] = $project ?? null;

        $data['hash'] = 0;
        $data['tracker'] = request('tracker', 0);
        $data['shareType'] = 0;

        return view('pages.tour', $data);
    }
}
