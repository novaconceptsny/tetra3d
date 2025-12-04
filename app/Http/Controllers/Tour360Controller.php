<?php
namespace App\Http\Controllers;

use App\Helpers\ProjectImageHelper;
use App\Models\ArtworkCollection;
use App\Models\CompanyTour;
use App\Models\PhotoState;
use App\Models\Project;
use App\Models\Company;
use App\Models\Tour;
use App\Models\Layout;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class Tour360Controller extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $data = [];

        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            // For super admin, get all companies with their projects
            $companies = Company::with(['projects' => function($query) {
                $query->orderBy('created_at', 'desc')
                      ->withCount(['tours', 'artworkCollections', 'contributors', 'layouts']);
            }])->get();

            $favorites = Layout::where('is_favorite', true)
                ->with(['project', 'tour'])
                ->get();
                
            // Get all layouts for super admin
            $allLayouts = Layout::with(['project', 'user', 'tour'])
                ->get();
                
            // Get all tours for super admin
            $allTours = Tour::with(['company'])
                ->get();
        } else {
            // For regular users, get only their company with its projects
            $companies = Company::where('id', $user->company_id)
                ->with(['projects' => function($query) {
                    $query->orderBy('created_at', 'desc')
                          ->withCount(['tours', 'artworkCollections', 'contributors', 'layouts']);
                }])
                ->get();
            $company = Company::findOrFail($user->company_id);

            // Get all user IDs in this company
            $userIds = User::where('company_id', $company->id)->pluck('id');

            // Get favorite layouts for those users
            $favorites = Layout::where('is_favorite', true)
                ->whereIn('user_id', $userIds)
                ->with(['project', 'tour'])
                ->get();
                
            // Get all layouts for users in this company
            $allLayouts = Layout::whereIn('user_id', $userIds)
                ->with(['project', 'user', 'tour'])
                ->get();
                
            // Get all tours for this company
            $allTours = Tour::where('company_id', $company->id)
                ->with(['company'])
                ->get();
        }


        return view('tour360.index', compact('companies', 'favorites', 'allLayouts', 'allTours'));
    }

    public function create($companyId)
    {
        try {
            // Get all tours
            $company = Company::findOrFail($companyId);
            $tours = Tour::where('company_id', $companyId)->get();

            // Get extra tours from company_tour table
            $extraTourIds = CompanyTour::where('company_id', $companyId)->pluck('tour_id');
            $extraTours   = Tour::withoutGlobalScope('forCurrentCompany')->whereIn('id', $extraTourIds)->get();

            // Merge and remove duplicates by 'id'
            $allTours = $tours->merge($extraTours)->unique('id')->values();

            $data = [
                'success'            => true,
                'tours'              => $allTours,
                'users'              => User::where('company_id', $companyId)->get(),
                'artworkCollections' => ArtworkCollection::where('company_id', $companyId)->get(),
                'company'            => $company,
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
            $project = Project::with('company')->findOrFail($id);

            // Get all tours
            $tours = Tour::where('company_id', $project->company_id)->get();

            // Get extra tours from company_tour table
            $extraTourIds = CompanyTour::where('company_id', $project->company_id)->pluck('tour_id');
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
            $data['company']             = $project->company;
            $data['assignedCollections'] = $project->artworkCollections;
            $data['assignedUsers']       = $project->contributors;
            $data['assignedTours']       = $project->assignedTours();

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
                'unit'                   => 'required|string|in:imperial,metric',
                'company_id'             => 'required|exists:companies,id',
            ]);

            // Add conditional validation for thumbnail
            if ($request->hasFile('thumbnail')) {
                $request->validate([
                    'thumbnail' => 'image|mimes:jpeg,png',
                ]);
            }

            // Decode JSON strings back to arrays
            $tourIds       = json_decode($request->tour_ids);
            $userIds       = json_decode($request->user_ids);
            $collectionIds = json_decode($request->artwork_collection_ids);

            // Create the project
            $project = Project::create([
                'name' => $request->name,
                'company_id' => $request->company_id,
                'unit' => $request->unit,
            ]);

            // Store thumbnail
            if ($request->hasFile('thumbnail')) {
                $path                    = $request->file('thumbnail')->store('project-thumbnails', 'public');
                $project->background_url = '/storage/' . $path;
                $project->save();
            } else {
                // Assign a random default image if no thumbnail is provided
                $project->background_url = ProjectImageHelper::getRandomDefaultImage();
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
                'unit'                   => 'required|string|in:imperial,metric',
            ]);

            // Add conditional validation for thumbnail
            if ($request->hasFile('thumbnail')) {
                $request->validate([
                    'thumbnail' => 'image|mimes:jpeg,png',
                ]);
            }

            // Update basic project information
            $project->name = $request->name;
            $project->unit = $request->unit;

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

    public function toggleFavorite($id)
    {
        try {
            $layout = Layout::findOrFail($id);
            
            // Use direct database update to avoid updating timestamps
            $newFavoriteStatus = !$layout->is_favorite;
            \DB::table('layouts')
                ->where('id', $id)
                ->update(['is_favorite' => $newFavoriteStatus]);

            return response()->json([
                'success' => true,
                'message' => $newFavoriteStatus ? 'Added to favorites' : 'Removed from favorites',
                'is_favorite' => $newFavoriteStatus,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to toggle favorite: ' . $e->getMessage(),
            ], 500);
        }
    }
}
