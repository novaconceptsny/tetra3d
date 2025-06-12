<?php

namespace App\Livewire;

use App\Models\Layout;
use App\Models\Project;
use App\Models\Tour;
use WireElements\Pro\Components\SlideOver\SlideOver;
use WireElements\Pro\Concerns\InteractsWithConfirmationModal;
use App\Models\Sculpture;
use App\Models\SurfaceState;

class UpdatedTourSwitcher extends SlideOver
{
    use InteractsWithConfirmationModal;

    public Project|int $project;
    public Tour $selectedTour;
    public int $selectedTourId;

    public array $tourImages;

    protected $listeners = [
        'refresh' => '$refresh',
        'duplicateLayout' => 'duplicateLayout'
    ];

    public function mount(Project $project)
    {
        $this->project = $project;
        $this->selectedTour = $project->assignedTours()->first();
        $this->selectedTourId = $this->selectedTour?->id;

        $this->tourImages = $this->project->assignedTours()
            ->mapWithKeys(fn ($tour) => [$tour->id => $tour->getFirstMediaUrl('thumbnail')])
            ->all();
            
    }

    public function render()
    {
        return view('livewire.updated-tour-switcher');
    }

    public function selectTour()
    {
        $this->selectedTour = $this->project->assignedTours()->where('id', $this->selectedTourId)->first();
    }

    public static function attributes(): array
    {
        return [
            'size' => '4xl'
        ];
    }

    public function toggleFavorite($layoutId)
    {
        $layout = Layout::findOrFail($layoutId);
        $layout->is_favorite = !$layout->is_favorite;
        $layout->save();
    }

    public function deleteLayout(Layout $layout)
    {
        $this->askForConfirmation(function () use ($layout) {
            $layout->delete();
            // $this->close();
            // $this->dispatch('refresh');
            $this->dispatch('flashNotification', message: 'Layout deleted');
            $this->dispatch('layoutDeleted', layoutId: $layout->id);
        });
    }


}
