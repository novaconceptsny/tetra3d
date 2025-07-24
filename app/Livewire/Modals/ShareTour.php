<?php

namespace App\Livewire\Modals;

use App\Models\Layout;
use App\Models\Project;
use App\Models\SharedTour;
use App\Models\SharedLayout;
use App\Models\Spot;
use App\Models\Tour;
use WireElements\Pro\Components\Modal\Modal;

class ShareTour extends Modal
{
    public Layout|int $layout;
    public SharedLayout|int|null $sharedLayout;
    public $tourId;
    public $spotId;

    public $link = '';
    public $linkCopied = false;
    public $share_type = 'tour';
    public $spotSelectionAllowed = false;
    public $spot = null;

    public function mount(Layout $layout, SharedLayout $sharedLayout, $spotId = null)
    {
        $this->layout = $layout;
        $this->sharedLayout = $sharedLayout;
        $this->spotId = $spotId;
        $this->spot = Spot::find($this->spotId);

        if($this->spot){
            $this->spotSelectionAllowed = true;
        }
    }

    public function render()
    {
        return view('livewire.modals.share-tour');
    }

    public function generateLink()
    {
        $layout = $this->layout;
        $sharedLayout = $this->sharedLayout;

        /*$surfaces = $layout->surfaces()->with([
            'states' => fn($query) => $query->forLayout($layout->id)->active(),
        ])->get();

        $surface_states = [];

        foreach ($surfaces as $surface) {
            $surface_states[$surface->id] = $surface->states->first()?->id;
        }*/

        $surface_states = $layout->surfaceStates->pluck('id')->toArray();

        // Create a new SharedTour record instead of updating existing one
        $sharedTour = SharedTour::create([
            'spot_id' => $this->share_type == 'spot' ? $this->spot?->id : null,
            'layout_id' => $layout->id,
            'shared_layout_id' => $sharedLayout->id,
            'user_id' => auth()->id(),
            'surface_states' => json_encode($surface_states),
        ]);

        $this->link = route('shared-tours.show', $sharedTour);
    }


}
