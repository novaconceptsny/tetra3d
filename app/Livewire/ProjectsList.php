<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Tour;
use Livewire\Component;

class ProjectsList extends Component
{
    public ?Project $selectedProject = null;
    public ?Tour $selectedTour = null;
    public $selectedTourId;
    public $sortBy = 'name';

    public function mount()
    {

    }

    public function render()
    {
        $data = array();

        $data['projects'] = Project::with(['tours', 'contributors.media',])
            ->withCount(['tours', 'artworkCollections'])
            ->when($this->sortBy == 'created_at', fn($query) => $query->latest('created_at'))
            ->when($this->sortBy == 'updated_at', fn($query) => $query->latest('updated_at'))
            ->when($this->sortBy == 'name', fn($query) => $query->oldest('name'))
            ->relevant()->get();

        return view('livewire.project-list', $data);
    }

    public function selectProject(Project $project)
    {
        $this->selectedProject = $project;
        $this->selectedTour = $project->tours->first();
        $this->selectedTourId = $this->selectedTour?->id;
    }

    public function selectTour()
    {
        $this->selectedTour = $this->selectedProject->tours->where('id', $this->selectedTourId)->first();
    }
}
