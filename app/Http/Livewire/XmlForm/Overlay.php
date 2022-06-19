<?php

namespace App\Http\Livewire\XmlForm;

use Livewire\Component;

class Overlay extends Component
{
    public $overlays = [];

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
            'ath' => '',
            'atv' => '',
            'scale' => '',
            'zorder' => '',
        ];
    }
}
