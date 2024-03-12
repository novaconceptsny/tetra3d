<?php

namespace App\Livewire\Model;

use App\Models\Map;
use App\Models\Tour;
use App\Models\SpotsPosition;
use App\Models\TourModel;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibraryPro\Livewire\Concerns\WithMedia;
use WireElements\Pro\Concerns\InteractsWithConfirmationModal;

class Index extends Component
{
    use WithMedia;
    use WithFileUploads;
    use InteractsWithConfirmationModal;

    public Tour $tour;
    public Map $selectedMap;
    public $tourModel = null;
    public $tourModelPath = null;
    public $creatingNewMap = false;
    public $spots = array();
    public $spotsPosition = array();
    public $mapImage = [];
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
        $this->setSpots();
        $this->setModel();

        error_log(json_encode($this->spotsPosition));
    }

    public function render()
    {
        error_log(json_encode($this->spotsPosition));

        return view('livewire.model.index');
    }
    public function setModel() {
        $models = TourModel::where('tour_id', $this->tour->id)->get();
        if ($models->isEmpty()) {
            $data = array('tour_id'=>$this->tour->id, 'name'=>'null');
            TourModel::create($data);
            $models = TourModel::where('tour_id', $this->tour->id)->get();
        } 

        foreach($models as $model) {
            $this->tourModel = $model->name;
        }

        $this->tourModelPath = Storage::url('3dmodel/'.$this->tourModel);
        // $this->tourModelPath = Storage::disk('local')->;
    }
    public function setSpots() {
        $temp_spots = SpotsPosition::where('tour_id', $this->tour->id)->get();

        if ($temp_spots->isEmpty()) {
            foreach($this->tour->spots as $spot) {
                $data = array("tour_id"=>$this->tour->id, "spot_id"=>$spot->id, "x"=>0.0, "y"=>0.0, "z"=>0.0);
                SpotsPosition::create($data);
            }
            $temp_spots = SpotsPosition::where('tour_id', $this->tour->id)->get();
        }
        $spotsPosition = array();

        foreach ($temp_spots as $spot){
            $spotsPosition[$spot->spot_id]['x'] = $spot?->x;
            $spotsPosition[$spot->spot_id]['y'] = $spot?->y;
            $spotsPosition[$spot->spot_id]['z'] = $spot?->z;
        }

        $this->spotsPosition = $spotsPosition;
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
        $this->mapImage = [];
        $this->setMapSpots();
    }

    public function update()
    {
        // $this->validate();
        // $spots = \Arr::where($this->spots, function ($spot){
        //     return $spot['x'] && $spot['y'];
        // });

        // $this->selectedMap->save();
        // $this->selectedMap->spots()->sync($spots);

        // $this->selectedMap->addFromMediaLibraryRequest($this->mapImage)
        //     ->toMediaCollection('image');

        // $this->tour->refresh();

        // $this->selectMap($this->selectedMap);
        $this->dispatch('flashNotification', message: 'Model updated');
        $temp_spots = SpotsPosition::where('tour_id', $this->tour->id)->get();
        if ($temp_spots->isEmpty()) {

        } else {
            foreach($temp_spots as $spot) {
                $spot->x = $this->spotsPosition[$spot->spot_id]['x'];
                $spot->y = $this->spotsPosition[$spot->spot_id]['y'];
                $spot->z = $this->spotsPosition[$spot->spot_id]['z'];
                $spot->save();
            }
        }

        if (gettype($this->tourModel) !== 'string') {
            $name = $this->tourModel->getClientOriginalName();
            $name = $this->tour->id.'_'.$name;

            $this->tourModel->storeAs(path: 'public/3dmodel', name: $name);

            $models = TourModel::where('tour_id', $this->tour->id)->get();
            // echo $models;
            foreach($models as $model) {
                $model->name = $name;
                $model->save();
            }
        }
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
