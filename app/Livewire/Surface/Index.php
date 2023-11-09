<?php

namespace App\Livewire\Surface;

use App\Models\Project;
use App\Models\SurfaceState;
use App\Models\Tour;
use Livewire\Component;

class Index extends Component
{
    public Tour $tour;
    public $surfaces;
    public Project $project;

    protected $listeners = ['removeSurfaceState'];

    public function mount()
    {
        $this->surfaces = $this->tour->surfaces()->with([
            'states.user',
            'states.media',
            'states.likes',
            'states' => fn($query) => $query->forProject($this->project->id),
            'media',
        ])->get();
    }

    public function render()
    {
        return view('livewire.surface.index');
    }

    public function removeSurfaceState(SurfaceState $state)
    {
        $this->dispatch('hideModal');
        $state->delete();
        $this->dispatch('flashNotification', message: 'State deleted');
    }
}
