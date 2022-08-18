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
    public $form = false;
    public $method = 'Delete';
    public $title = "Confirm";
    public $confirmBtnAttributes = "";
    public $message = "Are you sure you want to perform this action?";


    public function render()
    {
        return view('livewire.modals.confirm');
    }

    public function mount($options = array())
    {
        $this->route = $options['route'] ?? $this->route;
        $this->message = $options['message'] ?? $this->message;
        $this->form = $options['form'] ?? (bool)$this->route;
        $this->confirmBtnAttributes = $options['confirm_btn_attributes'] ?? "";
    }


    public function confirm(){
        $this->confirmed = true;
        $this->output = false;
    }

}
