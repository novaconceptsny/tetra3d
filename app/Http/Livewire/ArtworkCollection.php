<?php

namespace App\Http\Livewire;

use App\Models\Artwork;
use Livewire\Component;
use Livewire\WithPagination;

class ArtworkCollection extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function mount()
    {
    }

    public function render()
    {
        $artworks = Artwork::simplePaginate(5);

        $data['artworks'] = $artworks;

        return view('livewire.artwork-collection', $data);
    }
}
