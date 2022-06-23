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
        $this->activeForm = request('section', 'view');
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
            'shared' => 'Shared',
        ];

        $data['scales'] = [
            'square' => 'Square',
            'cross' => 'Cross',
        ];

        $data['spots'] = $this->spot->tour->spots()->where('id', '!=', $this->spot->id)->get();

        $sections = [
            'view' => [
                'name' => 'View',
            ],
            'scale-box' => [
                'name' => 'Scale Box',
            ],
            'background' => [
                'name' => 'Surface Background',
            ],
            'click' => [
                'name' => 'Surface Click',
            ],
            'navigation' => [
                'name' => 'Navigation',
            ],
            'overlay' => [
                'name' => 'Overlay',
            ],
        ];

        $data['sections'] = $sections;

        return view('livewire.xml-form', $data);
    }
}
