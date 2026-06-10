<?php

namespace App\Livewire;

use Livewire\Component;
use Livewire\Mechanisms\ComponentRegistry;

/**
 * Global modal manager (replacement for Wire Elements Pro's modal-pro).
 * Listens for modal.open / modal.close and renders the requested Livewire
 * component inside a modal shell. Also hosts the confirmation dialog used
 * by the InteractsWithConfirmationModal trait.
 */
class ModalPro extends Component
{
    public ?string $component = null;
    public array $arguments = [];
    public string $size = '2xl';

    public bool $confirmationOpen = false;
    public ?string $confirmCallerId = null;
    public ?string $confirmMethod = null;
    public array $confirmArgs = [];
    public string $confirmMessage = '';
    public string $confirmLabel = 'Confirm';
    public string $cancelLabel = 'Cancel';

    protected $listeners = [
        'modal.open' => 'open',
        'modal.close' => 'close',
        'confirmation-modal.open' => 'openConfirmation',
    ];

    public function open($component = null, $arguments = []): void
    {
        if (! $component) {
            return;
        }

        $this->component = $component;
        $this->arguments = (array) $arguments;
        $this->size = static::resolveSize($component, '2xl');
    }

    public function close(): void
    {
        $this->component = null;
        $this->arguments = [];
    }

    public function openConfirmation(
        $callerId = null,
        $method = null,
        $args = [],
        $message = 'Are you sure you want to perform this action?',
        $confirmLabel = 'Confirm',
        $cancelLabel = 'Cancel'
    ): void {
        if (! $callerId || ! $method) {
            return;
        }

        $this->confirmationOpen = true;
        $this->confirmCallerId = $callerId;
        $this->confirmMethod = $method;
        $this->confirmArgs = (array) $args;
        $this->confirmMessage = $message;
        $this->confirmLabel = $confirmLabel;
        $this->cancelLabel = $cancelLabel;
    }

    public function closeConfirmation(): void
    {
        $this->confirmationOpen = false;
        $this->confirmCallerId = null;
        $this->confirmMethod = null;
        $this->confirmArgs = [];
    }

    public static function resolveSize(string $component, string $default): string
    {
        try {
            $class = app(ComponentRegistry::class)->getClass($component);

            if ($class && method_exists($class, 'attributes')) {
                return $class::attributes()['size'] ?? $default;
            }
        } catch (\Throwable) {
            // Unknown component name; fall through to default size.
        }

        return $default;
    }

    public function render()
    {
        return view('livewire.wire-elements-pro.modal-pro');
    }
}
