<?php

namespace App\Livewire\Modals;

use Livewire\Component;

class BaseModal extends Component
{
    public $alias;
    public $params = [];

    protected $listeners = ['showModal', 'resetModal'];

    public function render()
    {
        return view('livewire.modals.base');
    }

    public function showModal($alias, ...$params)
    {
        $this->alias = $alias;
        $this->params = $params;

        $this->dispatch('showBootstrapModal');
    }

    public function resetModal()
    {
        $this->reset();
    }
}
