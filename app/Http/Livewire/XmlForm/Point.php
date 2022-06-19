<?php

namespace App\Http\Livewire\XmlForm;

use Livewire\Component;

class Point extends Component
{
    /*public $points = [
        ['ath' => '', 'atv' => ''],
        ['ath' => '', 'atv' => ''],
        ['ath' => '', 'atv' => ''],
        ['ath' => '', 'atv' => ''],
    ];*/
    public $points = [];
    public $surface_id;

    public function mount()
    {
        if ( ! $this->points) {
            $this->points = [
                ['ath' => '', 'atv' => ''],
                ['ath' => '', 'atv' => ''],
                ['ath' => '', 'atv' => ''],
                ['ath' => '', 'atv' => ''],
            ];
        }
    }

    public function render()
    {
        return view('livewire.xml-form.point');
    }

    public function remove($i)
    {
        unset($this->points[$i]);
    }

    public function add()
    {
        $this->points[] = [
            'ath' => '',
            'atv' => '',
        ];
    }
}
