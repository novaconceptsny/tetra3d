<?php

namespace App\Http\Livewire\Modals;

use App\Enums\Spot\PanoStatus;
use App\Models\Spot;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Pusher\Pusher;
use Symfony\Component\Process\Process;

class Confirm extends Component
{

    public $route = '';
    public $form = true;
    public $method = 'Delete';
    public $title = "Confirm";
    public $message = "Are you sure you want to perform this action?";

    public function render()
    {
        return view('livewire.modals.confirm');
    }

    public function mount($options)
    {
        $this->route = $options['route'] ?? $this->route;
        $this->message = $options['message'] ?? $this->message;
        $this->form = $options['form'] ?? $this->form;
    }


    public function confirm(){
        $this->confirmed = true;
        $this->output = false;
    }

}
