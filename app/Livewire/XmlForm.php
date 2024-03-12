<?php

namespace App\Livewire;

use App\Models\Spot;
use Livewire\Component;

class XmlForm extends Component
{
    public $activeForm = 'actions';

    public Spot $spot;

    public function mount(Spot $spot)
    {
        $this->spot = $spot;
        $this->activeForm = request('section', $this->activeForm);
    }

    public function render()
    {
        $data = array();

        $data['surface_click_styles'] = [
          'surface_click' => 'Surface Click',
          'surface_click_line' => 'Surface Click Line',
        ];

        $data['actions'] = [
            'navigations' => [
                'label' => 'Navigation',
                'options' => [
                    'default' => 'Default',
                    'on' => 'Enable All',
                    'off' => 'Disable All',
                ],
            ],
            'overlays' => [
                'label' => 'Overlay',
                'options' => [
                    'default' => 'Default',
                    'on' => 'Enable All',
                    'off' => 'Disable All',
                ],
            ],
            'surface_backgrounds' => [
                'label' => 'Surface Backgrounds',
                'options' => [
                    'default' => 'Default',
                    'shared' => 'Shared',
                    'main' => 'Main',
                    'live' => 'Live',
                ],
            ],
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
            'actions' => [
                'name' => 'Quick Actions',
            ],
            'view' => [
                'name' => 'View',
            ],
            /*'scale-box' => [
                'name' => 'Scale Box',
            ],*/
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
