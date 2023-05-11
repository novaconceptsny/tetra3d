<?php

namespace App\Http\Livewire\Surface;

use App\Models\Surface;
use App\Models\SurfaceState;
use Livewire\Component;

class SurfaceRow extends Component
{
    public $projectId;
    public Surface $surface;

    protected $listeners = ['surfaceStateRemoved'];

    public function render()
    {
        $this->surface->load([
            'states' => fn($query) => $query->forProject($this->projectId)
        ]);

        return view('livewire.surface.surface-row');
    }

    public function changeActiveState(SurfaceState $state)
    {
        if($state->isActive()){
            return false;
        }

        $state->setAsActive();
        $state->addActivity('switched_state');
    }
}
