<?php

namespace App\Livewire\Datatables;

use App\Models\Activity;
use App\Models\Project;
use App\Models\Tour;
use Illuminate\Pagination\LengthAwarePaginator;

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
        $this->dispatch('contentChanged');
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
            ->forCurrentCompany()
            ->sort($this->sortBy, $this->sortOrder)
            ->paginate($this->perPage);

        $rows->getCollection()->transform(function ($row){
            $row->project_name = $row->project?->name;
            $row->layout_name = $row->layout?->name ?? 'Layout Deleted';
            $row->user_name = $row->user?->name;
            $row->date = $row->created_at->format('d M Y');

            if (!$row->layout){
                $row->url = null;
            }

            return $row;
        });

        $search = strtolower($this->search);

        $filtered = $rows->getCollection()->filter(function ($row) use ($search) {
            $activity = strtolower($row->activity);
            $project_name = strtolower($row->project?->name);
            $user_name = strtolower($row->user?->name);
            $layout_name = strtolower($row->layout?->name ?? 'Layout Deleted');
            $date = strtolower($row->created_at->format('d M Y'));
            return str_contains($layout_name, $search) || str_contains($project_name, $search) || str_contains($user_name, $search) || str_contains($activity, $search) || str_contains($date, $search);
        });

            // Get unique tours from the filtered rows
        $uniqueTours = $filtered->pluck('tour')->filter()->unique('id')->values();

        $currentPage = $rows->currentPage();
        $perPage = $rows->perPage();
        $total = $filtered->count();
        $paginator = new LengthAwarePaginator(
            $filtered->forPage($currentPage, $perPage),
            $total,
            $perPage,
            $currentPage,
            ['path' => request()->url()]
        );


        $data['rows'] = $paginator;
        $data['label'] = 'activity';
        $data['uniqueTours'] = $uniqueTours; // Pass unique tours to the view
        
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
                'name' => 'Exhibition',
                'visible' => true,
            ],
            'layout_name' => [
                'name' => 'Layout',
                'visible' => true,
                'th-classes' => 'w-10'
            ],
            'user_name' => [
                'name' => 'User',
                'visible' => true,
                'sortable' => true,
                'th-classes' => 'w-10'
            ],
            'activity' => [
                'name' => 'Activity',
                'visible' => true,
                'sortable' => true,
                'th-classes' => 'w-40'
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
