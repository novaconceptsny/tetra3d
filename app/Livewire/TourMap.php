<?php

namespace App\Livewire;

use App\Models\Map;
use App\Models\Tour;
use Livewire\Component;
use WireElements\Pro\Components\Modal\Modal;

class TourMap extends Modal
{
    public int|Tour $tour;
    public $shared_tour_id;
    public $project;
    public $selectedMap;

    public function mount(Tour $tour)
    {
        $this->tour = $tour;
        $this->selectedMap = $tour->map;
    }

    public function render()
    {
        return view('livewire.tour-map');
    }

    public function selectMap(Map $map)
    {
        $this->selectedMap = $map;
        $this->dispatch('mapChanged');
    }

    public static function attributes(): array
    {
        return [
            'size' => '7xl'
        ];
    }
}
