<?php

namespace App\Http\Livewire\Surface;

use App\Models\Project;
use App\Models\Surface;
use App\Models\SurfaceState;
use Livewire\Component;

class SurfaceRow extends Component
{
    public $projectId;
    public Surface $surface;

    protected $listeners = [
        'changeActiveState'
    ];


    public function mount()
    {

    }

    public function render()
    {
        return view('livewire.surface.surface-row');
    }

    public function changeActiveState(SurfaceState $state)
    {
        $state->setAsActive();
        $this->surface->refresh();
    }
}
