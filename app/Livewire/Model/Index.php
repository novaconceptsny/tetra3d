<?php

namespace App\Livewire\Model;

use App\Models\Tour;
use App\Models\SpotsPosition;
use App\Models\TourModel;
use Livewire\Component;
use Livewire\WithFileUploads;
use Livewire\Attributes\Renderless;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibraryPro\Livewire\Concerns\WithMedia;
use WireElements\Pro\Concerns\InteractsWithConfirmationModal;

class Index extends Component
{
    use WithMedia;
    use WithFileUploads;
    use InteractsWithConfirmationModal;

    public Tour $tour;
    
    #[Renderless]
    public $tourModel = null;
    public $surfaceModel = null;
    public $surfaceModelPath = null;
    public $tourModelPath = null;
    public $spots = array();
    public $spotsPosition = array();
    public $mapImage = [];
    protected $listeners = ['delete'];
    protected $rules = [
        'spots.*.x' => 'required_with:spots.*.y',
        'spots.*.y' => 'required_with:spots.*.x',
    ];
    public function mount(Tour $tour)
    {
        $this->tour = $tour;
        $this->setSpots();
        $this->setModel();
    }
    public function render()
    {
        return view('livewire.model.index');
    }
    public function setModel() {
        $models = TourModel::where('tour_id', $this->tour->id)->get();
        if ($models->isEmpty()) {
            $this->tourModel = 'Empty';
            $this->surfaceModel = 'Empty';
        } else {
            $this->tourModel = $models[0]->name;
            $this->surfaceModel = $models[0]->surface;
            $this->tourModelPath = Storage::url('3dmodel/'.$this->tourModel);
            $this->surfaceModelPath = Storage::url('3dmodel/surface/'.$this->surfaceModel);
        }
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
    #[Renderless]
    public function update()
    {
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

        $name = null;
        $surface = null;
        
        if (gettype($this->tourModel) !== 'string') {
            $name = $this->tourModel->getClientOriginalName();
            $name = $this->tour->id.'_'.$name;
            
            $this->tourModel->storeAs(path: 'public/3dmodel', name: $name);
        } else {
            $name = $this->tourModel;
        }
        
        if (gettype($this->surfaceModel) !== 'string') {
            $surface = $this->surfaceModel->getClientOriginalName();
            $surface = $this->tour->id.'_'.$surface;
            
            $this->surfaceModel->storeAs(path: 'public/3dmodel/surface/', name: $surface);
        } else {
            $surface = $this->surfaceModel;
        }
        
        $models = TourModel::where('tour_id', $this->tour->id)->get();
        if ($models->isEmpty()) {
            $data = array('tour_id'=>$this->tour->id, 'name'=>$name, 'surface'=>$surface);
            TourModel::create($data);
        } else {
            $models[0]->name = $name;
            $models[0]->surface = $surface;
            $models[0]->save();
        }

        $this->updateTourXMLFiles('app/public/tours/'.$this->tour->id);
    }

    private function updateTourXMLFiles($dir) {
        $dh = opendir(storage_path($dir));

        while (($file = readdir($dh)) !==false) {
            if ($file != '.'&& $file != '..') {
                $fullpath = $dir.'/'.$file;

                if (is_dir(storage_path($fullpath))) {
                    $this->updateTourXMLFiles($fullpath);
                } else {
                    if ($file == 'tour.xml') {
                        $this->updateTourXML($fullpath);
                    }
                }
            }
        }

        closedir($dh);
    }
    private function updateTourXML($file_path) {
        $new_code = '<!-- add the custom ThreeJS plugin -->
            <plugin name="threejs" url="/krpano/three.krpanoplugin.js" type="plugin" keep="true" />
        ';

        $file_contents = file_get_contents(storage_path($file_path));
        $check_file = strrpos($file_contents, '<!-- add the custom ThreeJS plugin -->');

        if ($check_file) {

        } else {
            $insert_position = strrpos($file_contents, '</krpano>');
    
            if ($insert_position !== false) {
                $file_contents = substr_replace($file_contents, $new_code, $insert_position, 0);
                file_put_contents(storage_path($file_path), $file_contents);
            }
        }
    }
}