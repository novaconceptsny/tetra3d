<?php

namespace WireElements\Pro\Concerns;

use Illuminate\Database\Eloquent\Model;
use Livewire\ImplicitlyBoundMethod;

/**
 * Local replacement for the Wire Elements Pro confirmation trait.
 *
 * askForConfirmation() opens a global confirmation dialog (rendered by the
 * modal-pro manager component). When the user confirms, the original method
 * is invoked again via runConfirmedAction() with the confirmed flag set,
 * so the callback executes.
 */
trait InteractsWithConfirmationModal
{
    public bool $actionIsConfirmed = false;

    public function askForConfirmation(callable $callback, array $modal = []): void
    {
        if ($this->actionIsConfirmed) {
            $this->actionIsConfirmed = false;
            $callback();

            return;
        }

        $caller = debug_backtrace(DEBUG_BACKTRACE_PROVIDE_OBJECT, 2)[1] ?? [];

        $args = array_map(
            fn ($arg) => $arg instanceof Model ? $arg->getKey() : $arg,
            array_values($caller['args'] ?? [])
        );

        $this->dispatch(
            'confirmation-modal.open',
            callerId: $this->getId(),
            method: $caller['function'] ?? null,
            args: $args,
            message: $modal['message'] ?? 'Are you sure you want to perform this action?',
            confirmLabel: $modal['confirm'] ?? 'Confirm',
            cancelLabel: $modal['cancel'] ?? 'Cancel',
        );
    }

    /**
     * Called from the confirmation dialog. Re-invokes the original action
     * with implicit model binding (so type-hinted models are re-resolved).
     */
    public function runConfirmedAction(string $method, array $args = [])
    {
        abort_unless(
            method_exists($this, $method)
            && $method !== 'runConfirmedAction'
            && ! str_starts_with($method, '__'),
            403
        );

        $this->actionIsConfirmed = true;

        return ImplicitlyBoundMethod::call(app(), [$this, $method], array_values($args));
    }
}
