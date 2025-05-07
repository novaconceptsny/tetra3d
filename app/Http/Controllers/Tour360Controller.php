<?php

namespace App\Http\Controllers;

use App\Models\Curate2dProject;
use App\Models\PhotoState;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Tour360Controller extends Controller
{
    public function index()
    {
        // Get all projects from the database
        $projects = Curate2dProject::orderBy('created_at', 'desc')->get();
        
        // Get favorite photo states
        $favorites = PhotoState::where('is_favorite', true)
            ->with('photo.project') // Assuming you have these relationships set up
            ->get();

        return view('tour360.index', compact('projects', 'favorites'));
    }

    public function store(Request $request)
    {
        // Validate the request
        $request->validate([
            'title' => 'required|string|max:255',
            'image' => 'required|image|mimes:jpeg,png|max:2048'
        ]);

        // Handle file upload
        $imagePath = $request->file('image')->store('project-images', 'public');
        
        // Create new project
        $project = Curate2dProject::create([
            'name' => $request->title,
            'background_url' => '/storage/' . $imagePath,
        ]);

        return response()->json([
            'success' => true,
            'project' => $project
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $project = Curate2dProject::findOrFail($id);
            
            $request->validate([
                'title' => 'required|string|max:255',
                'image' => 'nullable|image|mimes:jpeg,png|max:2048',
            ]);

            $project->name = $request->title;

            // Handle image upload if a new image is provided
            if ($request->hasFile('image')) {
                // Delete old image if it exists
                if ($project->background_url) {
                    Storage::disk('public')->delete($project->background_url);
                }

                // Store new image
                $path = $request->file('image')->store('project-images', 'public');
                $project->background_url = '/storage/' . $path;
            }

            $project->save();

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully'
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update project: ' . $e->getMessage()
            ], 500);
        }
    }
} 