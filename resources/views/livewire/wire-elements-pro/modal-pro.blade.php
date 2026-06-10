@php
    $widths = [
        'xs' => 320, 'sm' => 384, 'md' => 448, 'lg' => 512, 'xl' => 576,
        '2xl' => 672, '3xl' => 768, '4xl' => 896, '5xl' => 1024,
        '6xl' => 1152, '7xl' => 1280,
    ];
    $width = $widths[$size] ?? 672;
@endphp

<div>
    {{-- Dynamic modal --}}
    @if ($component)
        <div class="wep-modal" role="dialog" aria-modal="true">
            <div class="wep-modal-backdrop" wire:click="close"></div>
            <div class="wep-modal-container">
                <div class="wep-modal-content" style="width: {{ $width }}px;">
                    @livewire($component, $arguments, key('wep-modal-' . $component . '-' . md5(json_encode($arguments))))
                </div>
            </div>
        </div>
    @endif

    {{-- Confirmation dialog (used by InteractsWithConfirmationModal) --}}
    @if ($confirmationOpen)
        <div class="wep-confirmation" role="alertdialog" aria-modal="true">
            <div class="wep-modal-backdrop" wire:click="closeConfirmation"></div>
            <div class="wep-modal-container">
                <div class="wep-modal-content wep-confirmation-content">
                    <p class="wep-confirmation-message">{{ $confirmMessage }}</p>
                    <div class="wep-confirmation-buttons">
                        <button type="button" class="btn btn-sm btn-outline-secondary" wire:click="closeConfirmation">
                            {{ $cancelLabel }}
                        </button>
                        <button type="button" class="btn btn-sm btn-danger"
                                x-on:click="window.Livewire.find(@js($confirmCallerId))?.call('runConfirmedAction', @js($confirmMethod), @js($confirmArgs)); $wire.closeConfirmation()">
                            {{ $confirmLabel }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif

    <style>
        .wep-modal, .wep-confirmation {
            position: fixed;
            inset: 0;
            z-index: 1055;
        }

        /* Must sit above .wep-modal-content (z-index 2001 !important in custom.css) */
        .wep-confirmation {
            z-index: 2100 !important;
        }

        .wep-confirmation .wep-confirmation-content {
            position: relative;
            z-index: 2101;
        }

        .wep-modal-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(4px);
        }

        .wep-modal-container {
            position: absolute;
            inset: 0;
            display: flex;
            align-items: flex-start;
            justify-content: center;
            padding: 3rem 1rem;
            overflow-y: auto;
            pointer-events: none;
        }

        .wep-modal-content {
            pointer-events: auto;
            background: #fff;
            border-radius: 0.5rem;
            box-shadow: 0 20px 50px rgba(0, 0, 0, 0.3);
            max-width: 100%;
            animation: wep-modal-in 0.15s ease-out;
        }

        .wep-confirmation-content {
            width: 400px;
            padding: 1.5rem;
        }

        .wep-confirmation-message {
            margin-bottom: 1rem;
        }

        .wep-confirmation-buttons {
            display: flex;
            justify-content: flex-end;
            gap: 0.5rem;
        }

        @keyframes wep-modal-in {
            from { opacity: 0; transform: scale(0.97); }
            to { opacity: 1; transform: scale(1); }
        }
    </style>

    @script
    <script>
        if (!window.__wepDirectiveShim) {
            window.__wepDirectiveShim = true;

            // Re-implements Wire Elements Pro's wire:modal / wire:slide-over
            // click directives, e.g.:
            //   wire:modal="forms.layout-form, {"project":1}"
            //   wire:slide-over="close"
            document.addEventListener('click', function (e) {
                const el = e.target.closest('[wire\\:modal], [wire\\:slide-over]');
                if (!el) return;

                ['modal', 'slide-over'].forEach(function (type) {
                    const expression = el.getAttribute('wire:' + type);
                    if (expression === null) return;

                    e.preventDefault();

                    const i = expression.indexOf(',');
                    const name = (i === -1 ? expression : expression.slice(0, i)).trim();

                    if (name === 'close') {
                        Livewire.dispatch(type + '.close');
                        return;
                    }

                    let args = {};
                    if (i !== -1) {
                        try {
                            args = JSON.parse(expression.slice(i + 1).trim());
                        } catch (err) {
                            console.error('wire:' + type + ': could not parse arguments', expression);
                        }
                    }

                    Livewire.dispatch(type + '.open', { component: name, arguments: args });
                });
            });
        }
    </script>
    @endscript
</div>
