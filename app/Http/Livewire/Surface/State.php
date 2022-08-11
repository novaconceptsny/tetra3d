<?php

namespace App\Http\Livewire\Surface;

use App\Models\Surface;
use App\Models\SurfaceState;
use Livewire\Component;

class State extends Component
{
    public SurfaceState $state;
    public Surface $surface;
    public $projectId;

    public function mount()
    {

    }

    public function render()
    {
        return view('livewire.surface.state');
    }
}
