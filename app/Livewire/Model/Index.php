<?php
namespace App\Livewire\Model;

use App\Models\SpotsPosition;
use App\Models\SurfaceInfo;
use App\Models\Tour;
use App\Models\TourModel;
use Illuminate\Support\Facades\Storage;
use Livewire\Attributes\Renderless;
use Livewire\Component;
use Livewire\WithFileUploads;
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
    public $spots = [];
    public $spotsPosition = [];
    public $surfaceArray = [];
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
        /******  f5aece62-d535-48e3-8c08-30b1af93fe32  *******/
    }

    public function render()
    {
        return view('livewire.model.index');
    }
    public function setModel()
    {
        $models = TourModel::where('tour_id', $this->tour->id)->get();
        if ($models->isEmpty()) {
            $this->tourModel    = 'Empty';
            $this->surfaceModel = 'Empty';
        } else {
            $this->tourModel        = $models[0]->name;
            $this->surfaceModel     = $models[0]->surface;
            $this->tourModelPath    = Storage::url('3dmodel/' . $this->tourModel);
            $this->surfaceModelPath = Storage::url('3dmodel/surface/' . $this->surfaceModel);
        }
    }
    public function setSpots()
    {
        $temp_spots       = SpotsPosition::where('tour_id', $this->tour->id)->get();
        $tour_spots_count = count($this->tour->spots);

        // Check if temp_spots count matches the count of tour spots
        if ($temp_spots->count() !== $tour_spots_count) {
            $tour_spot_ids = $this->tour->spots->pluck('id')->toArray();
            $temp_spot_ids = $temp_spots->pluck('spot_id')->toArray();

            // Delete spots that no longer exist in tour
            foreach ($temp_spot_ids as $temp_spot_id) {
                if (! in_array($temp_spot_id, $tour_spot_ids)) {
                    SpotsPosition::where('tour_id', $this->tour->id)
                        ->where('spot_id', $temp_spot_id)
                        ->delete();
                }
            }

            // Create new spots that exist in tour but not in positions
            foreach ($tour_spot_ids as $tour_spot_id) {
                if (! in_array($tour_spot_id, $temp_spot_ids)) {
                    $data = [
                        "tour_id" => $this->tour->id,
                        "spot_id" => $tour_spot_id,
                        "x"       => 0.0,
                        "y"       => 0.0,
                        "z"       => 0.0,
                    ];
                    SpotsPosition::create($data);
                }
            }

            $temp_spots = SpotsPosition::where('tour_id', $this->tour->id)->get();
        }

        $spotsPosition = [];

        foreach ($temp_spots as $spot) {
            $spotsPosition[$spot->spot_id] = [
                'x' => $spot->x ?? 0.0,
                'y' => $spot->y ?? 0.0,
                'z' => $spot->z ?? 0.0,
            ];
        }

        $this->spotsPosition = $spotsPosition;
    }

    public function setSurfaces()
    {
        $temp_surfaces       = SurfaceInfo::where('tour_id', $this->tour->id)->get();
        $tour_surfaces_count = count($this->tour->surfaces);

        // Check if temp_surfaces count matches the count of tour surfaces
        if ($temp_surfaces->count() !== $tour_surfaces_count) {
            $tour_surface_ids = $this->tour->surfaces->pluck('id')->toArray();
            $temp_surface_ids = $temp_surfaces->pluck('surface_id')->toArray();
            foreach ($temp_surface_ids as $temp_surface_id) {
                if (! in_array($temp_surface_id, $tour_surface_ids)) {
                    SurfaceInfo::where('tour_id', $this->tour->id)
                        ->where('surface_id', $temp_surface_id)
                        ->delete();
                }
            }
            foreach ($tour_surface_ids as $tour_surface_id) {
                if (! in_array($tour_surface_id, $temp_surface_ids)) {
                    $data = [
                        "tour_id"      => $this->tour->id,
                        "surface_id"   => $tour_surface_id,
                        "normalvector" => ["x" => 0.0, "y" => 0.0, "z" => 0.0],
                        "start_pos"    => ["x" => 0.0, "y" => 0.0, "z" => 0.0],
                        "width"        => 1,
                        "height"       => 1,
                    ];
                    SurfaceInfo::create($data);
                }
            }
            $temp_surfaces = SurfaceInfo::where('tour_id', $this->tour->id)->get();
        }

        $surfaceArray = [];

        foreach ($temp_surfaces as $surface) {
            $surfaceArray[$surface->surface_id] = [
                'width'        => $surface->width ?? 0,
                'height'       => $surface->height ?? 0,
                'normalvector' => $surface->normalvector ?? ["x" => 0.0, "y" => 0.0, "z" => 0.0],
                'start_pos'    => $surface->start_pos ?? ["x" => 0.0, "y" => 0.0, "z" => 0.0],
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
            foreach ($temp_spots as $spot) {
                $spot->x = $this->spotsPosition[$spot->spot_id]['x'];
                $spot->y = $this->spotsPosition[$spot->spot_id]['y'];
                $spot->z = $this->spotsPosition[$spot->spot_id]['z'];
                $spot->save();
            }
        }

        $temp_surfaces = SurfaceInfo::where('tour_id', $this->tour->id)->get();

        if ($temp_surfaces->isEmpty()) {

        } else {
            foreach ($temp_surfaces as $surface) {
                $surface->normalvector = $this->surfaceArray[$surface->surface_id]['normalvector'];
                $surface->start_pos    = $this->surfaceArray[$surface->surface_id]['start_pos'];
                $surface->width        = $this->surfaceArray[$surface->surface_id]['width'];
                $surface->height       = $this->surfaceArray[$surface->surface_id]['height'];
                $surface->save();
            }
        }

        $name    = null;
        $surface = null;

        if (gettype($this->tourModel) !== 'string') {
            $name = $this->tourModel->getClientOriginalName();
            $name = $this->tour->id . '_' . $name;

            $this->tourModel->storeAs(path: 'public/3dmodel', name: $name);
        } else {
            $name = $this->tourModel;
        }

        if (gettype($this->surfaceModel) !== 'string') {
            $surface = $this->surfaceModel->getClientOriginalName();
            $surface = $this->tour->id . '_' . $surface;

            $this->surfaceModel->storeAs(path: 'public/3dmodel/surface/', name: $surface);
        } else {
            $surface = $this->surfaceModel;
        }

        $models = TourModel::where('tour_id', $this->tour->id)->get();
        if ($models->isEmpty()) {
            $data = ['tour_id' => $this->tour->id, 'name' => $name, 'surface' => $surface];
            TourModel::create($data);
        } else {
            $models[0]->name    = $name;
            $models[0]->surface = $surface;
            $models[0]->save();
        }

        $this->dispatch('flashNotification', message: 'Model updated');
    }
}
