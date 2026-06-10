<?php

namespace App\Livewire;

use Livewire\Component;

/**
 * Global slide-over manager (replacement for Wire Elements Pro's slide-over-pro).
 * Listens for slide-over.open / slide-over.close and renders the requested
 * Livewire component inside a right-hand sliding panel.
 */
class SlideOverPro extends Component
{
    public ?string $component = null;
    public array $arguments = [];
    public string $size = '2xl';

    protected $listeners = [
        'slide-over.open' => 'open',
        'slide-over.close' => 'close',
    ];

    public function open($component = null, $arguments = []): void
    {
        if (! $component) {
            return;
        }

        $this->component = $component;
        $this->arguments = (array) $arguments;
        $this->size = ModalPro::resolveSize($component, '2xl');
    }

    public function close(): void
    {
        $this->component = null;
        $this->arguments = [];
    }

    public function render()
    {
        return view('livewire.wire-elements-pro.slide-over-pro');
    }
}
