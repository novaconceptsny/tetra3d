<?php

namespace App\Http\Livewire;

use App\Models\Artwork;
use App\Models\Company;
use App\Models\Project;
use Livewire\Component;
use Livewire\WithPagination;

class ArtworkCollection extends Component
{
    use WithPagination;

    public Project $project;

    protected $paginationTheme = 'bootstrap';

    public function mount(Project $project)
    {
        $this->project = $project;
    }

    public function render()
    {
        $artworks = Artwork::forCompany($this->project->company_id)->with('media')->simplePaginate(10);

        $data['artworks'] = $artworks;

        return view('livewire.artwork-collection', $data);
    }
}
