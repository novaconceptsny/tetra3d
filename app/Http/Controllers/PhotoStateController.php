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
            
            // Log each variable separately with labels
            error_log("Layout: " . json_encode($layout, JSON_PRETTY_PRINT));
            error_log("Project: " . json_encode($project, JSON_PRETTY_PRINT));
            error_log("Photo: " . json_encode($photo, JSON_PRETTY_PRINT));

            $data            = [];
            $data['project'] = $project;
            $data['layout']  = $layout;
            $data['photo']   = $photo;

            return view('pages.photoeditor', $data);
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error in PhotoStateController@show: ' . $e->getMessage());
            
            // Redirect back with error message
            return redirect()->back()->with('error', 'Unable to load photo editor: ' . $e->getMessage());
        }
    }

}
