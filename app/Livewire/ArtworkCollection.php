<?php

namespace App\Livewire;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class ArtworkCollection extends Component
{
    use WithPagination;

    public Project $project;
    public $search;
    public $searchBy = 'all';
    public $collectionId;

    protected $paginationTheme = 'bootstrap';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    public function render()
    {
        $data['collections'] = $this->project->artworkCollections;

        $artworks = $this->project->artworks()
            ->when(
                $this->search,
                fn($query) => $query->whereAnyColumnLike($this->search, ['artist', 'name'])
            )
            ->when(
                $this->collectionId,
                fn($query) => $query->where('artworks.artwork_collection_id', $this->collectionId)
            )
            ->with('media')->paginate(25);

        $data['artworks'] = $artworks;

        return view('livewire.artwork-collection', $data);
    }
}
