<?php

namespace App\Livewire\Surface;

use App\Models\Layout;
use App\Models\Project;
use App\Models\SurfaceState;
use App\Models\Tour;
use Livewire\Component;
use WireElements\Pro\Concerns\InteractsWithConfirmationModal;

class Index extends Component
{
    use InteractsWithConfirmationModal;

    public Tour $tour;
    public $surfaces;
    public Layout $layout;

    protected $listeners = [
        'removeSurfaceState',
        'refresh' => '$refresh'
    ];

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

    public function removeSurfaceState(SurfaceState $state): void
    {
        $this->askForConfirmation(function () use ($state) {
            $state->remove();
            $this->js("location.reload();");
            $this->dispatch('flashNotification', message: 'State deleted');
        });
    }

    public function changeActiveState(SurfaceState $state): void
    {
        $state->setAsActive();
    }
}
