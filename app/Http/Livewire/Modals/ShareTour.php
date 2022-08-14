<?php

namespace App\Http\Livewire\Modals;

use App\Enums\Spot\PanoStatus;
use App\Models\Project;
use App\Models\SharedTour;
use App\Models\Spot;
use App\Models\Tour;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Pusher\Pusher;
use Symfony\Component\Process\Process;

class ShareTour extends Component
{

    public $projectId;
    public $tourId;

    public $link = '';
    public $linkCopied = false;

    public function mount($tourId, $projectId)
    {
        $this->tourId = $tourId;
        $this->projectId = $projectId;
    }

    public function render()
    {
        return view('livewire.modals.share-tour');
    }

    public function generateLink()
    {
        $tour = Tour::findOrFail($this->tourId);
        $project = Project::findOrFail($this->projectId);

        $surfaces = $tour->surfaces()->with([
            'states' => fn($query) => $query->forProject($project->id)->active(),
        ])->get();

        $surface_states = [];

        foreach ($surfaces as $surface) {
            $surface_states[$surface->id] = $surface->states->first()?->id;
        }

        $sharedTour = SharedTour::updateOrCreate([
            'project_id' => $project->id,
            'user_id' => auth()->id(),
            'tour_id' => $tour->id,
            'surface_states' => json_encode($surface_states),
        ], []);

        $this->link = route('shared-tours.show', $sharedTour);
    }


}
