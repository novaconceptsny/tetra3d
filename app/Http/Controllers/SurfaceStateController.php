<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\Project;
use App\Models\Spot;
use App\Models\Surface;
use App\Models\SurfaceState;
use Illuminate\Http\Request;

class SurfaceStateController extends Controller
{
    public function show(Surface $surface)
    {
        $surface->load([
            'states.comments.user'
        ]);
        
        $project = Project::relevant()->findOrFail(request('project_id'));

        if ($spot_id = request('spot_id')){
            $spot = Spot::findOrFail($spot_id);
        } else {
            $spot = $surface->tour->spots->first();
        }

        $surface_state = null;
        $create_new_state = request('new');

        $surface->background_url = $surface->getFirstMediaUrl('background');

        if (!$create_new_state){
            $surface_state = $surface->getCurrentState($project->id);
        }
        $assignedArtworks = $surface_state?->artworks;

        $surfaceData = $surface->only([
            'id', 'name', 'background_url', 'data'
        ]);

        $data = array();
        $data['project'] = $project;
        $data['surface'] = $surface;
        $data['surface_data'] = $surfaceData;
        $data['surface_current_state'] = $surface_state;
        $data['spot'] = $spot;
        $data['assigned_artworks'] = $assignedArtworks;
        $data['canvas_state'] = $surface_state ? $surface_state->canvas : [];

        return view('pages.editor', $data);
    }

    /**
     * @throws \Exception
     */
    public function update(Request $request, Surface $surface)
    {
        $request->validate([
            'project_id' => 'required'
        ]);


        $assigned_artworks = array();
        foreach (json_decode($request->assigned_artwork, true) as $artwork){
            $assigned_artworks[$artwork['artworkId']] = array(
                'top_position' => $artwork['topPosition'],
                'left_position' => $artwork['leftPosition'],
                'crop_data' => $artwork['cropData'],
                'override_scale' => $artwork['overrideScale'],
            );
        }

        if ($request->new){
            $state = $surface->createNewState($request->project_id);
            $state->update([
                'name' => $request->name
            ]);
        } else {
            $state = $surface->getCurrentState($request->project_id);
        }

        $state->update([
            'canvas' => json_decode($request->canvasState, true)
        ]);

        $state->addMediaFromBase64($request->thumbnail)
            ->usingFileName('thumbnail.png')
            ->toMediaCollection('thumbnail');
        $state->addMediaFromBase64($request->hotspot)
            ->usingFileName('hotspot.png')
            ->toMediaCollection('hotspot');

        $state->artworks()->sync($assigned_artworks);

        return redirect()->route('tours.show', [
            $surface->tour, 'project_id' => $request->project_id,
            'hlookat' => $request->hlookat,
            'vlookat' => $request->vlookat,
        ]);
    }

    public function destroy(SurfaceState $state)
    {
        $state->delete();
        return redirect()->back()->with('success', 'State removed');
    }
}
