<?php
namespace App\Http\Controllers;

use App\Models\ArtworkCollection;
use App\Models\CompanyTour;
use App\Models\PhotoState;
use App\Models\Project;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Tour360Controller extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            // Get all projects from the database
            $projects = Project::orderBy('created_at', 'desc')->get();
        } else {
            // Not super admin: get only the user's company
            $projects = Project::where('company_id', $user->company_id)->orderBy('created_at', 'desc')->get();
        }

        // Get favorite photo states
        $favorites = PhotoState::where('is_favorite', true)
            ->with('photo.project') // Assuming you have these relationships set up
            ->get();

        return view('tour360.index', compact('projects', 'favorites'));
    }

    public function create()
    {
        try {
            // Get all tours
            $tours = Tour::all();

            // Get extra tours from company_tour table
            $extraTourIds = CompanyTour::where('company_id', auth()->user()->company_id)->pluck('tour_id');
            $extraTours   = Tour::withoutGlobalScope('forCurrentCompany')->whereIn('id', $extraTourIds)->get();

            // Merge and remove duplicates by 'id'
            $allTours = $tours->merge($extraTours)->unique('id')->values();

            $data = [
                'success'            => true,
                'tours'              => $allTours,
                'users'              => User::forCurrentCompany()->get(),
                'artworkCollections' => ArtworkCollection::forCurrentCompany()->get(),
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load project data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function edit($id)
    {
        try {
            $project = Project::findOrFail($id);

            // Get all tours
            $tours = Tour::all();

            // Get extra tours from company_tour table
            $extraTourIds = CompanyTour::where('company_id', auth()->user()->company_id)->pluck('tour_id');
            $extraTours   = Tour::withoutGlobalScope('forCurrentCompany')->whereIn('id', $extraTourIds)->get();

            // Merge and remove duplicates by 'id'
            $allTours = $tours->merge($extraTours)->unique('id')->values();

            $data          = [];
            $data['tours'] = $allTours;
            // $data['users'] = User::forCompany($project->company_id)->get();
            // $data['artworkCollections'] = ArtworkCollection::forCompany($project->company_id)->get();
            $data['users']               = User::forCurrentCompany()->get();
            $data['artworkCollections']  = ArtworkCollection::forCurrentCompany()->get();
            $data['project']             = $project;
            $data['assignedCollections'] = $project->artworkCollections;
            $data['assignedUsers']       = $project->contributors;
            $data['assignedTours']       = $project->tours;

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load project data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'name'                   => 'required|string|max:255',
                'tour_ids'               => 'required|string',
                'user_ids'               => 'required|string',
                'artwork_collection_ids' => 'required|string',
                'units'                  => 'required|string|in:imperial,metric',
                'thumbnail'              => 'required|image|mimes:jpeg,png|max:2048',
            ]);

            // Decode JSON strings back to arrays
            $tourIds       = json_decode($request->tour_ids);
            $userIds       = json_decode($request->user_ids);
            $collectionIds = json_decode($request->artwork_collection_ids);

            // Create the project
            $project = Project::create($request->only([
                'name',
            ]));

            // Store thumbnail
            if ($request->hasFile('thumbnail')) {
                $path                    = $request->file('thumbnail')->store('project-thumbnails', 'public');
                $project->background_url = '/storage/' . $path;
                $project->save();
            }

            // Sync relationships
            $project->contributors()->sync($userIds);
            $project->tours()->sync($tourIds);
            $project->artworkCollections()->sync($collectionIds);

            return response()->json([
                'success' => true,
                'message' => 'Project created successfully',
                'project' => $project,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create project: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $project = Project::findOrFail($id);

            // Validate the request
            $request->validate([
                'name'                   => 'required|string|max:255',
                'tour_ids'               => 'required|string',
                'user_ids'               => 'required|string',
                'artwork_collection_ids' => 'required|string',
                'units'                  => 'required|string|in:imperial,metric',
                'thumbnail'              => 'required|image|mimes:jpeg,png|max:2048',
            ]);

            // Update basic project information
            $project->name = $request->name;
            // $project->units = $request->units;

            // Decode JSON strings back to arrays
            $tourIds       = json_decode($request->tour_ids);
            $userIds       = json_decode($request->user_ids);
            $collectionIds = json_decode($request->artwork_collection_ids);

            // Handle thumbnail update if provided
            if ($request->hasFile('thumbnail')) {
                // Delete old thumbnail if it exists
                if ($project->background_url) {
                    Storage::delete($project->background_url);
                }

                // Store new thumbnail
                $path                    = $request->file('thumbnail')->store('project-thumbnails', 'public');
                $project->background_url = '/storage/' . $path;
            }

            $project->save();

            // Update relationships
            if ($request->has('tour_ids')) {
                $project->tours()->sync($tourIds);
            }

            if ($request->has('artwork_collection_ids')) {
                $project->artworkCollections()->sync($collectionIds);
            }

            if ($request->has('user_ids')) {
                $project->contributors()->sync($userIds);
            }

            return response()->json([
                'success' => true,
                'message' => 'Project updated successfully',
                'project' => $project,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update project: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function destroy($id)
    {
        try {
            $project = Project::findOrFail($id);

            // Delete the project's thumbnail if it exists
            if ($project->background_url) {
                Storage::delete($project->background_url);
            }

            // Delete the project
            $project->delete();

            return response()->json([
                'success' => true,
                'message' => 'Project deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete project: ' . $e->getMessage(),
            ], 500);
        }
    }
}
