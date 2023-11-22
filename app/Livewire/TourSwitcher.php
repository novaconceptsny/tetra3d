<?php

namespace App\Livewire;

use App\Models\Project;
use App\Models\Tour;
use WireElements\Pro\Components\SlideOver\SlideOver;

class TourSwitcher extends SlideOver
{
    public Project|int $project;
    public Tour $selectedTour;
    public int $selectedTourId;

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
}
