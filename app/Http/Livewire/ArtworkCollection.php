<?php

namespace App\Http\Livewire;

use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class ArtworkCollection extends Component
{
    use WithPagination;

    public Project $project;
    public $search;
    public $searchBy = 'all';

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
        $artworks = $this->project->artworks()
            ->when(
                $this->search && $this->searchBy == 'all',
                fn($query) => $query->whereAnyColumnLike($this->search, ['artist', 'name'])
            )
            ->when(
                $this->search && $this->searchBy == 'artist',
                fn($query) => $query->whereAnyColumnLike($this->search, ['artist'])
            )
            ->when(
                $this->search && $this->searchBy == 'name',
                fn($query) => $query->whereAnyColumnLike($this->search, ['name'])
            )
            ->with('media')->paginate(25);

        $data['artworks'] = $artworks;

        return view('livewire.artwork-collection', $data);
    }
}
