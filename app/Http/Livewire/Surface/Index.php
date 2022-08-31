<?php

namespace App\Http\Livewire\Surface;

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
        $this->surfaces = $this->tour->surfaces;
    }

    public function render()
    {
        return view('livewire.surface.index');
    }

    public function removeSurfaceState(SurfaceState $state)
    {
        $state->delete();
        $this->emit('surfaceStateRemoved');
        $this->emit('hideModal');
        $this->emit('flashNotification', 'State deleted');
    }
}
