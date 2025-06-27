<?php

namespace App\Livewire\Modals;

use App\Models\Artwork;
use WireElements\Pro\Components\Modal\Modal;

class ArtworkInfo extends Modal
{
    public $artworkId;
    public $artwork = null;

    public function mount($artworkId = null)
    {
        $this->artworkId = $artworkId;
        if ($this->artworkId) {
            $this->artwork = Artwork::find($this->artworkId);
        }
    }

    public function render()
    {
        return view('livewire.modals.artwork-info');
    }

    public function closeModal()
    {
        $this->dispatch('modal.close');
    }
 
    public static function attributes(): array
    {
        return [
            // Set the modal size to 2xl, you can choose between:
            // xs, sm, md, lg, xl, 2xl, 3xl, 4xl, 5xl, 6xl, 7xl, fullscreen
            'size' => '8xl',
        ];
    }
} 