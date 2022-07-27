<?php

namespace App\Http\Livewire\XmlForm;

use Livewire\Component;

class Overlay extends Component
{
    public $overlays = [];
    public $spot;

    public function render()
    {
        return view('livewire.xml-form.overlay');
    }

    public function remove($i)
    {
        unset($this->overlays[$i]);
    }

    public function add()
    {
        $this->overlays[] = [
            'uuid' => str()->uuid(),
            'ath' => 0,
            'atv' => 0,
            'scale' => 1,
            'zorder' => 20,
            'enabled' => 1,
        ];
    }
}
