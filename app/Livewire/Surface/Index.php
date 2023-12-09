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

    public function removeSurfaceState(SurfaceState $state): void
    {
        $this->askForConfirmation(function () use ($state){
            $this->dispatch('hideModal');
            $state->delete();

            if ($state->isActive()){
                $stateToActive = SurfaceState::query()
                    ->where('surface_id', $state->surface_id)
                    ->where('layout_id', $state->layout_id)
                    ->first();
                $stateToActive?->setAsActive();
            }
            $this->dispatch('flashNotification', message: 'State deleted');
        });
    }

    public function changeActiveState(SurfaceState $state)
    {
        if($state->isActive()){
            return false;
        }

        $state->setAsActive();
    }
}
