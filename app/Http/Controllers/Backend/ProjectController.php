<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ValidationRules;
use App\Http\Controllers\Controller;
use App\Models\ArtworkCollection;
use App\Models\Project;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\CompanyTour;
use App\Models\Layout;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Project::class, 'project');
    }

    public function index()
    {
        $projects = Project::relevant()->with([
            'company', 'tours'
        ])->withCount('contributors')->paginate(25);
        return view('backend.project.index', compact('projects'));
    }

    public function create()
    {
        $data = array();
        $data['route'] = route('backend.projects.store');

        // Get all tours
        $tours = Tour::all();

        // Get extra tours from company_tour table (assuming a CompanyTour model exists)
        $extraTourIds = CompanyTour::where('company_id', user()->company_id)->pluck('tour_id'); 
        $extraTours = Tour::withoutGlobalScope('forCurrentCompany')->whereIn('id', $extraTourIds)->get();

        // Merge and remove duplicates by 'id'
        $allTours = $tours->merge($extraTours)->unique('id')->values();

     //   dd($allTours);

        $data['tours'] = $allTours;
        $data['users'] = User::forCurrentCompany()->get();
        $data['artworkCollections'] = ArtworkCollection::forCurrentCompany()->get();

        return view('backend.project.form', $data);
    }

    public function store(Request $request)
    {
        $request->validate(ValidationRules::storeProject());

        $project = Project::create($request->only([
            'name'
        ]));

        $project->contributors()->sync($request->user_ids);
        $project->tours()->sync($request->tour_ids);
        $project->artworkCollections()->sync($request->artwork_collection_ids);

        $project->addFromMediaLibraryRequest($request->thumbnail)
            ->toMediaCollection('thumbnail');

        return redirect()->route('backend.projects.index')
            ->with('success', 'Project created successfully');
    }

    public function show(Project $project)
    {
        return view('backend.project.show', compact('project'));
    }

    public function edit(Project $project)
    {
        $data = array();
        $data['route'] = route('backend.projects.update', $project);

        $data['tours'] = $project->allTours();
        $data['users'] = User::forCompany($project->company_id)->get();
        $data['artworkCollections'] = ArtworkCollection::forCompany($project->company_id)->get();
        $data['project'] = $project;
        $data['method'] = 'put';

        return view('backend.project.form', $data);
    }

    public function update(Request $request, Project $project)
    {
        $request->validate(ValidationRules::updateProject());

        if ($project->name !== $request->name){
            $project->addActivity('name_updated', ['old_name' => $project->name, 'new_name' => $request->name]);
        }

        $project->update($request->only([
            'name'
        ]));

        $syncedTours = $project->tours()->sync($request->tour_ids);
        $syncedUsers = $project->contributors()->sync($request->user_ids);
        $syncedCollections = $project->artworkCollections()->sync($request->artwork_collection_ids);

        
        if (!empty($syncedTours['detached'])) {
            
            // Remove layouts for detached tours
            Layout::where('project_id', $project->id)
                  ->whereIn('tour_id', $syncedTours['detached'])
                  ->delete();
        }


        if (!empty($syncedTours['attached']) || !empty($syncedTours['detached'])) {
            $project->addActivity('tours_updated');
        }

        if (!empty($syncedUsers['attached']) || !empty($syncedUsers['detached'])){
            $project->addActivity('users_updated');
        }

        if (!empty($syncedCollections['attached']) || !empty($syncedCollections['detached'])){
            $project->addActivity('collections_updated');
        }

        $project->addFromMediaLibraryRequest($request->thumbnail)
            ->toMediaCollection('thumbnail');

        return redirect()->route('backend.projects.index')
            ->with('success', 'Project updated successfully');

    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->back()->with('success', 'Project deleted successfully');
    }
}
