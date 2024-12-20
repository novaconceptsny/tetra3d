<?php

namespace App\Livewire;

use App\Models\Map;
use App\Models\Tour;
use App\Models\Spot;
use Livewire\Component;
use App\Models\TourModel;

class TourMap extends Component
{
    public $tour;
    public $spot_id;
    public $shared_tour_id;
    public $layoutId;
    public $selectedMap;
    public $tourModel;

    public function mount(Tour $tour, Spot $spot)
    {
        $this->spot_id = $spot->id;
        $this->tour = $tour;
        $this->selectedMap = $tour->map;

        $tourModel = $tour ? TourModel::where('tour_id', $tour->id)->get() : null;
        if ($tourModel !== null && !$tourModel->isEmpty()) {
            $this->tourModel  = $tourModel[0];
        } else {
            $this->tourModel  = null;
        }
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
