<?php

namespace App\Livewire;

use App\Models\Map;
use App\Models\Tour;
use Livewire\Component;

class TourMap extends Component
{
    public $tour;
    public $spot_id;
    public $shared_tour_id;
    public $layoutId;
    public $selectedMap;

    public function mount(Tour $tour)
    {
        $this->spot_id = request('spot_id');
        $this->tour = $tour;
        $this->selectedMap = $tour->map;
    }

    public function dehydrate()
    {
        $this->js('setMapScale()');
    }

    public function render()
    {
        return view('livewire.tour-map');
    }

    public function selectMap(Map $map)
    {
        $this->selectedMap = $map;
        //$this->dispatch('mapChanged');
    }
}
