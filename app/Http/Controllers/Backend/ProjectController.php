<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ValidationRules;
use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Project;
use App\Models\Tour;
use App\Models\User;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Project::class, 'project');
    }

    public function index()
    {
        $projects = Project::relevant()->paginate(25);
        return view('backend.project.index', compact('projects'));
    }

    public function create()
    {
        $data = array();
        $data['route'] = route('backend.projects.store');
        $data['tours'] = Tour::all();
        $data['users'] = User::all();

        return view('backend.project.form', $data);
    }

    public function store(Request $request)
    {
        $request->validate(ValidationRules::storeProject());

        Project::create($request->all());

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
        $data['tours'] = Tour::all();
        $data['users'] = User::all();
        $data['project'] = $project;
        $data['method'] = 'put';

        return view('backend.project.form', $data);
    }

    public function update(Request $request, Project $project)
    {
        $request->validate(ValidationRules::updateProject());

        $project->update($request->all());

        return redirect()->route('backend.projects.index')
            ->with('success', 'Project updated successfully');

    }

    public function destroy(Project $project)
    {
        $project->delete();

        return redirect()->back()->with('success', 'Project deleted successfully');
    }
}
