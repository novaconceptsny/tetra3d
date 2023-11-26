<?php

namespace App\Livewire\Surface;

use App\Models\Layout;
use App\Models\Project;
use App\Models\SurfaceState;
use App\Models\Tour;
use Livewire\Component;

class Index extends Component
{
    public Tour $tour;
    public $surfaces;
    public Layout $layout;

    protected $listeners = ['removeSurfaceState'];

    public function mount()
    {

    }

    public function render()
    {
        $this->surfaces = $this->tour->surfaces()
            ->with([
                'states' => fn($query) => $query->forLayout($this->layout->id)->with(['user', 'media', 'likes']),
                'media',
            ])->get();

        return view('livewire.surface.index');
    }

    public function removeSurfaceState(SurfaceState $state)
    {
        $this->dispatch('hideModal');
        $state->delete();
        $this->dispatch('flashNotification', message: 'State deleted');
    }

    public function changeActiveState(SurfaceState $state)
    {
        if($state->isActive()){
            return false;
        }

        $state->setAsActive();
    }
}
