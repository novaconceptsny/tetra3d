<?php

namespace WireElements\Pro\Components;

use Livewire\Component;

/**
 * Local replacement for the Wire Elements Pro overlay base class.
 * Provides the close() API used by app Modal/SlideOver components.
 */
abstract class Overlay extends Component
{
    /**
     * Event name handled by the overlay manager (modal.close / slide-over.close).
     */
    abstract protected static function closeEvent(): string;

    public static function attributes(): array
    {
        return [];
    }

    public static function behavior(): array
    {
        return [];
    }

    /**
     * Close the overlay and optionally dispatch follow-up events.
     * Mirrors Wire Elements Pro: close(andDispatch: ['refresh', 'flash' => ['message' => '...']])
     */
    public function close($andDispatch = [], $andEmit = [], bool $withForce = false): void
    {
        foreach ([(array) $andDispatch, (array) $andEmit] as $events) {
            foreach ($events as $event => $params) {
                if (is_int($event)) {
                    $this->dispatch($params);
                } else {
                    $this->dispatch($event, ...(array) $params);
                }
            }
        }

        $this->dispatch(static::closeEvent());
    }
}
