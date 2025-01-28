<?php

namespace App\Livewire;

use App\Models\Layout;
use App\Models\Project;
use App\Models\Tour;
use WireElements\Pro\Components\SlideOver\SlideOver;
use WireElements\Pro\Concerns\InteractsWithConfirmationModal;
use App\Models\Sculpture;
use App\Models\SurfaceState;

class TourSwitcher extends SlideOver
{
    use InteractsWithConfirmationModal;

    public Project|int $project;
    public Tour $selectedTour;
    public int $selectedTourId;

    protected $listeners = [
        'refresh' => '$refresh',
        'duplicateLayout' => 'duplicateLayout'
    ];

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->selectedTour = $project->tours->first();
        $this->selectedTourId = $this->selectedTour?->id;
    }

    public function render()
    {
        return view('livewire.tour-switcher');
    }

    public function selectTour()
    {
        $this->selectedTour = $this->project->tours->where('id', $this->selectedTourId)->first();
    }

    public static function attributes(): array
    {
        return [
            'size' => '4xl'
        ];
    }
}
