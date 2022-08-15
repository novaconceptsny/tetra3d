<?php

namespace App\Http\Livewire;

use App\Models\SurfaceState;
use Livewire\Component;

class SurfaceStateTitle extends Component
{
    public $editing = false;
    public $title;
    public SurfaceState $state;

    protected $rules = [
        'title' => 'required|min:3',
    ];

    public function mount(SurfaceState $state)
    {
        $this->title = $state->name;
    }

    public function render()
    {
        return view('livewire.surface-state-title');
    }

    public function updateTitle()
    {
        $this->validateOnly('title');

        $this->state->update([
            'name' => $this->title
        ]);

        $this->editing = false;
    }
}
