<?php

namespace App\Http\Livewire;

use App\Models\Project;
use Livewire\Component;

class ProjectsList extends Component
{
    public ?Project $selectedProject = null;

    public function mount()
    {

    }

    public function render()
    {
        $data = array();

        $data['projects'] = Project::with([
            'tours', 'contributors.media'
        ])->relevant()->get();

        return view('livewire.project-list', $data);
    }

    public function selectProject(Project $project)
    {
        $this->selectedProject = $project;
    }
}
