<?php

namespace App\Livewire\Forms;

use App\Models\ArtworkSurfaceState;
use App\Models\Layout;
use App\Models\Project;
use App\Models\Activity;
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

        // Check if the name already has a "copy" suffix
        $matches = [];
        if (preg_match('/^(.*) - copy(?: (\d+))?$/', $layout->name, $matches)) {
            // Extract the original name without "copy" and the suffix
            $originalName = trim($matches[1]);
        } else {
            // If no "copy" suffix, use the current name as the original name
            $originalName = $layout->name;
        }

        // Base name for duplicates
        $baseName = $originalName . ' - copy';

        // Find existing duplicates with similar names
        $existingLayouts = Layout::where('name', 'LIKE', $baseName . '%')->pluck('name');

        // Determine the next available suffix
        $suffix = 1;
        while ($existingLayouts->contains($baseName . ($suffix > 1 ? " $suffix" : ''))) {
            $suffix++;
        }

        // Assign the new name
        $this->layout->name = $baseName . ($suffix > 1 ? " $suffix" : '');
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

                if (file_exists($surfaceState->getFirstMedia('thumbnail')->getPath())){
                    $surfaceState->getFirstMedia('thumbnail')->copy($newSurfaceState, 'thumbnail');
                }

                if (file_exists($surfaceState->getFirstMedia('hotspot')->getPath())){
                    $surfaceState->getFirstMedia('hotspot')->copy($newSurfaceState, 'hotspot');
                }

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

        $activity = "Layout {$this->layout->name} duplicated";
        $url = route('tours.show', ['tour' => $newLayout->tour_id, 'layout_id' => $newLayout->id], false);

        Activity::create([
            'user_id' => auth()->id(),
            'project_id' => $newLayout->project_id,
            'layout_id' => $this->layout->id,
            'tour_id' => $newLayout->tour_id,
            'activity' => $activity,
            'url' => $url,
        ]);
    }

    public static function attributes(): array
    {
        return [
            'size' => 'lg'
        ];
    }
}
