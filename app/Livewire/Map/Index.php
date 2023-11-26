<?php

namespace App\Livewire\Map;

use App\Models\Map;
use App\Models\Tour;
use Livewire\Component;
use Spatie\MediaLibraryPro\Livewire\Concerns\WithMedia;

class Index extends Component
{
    use WithMedia;

    public Tour $tour;
    public Map $selectedMap;
    public $creatingNewMap = false;
    public $spots = array();
    public $mapImage;
    public $deleteOptions = array(
        'confirm_btn_attributes' => "wire:click.prevent=\"\$emit('delete')\""
    );

    public $mediaComponentNames = ['mapImage'];
    protected $listeners = ['delete'];

    protected $rules = [
        'selectedMap.tour_id' => 'required',
        'selectedMap.name' => 'required',
        'selectedMap.width' => 'required',
        'selectedMap.height' => 'required',
        'spots.*.x' => 'required_with:spots.*.y',
        'spots.*.y' => 'required_with:spots.*.x',
    ];

    public function mount(Tour $tour)
    {
        $this->tour = $tour;
        $this->selectMap($tour->map);
    }

    public function render()
    {
        return view('livewire.map.index');
    }

    public function selectMap(?Map $map)
    {
        if (!$map){
            $this->createNew();
            return;
        }

        $this->resetValidation();
        $this->creatingNewMap = false;
        $this->selectedMap = $map;
        $this->setMapSpots();
    }

    public function createNew()
    {
        $this->creatingNewMap = true;
        $this->selectedMap = new Map;
        $this->selectedMap->tour_id = $this->tour->id;
        $this->setMapSpots();
    }

    public function update()
    {
        $this->validate();
        $spots = \Arr::where($this->spots, function ($spot){
            return $spot['x'] && $spot['y'];
        });

        $this->selectedMap->save();
        $this->selectedMap->spots()->sync($spots);

        $this->selectedMap->addFromMediaLibraryRequest($this->mapImage)
            ->toMediaCollection('image');

        $this->tour->refresh();

        $this->clearMedia();

        $this->selectMap($this->selectedMap);
        $this->dispatch('flashNotification', message: 'Map updated');
    }

    public function delete()
    {
        $this->selectedMap->delete();
        $this->tour->refresh();
        $this->selectMap($this->tour->map);
        $this->dispatch('hideModal');
        $this->dispatch('flashNotification', message: 'Map deleted');
    }

    public function setMapSpots()
    {
        $spots = array();

        foreach ($this->tour->spots as $spot){
            $coordinates = $spot->maps()->find($this->selectedMap->id)?->pivot;
            $spots[$spot->id]['x'] = $coordinates?->x;
            $spots[$spot->id]['y'] = $coordinates?->y;
        }

        $this->spots = $spots;
    }
}
