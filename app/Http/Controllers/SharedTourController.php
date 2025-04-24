<?php
namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\ArtworkProject;
use App\Models\ArtworkSurfaceState;
use App\Models\Sculpture;
use App\Models\SculptureModel;
use App\Models\SharedTour;
use App\Models\Spot;
use App\Models\SpotsPosition;
use App\Models\SurfaceInfo;
use App\Models\SurfaceState;
use App\Models\TourModel;

class SharedTourController extends Controller
{
    public function show(SharedTour $sharedTour)
    {
        $layout  = $sharedTour->layout;
        $tour    = $sharedTour->layout->tour;
        $project = $sharedTour->layout->project;

        $spot_id = request('spot_id', $sharedTour->spot_id);

        if ($sharedTour->spot_id && $sharedTour->spot_id != $spot_id) {
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

        $spot->surfaces->map(function ($surface) use ($sharedTour) {
            $surface->setRelation('state',
                $surface->states
                    ->where('active', 1)
                    ->first()
            );
        });

        $artwork_collections = $project ? ArtworkProject::where('project_id', $project->id)->get() : [];

        $sculpture_list = [];

        foreach ($artwork_collections as $artwork_collection) {
            $sculpture_list[] = $artwork_collection->artwork_collection_id;
        }

        $sculptures = ! empty($sculpture_list) ? SculptureModel::whereIn('artwork_collection_id', $sculpture_list)->get() : [];

        $tourModel = $tour ? TourModel::where('tour_id', $tour->id)->get() : null;
        if ($tourModel !== null && ! $tourModel->isEmpty()) {
            $tourModel = $tourModel[0];
        } else {
            $tourModel  = null;
            $sculptures = [];
        }

        $sculptureData = $layout ? Sculpture::where('layout_id', $layout->id)->get() : null;

        $spotPosition = $spot ? SpotsPosition::where('spot_id', $spot->id)->get() : null;
        if ($spotPosition !== null && ! $spotPosition->isEmpty()) {
            $spotPosition = $spotPosition[0];
        } else {
            $spotPosition = null;
        }

        $stateArray = $layout
        ? SurfaceState::where('layout_id', $layout->id)->pluck('id')->toArray()
        : [];
        $artworkData  = [];
        $surfaceData  = [];
        $surfaceInfos = SurfaceInfo::where('tour_id', $tour->id)->get();

        for ($index = 0; $index < count($surfaceInfos); $index++) {
            $normal   = $surfaceInfos[$index]->normalvector;
            $normal   = array_map('floatval', $normal);
            $startPos = $surfaceInfos[$index]->start_pos;
            $startPos = array_map('floatval', $startPos);

            $surfaceData[$index]['surface_id'] = $surfaceInfos[$index]->surface_id;
            $surfaceData[$index]['start_pos']  = $startPos;
            $surfaceData[$index]['width']      = $surfaceInfos[$index]->width;
            $surfaceData[$index]['height']     = $surfaceInfos[$index]->height;
            $surfaceData[$index]['normal']     = $normal;
        }

        if ($stateArray) {
            foreach ($stateArray as $stateId) {
                $artworkRecords = ArtworkSurfaceState::where('surface_state_id', $stateId)->get()->toArray(); // Convert to array

                $filteredRecords = array_filter($artworkRecords, function ($record) {
                    return ! is_null($record['position_x']) && ! is_null($record['position_y']) && ! is_null($record['position_z']);
                });
                if (count($filteredRecords) > 0) {
                    $artworkData = array_merge($artworkData, $filteredRecords); // Merge filtered records into artworkData
                }
            }
        }

        for ($index = 0; $index < count($artworkData); $index++) {
            $artworkId = $artworkData[$index]['artwork_id'] ?? null; // Safely access artwork_id
            $surfacestateId = $artworkData[$index]['surface_state_id'] ?? null;
            if ($artworkId) {
                $artInfo     = Artwork::find($artworkId); // Find by ID
                $surfaceInfo = SurfaceState::find($surfacestateId);
                $surfaceInfo = SurfaceInfo::where('surface_id', $surfaceInfo->surface_id)->first();

                if ($artInfo) {
                    $normal = $surfaceInfo->normalvector;
                    $normal = array_map('floatval', $normal);

                    $artworkData[$index]['surface_id']     = $surfaceInfo->surface_id;
                    $artworkData[$index]['normal']         = $normal;
                    $artworkData[$index]['image_url']      = $artInfo->image_url;
                    $artworkData[$index]['surfacestateId'] = $surfacestateId;
                    $artworkData[$index]['imageWidth']     = ($artInfo->data['width_inch'] ?? 0) * 0.0254;  // Safely access width_inch
                    $artworkData[$index]['imageHeight']    = ($artInfo->data['height_inch'] ?? 0) * 0.0254; // Safely access height_inch
                }
            }
        }

        $data                   = [];
        $data['spot']           = $spot;
        $data['tour']           = $tour;
        $data['layout']         = $layout;
        $data['project']        = $project;
        $data['shared_tour_id'] = $sharedTour->id;
        $data['shared_spot_id'] = $sharedTour->spot_id;
        $data['navEnabled']     = false;
        $data['navbarLight']    = true;
        $data['tourModel']      = $tourModel;
        $data['sculptureData']  = $sculptureData;
        $data['artworkData']    = $artworkData;
        $data['spotPosition']   = $spotPosition;
        $data['sculptures']     = $sculptures;
        $data['surfaceData']    = $surfaceData;

        return view('pages.tour', $data);
    }
}
