<?php
namespace App\Livewire;

use App\Models\Company;
use App\Models\Layout;
use App\Models\Project;
use App\Models\Tour;
use App\Models\User;
use WireElements\Pro\Components\SlideOver\SlideOver;
use WireElements\Pro\Concerns\InteractsWithConfirmationModal;

class UpdatedTourSwitcher extends SlideOver
{
    use InteractsWithConfirmationModal;

    public Project|int $project;
    public Tour $selectedTour;
    public int $selectedTourId;

    public array $tourImages;

    protected $listeners = [
        'refresh'         => '$refresh',
        'duplicateLayout' => 'duplicateLayout',
    ];

    public function mount(Project $project)
    {
        $this->project        = $project;
        $this->selectedTour   = $project->assignedTours()->first();
        $this->selectedTourId = $this->selectedTour?->id;

        $this->tourImages = $this->project->assignedTours()
            ->mapWithKeys(function ($tour) {
                $map = $tour->maps()->first();
                return [$tour->id => $map ? $map->getFirstMediaUrl('image') : ''];
            })
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
            'size' => '4xl',
        ];
    }

    public function toggleFavorite($layoutId)
    {
        $layout              = Layout::findOrFail($layoutId);
        $layout->is_favorite = ! $layout->is_favorite;
        $layout->save();

        $user = auth()->user();

        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            $favorites = Layout::where('is_favorite', true)
                ->get();
        } else {
            $company = Company::findOrFail($user->company_id);

            // Get all user IDs in this company
            $userIds = User::where('company_id', $company->id)->pluck('id');
            
            $favorites = Layout::where('is_favorite', true)
                ->whereIn('user_id', $userIds)
                ->with(['tour' => function ($query) {
                    $query->withoutGlobalScope('forCurrentCompany');
                }])
                ->get();
        }

        $this->dispatch('favoritesUpdated', favorites: $favorites);
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
