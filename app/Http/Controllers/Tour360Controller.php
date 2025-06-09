<?php
namespace App\Http\Controllers;

use App\Models\CompanyTour;
use App\Helpers\ValidationRules;
use App\Models\Curate2dProject;
use App\Models\PhotoState;
use App\Models\ArtworkCollection;
use App\Models\Project;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Tour360Controller extends Controller
{
    public function index()
    {
        // Get all projects from the database
        $projects = Project::orderBy('created_at', 'desc')->get();

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
            $extraTours = Tour::withoutGlobalScope('forCurrentCompany')->whereIn('id', $extraTourIds)->get();

            // Merge and remove duplicates by 'id'
            $allTours = $tours->merge($extraTours)->unique('id')->values();

            $data = [
                'success' => true,
                'tours' => $allTours,
                'users' => User::forCurrentCompany()->get(),
                'artworkCollections' => ArtworkCollection::forCurrentCompany()->get()
            ];

            return response()->json($data);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to load project data: ' . $e->getMessage()
            ], 500);
        }
    }

    public function store(Request $request)
    {
        try {
            // Validate the request
            $request->validate([
                'name' => 'required|string|max:255',
                'tour_ids' => 'required|string',
                'user_ids' => 'required|string',
                'artwork_collection_ids' => 'required|string',
                'units' => 'required|string|in:imperial,metric',
                'thumbnail' => 'required|image|mimes:jpeg,png|max:2048'
            ]);

            // Decode JSON strings back to arrays
            $tourIds = json_decode($request->tour_ids);
            $userIds = json_decode($request->user_ids);
            $collectionIds = json_decode($request->artwork_collection_ids);

            // Create the project
            $project = Project::create($request->only([
                'name'
            ]));

            // Store thumbnail
            if ($request->hasFile('thumbnail')) {
                $path = $request->file('thumbnail')->store('project-thumbnails', 'public');
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
                'project' => $project
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to create project: ' . $e->getMessage()
            ], 500);
        }
    }

    public function update(Request $request, $id)
    {
   
    }
}
