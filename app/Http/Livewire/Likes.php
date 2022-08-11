<?php

namespace App\Http\Livewire;

use App\Models\Like;
use Livewire\Component;

class Likes extends Component
{
    public $likeable;

    public function render()
    {
        return view('livewire.likes');
    }

    public function toggleLike()
    {
        $this->likeable->toggleLike();
    }
}
