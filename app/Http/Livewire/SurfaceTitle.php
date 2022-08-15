<?php

namespace App\Http\Livewire;

use App\Models\Surface;
use Livewire\Component;

class SurfaceTitle extends Component
{
    public $editing = false;
    public $title;
    public Surface $surface;

    protected $rules = [
        'title' => 'required|min:3',
    ];

    public function mount(Surface $surface)
    {
        $this->title = $surface->name;
    }

    public function render()
    {
        return view('livewire.surface-title');
    }

    public function updateTitle()
    {
        $this->validateOnly('title');

        $this->surface->update([
            'name' => $this->title
        ]);

        $this->editing = false;
    }
}
