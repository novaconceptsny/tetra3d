<?php

namespace App\Livewire\Forms;

use App\Models\ArtworkSurfaceState;
use App\Models\Layout;
use App\Models\Project;
use App\Models\Tour;
use App\Models\Sculpture;
use App\Models\SurfaceState;
use WireElements\Pro\Components\Modal\Modal;
use WireElements\Pro\Concerns\InteractsWithConfirmationModal;

class Duplicate extends Modal
{
    use InteractsWithConfirmationModal;

    public Project|int $project;
    public Layout|int $layout;

    public string $heading;
    public $editing = false;

    public array $tourImages;

    public function rules(): array
    {
        return [
            'layout.name' => 'required',
            'layout.tour_id' => 'required',
            'layout.user_id' => 'required',
        ];
    }

    public function mount(Project $project, Layout $layout)
    {
        $this->layout = $layout;
        $this->layout->name = $layout->name . ' - copy';
        $this->heading = 'Duplicate Layout';
    }

    public function render()
    {
        return view('livewire.forms.duplicate');
    }

    public function submit()
    {
        $this->validate();

        $newLayout = $this->layout->replicate();
        $newLayout->name = $this->layout->name;
        $newLayout->save();

        $sculptures = Sculpture::where('layout_id', $this->layout->id)->get();
        if (count($sculptures) > 0)
            foreach ($sculptures as $sculpture) {
                $newSculpture = $sculpture->replicate();
                $newSculpture->layout_id = $newLayout->id;
                $newSculpture->save();
            }

        $surfaceStates = SurfaceState::where('layout_id', $this->layout->id)->get();
        if (count($surfaceStates) > 0)
            foreach ($surfaceStates as $surfaceState) {
                $newSurfaceState = $surfaceState->replicate();
                $newSurfaceState->layout_id = $newLayout->id;
                $newSurfaceState->save();

                $artworkSurfaceStates = ArtworkSurfaceState::where('surface_state_id', $surfaceState->id)->get();
                if (count($artworkSurfaceStates) > 0)
                    foreach ($artworkSurfaceStates as $artworkSurfaceState) {
                        $newArtworkSurfaceState = $artworkSurfaceState->replicate();
                        $newArtworkSurfaceState->surface_state_id = $newSurfaceState->id;
                        $newArtworkSurfaceState->save();
                    }
            }

        $this->close(andDispatch: [
            'refresh',
            'flashNotification' => ['message' => 'Layout duplicated']
        ]);
    }

    public static function attributes(): array
    {
        return [
            'size' => 'lg'
        ];
    }
}
