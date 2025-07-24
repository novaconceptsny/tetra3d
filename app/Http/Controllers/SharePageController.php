<?php

namespace App\Http\Controllers;

use App\Models\Layout;
use App\Models\Project;
use App\Models\SharedLayout;
use Illuminate\Http\Request;

class SharePageController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            $projects = Project::all();
        } else {
            $projects = Project::where('company_id', $user->company->id)->get();
        }

        // Fetch all layouts and format for JavaScript
        $layouts = Layout::all()->map(function($layout) {
            $layout->thumbnail_url = $layout->assignedTour() ? $layout->assignedTour()->getFirstMediaUrl('thumbnail') : '';
            return $layout;
        });
        
        // Fetch all shared layouts with their related layouts
        $sharedLayouts = SharedLayout::with('layout')->get();

        return view('share.index', compact('sharedLayouts', 'layouts', 'projects'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'layout_id' => 'required|exists:layouts,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail_url' => 'nullable|string',
        ]);

        $sharedLayout = SharedLayout::create([
            'layout_id' => $request->layout_id,
            'title' => $request->title,
            'description' => $request->description,
            'thumbnail_url' => $request->thumbnail_url,
            'active' => true, // default value
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shared layout created successfully!',
            'data' => $sharedLayout->load('layout')
        ]);
    }

    public function toggle(Request $request, $id)
    {
        $sharedLayout = SharedLayout::findOrFail($id);
        $newStatus = !$sharedLayout->active; // Toggle the status
        $sharedLayout->update(['active' => $newStatus]);

        $action = $newStatus ? 'enabled' : 'disabled';

        return response()->json([
            'success' => true,
            'message' => "Shared layout {$action} successfully!"
        ]);
    }

    public function edit(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $sharedLayout = SharedLayout::findOrFail($id);
        $sharedLayout->update([
            'title' => $request->title,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shared layout updated successfully!',
            'data' => $sharedLayout->load('layout')
        ]);
    }
}
