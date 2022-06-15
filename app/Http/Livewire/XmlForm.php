<?php

namespace App\Http\Livewire;

use App\Models\Spot;
use Livewire\Component;

class XmlForm extends Component
{
    public $activeForm = 'view';

    public Spot $spot;

    public function mount(Spot $spot)
    {
        $this->spot = $spot;
    }

    public function render()
    {
        $data = array();

        $data['surface_click_styles'] = [
          'surface_click' => 'Surface Click',
          'surface_click_line' => 'Surface Click Line',
        ];

        $data['surface_types'] = [
            'live' => 'Live',
            'main' => 'Main',
            'share' => 'Share',
        ];

        $sections = [
            'view' => [
                'name' => 'View',
            ],
            'background' => [
                'name' => 'Surface Background',
            ],
            'click' => [
                'name' => 'Surface Click',
            ],
            'overlay' => [
                'name' => 'Overlay',
            ],
        ];

        $data['sections'] = $sections;

        return view('livewire.xml-form', $data);
    }
}
