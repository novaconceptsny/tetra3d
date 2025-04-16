<?php
namespace App\Http\Controllers;

use App\Models\Layout;
use App\Models\Photo;
use App\Models\Project;
use Illuminate\Http\Request;

class PhotoStateController extends Controller
{
    public function show(Photo $photo)
    {
        try {
            $layoutId = request('layout_id');
            if (!$layoutId) {
                throw new \Exception('Layout ID is required');
            }

            $layout = Layout::findOrFail($layoutId);
            $project = Project::findOrFail($layout->project_id);
            
            $data            = [];
            $data['project'] = $project;
            $data['layout']  = $layout;
            $data['surface']   = $photo;
            $data['navEnabled'] = false;
            $data['navbarLight'] = true;

            $canvases = array();
            $assignedArtworks = array();

            $canvases[$photo->id ?? 'new'] = [
                'canvasId' => "artwork_canvas_" . ($photo->id ?? 'new'),
                'surface' => $photo->only([
                    'id',
                    'name',
                    'background_url',
                    'data'
                ]),
                'assignedArtworks' => $assignedArtworks,
                'userId' => auth()->id(),
                'photoId' => $photo->id,
                'layoutId' => $layout->id,
                'photoEditable' => true
            ];

            $data['canvases'] = $canvases;
            return view('pages.photoeditor', $data);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error in PhotoStateController@show: ' . $e->getMessage());
            
            // Redirect back with error message
            return redirect()->back()->with('error', 'Unable to load photo editor: ' . $e->getMessage());
        }
    }

    public function update(Request $request, Photo $photo)
    {
        dd($request->all());
    }

}
