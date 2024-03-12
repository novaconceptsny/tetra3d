<?php

namespace App\Livewire\Surface;

use App\Models\Surface;
use App\Models\SurfaceState;
use Livewire\Component;

class SurfaceRow extends Component
{
    //todo::remove this component as we are not using it anymore
    public $layoutId;
    public Surface $surface;

    protected $listeners = ['surfaceStateRemoved'];

    public function render()
    {
        $this->surface->load([
            'states.media',
            'states' => fn($query) => $query->forLayout($this->layoutId)
        ]);

        return view('livewire.surface.surface-row');
    }

    public function changeActiveState(SurfaceState $state)
    {
        if($state->isActive()){
            return false;
        }

        $state->setAsActive();
    }
}
