<?php

namespace App\Http\Livewire\Datatables;

use App\Models\Activity;
use App\Models\Project;
use App\Models\Tour;

class ActivityDatatable extends BaseDatatable
{
    public $model = Activity::class;
    public $selectedProject = '';
    public $selectedTour = '';
    public $projectId = null;
    public $frontend = false;

    public function mount()
    {
        $this->columns = $this->getColumns();
        $this->routes = $this->getRoutes();
        $this->projectId = request('project_id');
        $this->selectedCollection = request('collection_id');
        if ($this->projectId){
            $this->selectedCollection = \DB::table('artwork_collection_project')
                ->where('project_id', $this->projectId)
                ->value('artwork_collection_id');
        }
    }

    public function dehydrate(){
        $this->dispatchBrowserEvent('contentChanged');
    }

    public function render()
    {
        $data = array();

        $data['heading'] = __('Activities');

        $data['projects'] = Project::latest('name')->get();
        $data['tours'] = Tour::latest('name')->get();

        $rows = $this->model::query()
            ->with('project', 'tour', 'user')
            ->when(
                $this->selectedProject,
                fn($query) => $query->where(
                    'project_id', $this->selectedProject
                )
            )
            ->when(
                $this->selectedTour,
                fn($query) => $query->where(
                    'tour_id', $this->selectedTour
                )
            )
            ->whereAnyColumnLike($this->search)
            ->sort($this->sortBy, $this->sortOrder)
            ->paginate($this->perPage);

        $rows->getCollection()->transform(function ($row){
            $row->project_name = $row->project?->name;
            $row->tour_name = $row->tour?->name;
            $row->user_name = $row->user?->name;
            $row->date = $row->created_at->format('d M Y');
            return $row;
        });

        $data['rows'] = $rows;
        $data['label'] = 'activity';

        return view("livewire.datatables.activity", $data);
    }

    public function resetFilters()
    {
        $this->reset([
            'selectedProject', 'selectedTour',
            'search', 'perPage', 'sortBy', 'sortOrder'
        ]);

    }

    public function getColumns()
    {
        $columns = [
            'project_name' => [
                'name' => 'Project',
                'visible' => true,
            ],
            'tour_name' => [
                'name' => 'Tour',
                'visible' => true,
                'th-classes' => 'w-10'
            ],
            'activity' => [
                'name' => 'Activity',
                'visible' => true,
                'sortable' => true,
                'th-classes' => 'w-40'
            ],
            'user_name' => [
                'name' => 'User',
                'visible' => true,
                'sortable' => true,
                'th-classes' => 'w-10'
            ],
            'date' => [
                'name' => 'Date',
                'visible' => true,
                'sortable' => true,
            ],
        ];

        return $columns;
    }

    public function getRoutes()
    {
        $routes =  [
            'create' => 'backend.artworks.create',
            'edit' => 'backend.artworks.edit',
            'delete' => 'backend.artworks.destroy',
        ];

        if (user()->cannot('create', Activity::class)) {
            unset($routes['create']);
        }

        return $routes;
    }
}
