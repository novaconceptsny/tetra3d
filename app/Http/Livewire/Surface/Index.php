<?php

namespace App\Http\Livewire\Surface;

use App\Models\Project;
use App\Models\Tour;
use Livewire\Component;

class Index extends Component
{
    public Tour $tour;
    public $surfaces;
    public Project $project;

    public function mount()
    {
        $this->surfaces = $this->tour->surfaces;
    }

    public function render()
    {
        return view('livewire.surface.index');
    }
}
