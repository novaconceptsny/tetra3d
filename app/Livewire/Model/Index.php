<?php

namespace App\Livewire\Model;

use App\Models\Tour;
use App\Models\SpotsPosition;
use App\Models\SurfaceInfo;
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
    public $surfaceArray = array();
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
        $this->setSurfaces();
        $this->setModel();
/*************  ✨ Codeium Command ⭐  *************/
    /**
     * Renders the component.
     *
     * @return \Illuminate\Contracts\View\View
     */
/******  f5aece62-d535-48e3-8c08-30b1af93fe32  *******/    }

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

    public function setSurfaces() {
        $temp_surfaces = SurfaceInfo::where('tour_id', $this->tour->id)->get();
        $tour_surfaces_count = count($this->tour->surfaces);
    
        // Check if temp_surfaces count matches the count of tour surfaces
        if ($temp_surfaces->count() !== $tour_surfaces_count) {
            // Delete existing SurfaceInfo records for this tour
            SurfaceInfo::where('tour_id', $this->tour->id)->delete();
    
            // Recreate the SurfaceInfo records
            foreach ($this->tour->surfaces as $surface) {
                $data = [
                    "surface_id" => $surface->id,
                    "tour_id" => $this->tour->id,
                    "normalvector" => ["x" => 0.0, "y" => 0.0, "z" => 0.0],
                    "start_pos" => ["x" => 0.0, "y" => 0.0, "z" => 0.0],
                    "width" => 1,
                    "height" => 1,
                ];
                SurfaceInfo::create($data);
            }
    
            $temp_surfaces = SurfaceInfo::where('tour_id', $this->tour->id)->get();
        }
    
        $surfaceArray = [];
    
        foreach ($temp_surfaces as $surface) {
            $surfaceArray[$surface->surface_id] = [
                'width' => $surface->width ?? 0,
                'height' => $surface->height ?? 0,
                'normalvector' => $surface->normalvector ?? ["x" => 0.0, "y" => 0.0, "z" => 0.0],
                'start_pos' => $surface->start_pos ?? ["x" => 0.0, "y" => 0.0, "z" => 0.0],
            ];
        }
    
        $this->surfaceArray = $surfaceArray;
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

        $temp_surfaces = SurfaceInfo::where('tour_id', $this->tour->id)->get();
       
        if ($temp_surfaces->isEmpty()) {

        } else {
            dump($this->surfaceArray);
            dump($temp_surfaces);
            foreach($temp_surfaces as $surface) {
                $surface->normalvector = $this->surfaceArray[$surface->surface_id]['normalvector'];
                $surface->start_pos = $this->surfaceArray[$surface->surface_id]['start_pos'];
                $surface->width = $this->surfaceArray[$surface->surface_id]['width'];
                $surface->height = $this->surfaceArray[$surface->surface_id]['height'];
                $surface->save();
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