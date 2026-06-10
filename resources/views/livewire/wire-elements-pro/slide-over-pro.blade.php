@php
    $widths = [
        'xs' => 320, 'sm' => 384, 'md' => 448, 'lg' => 512, 'xl' => 576,
        '2xl' => 672, '3xl' => 768, '4xl' => 896, '5xl' => 1024,
        '6xl' => 1152, '7xl' => 1280,
    ];
    $width = $widths[$size] ?? 672;
@endphp

<div>
    @if ($component)
        <div class="wep-slide-over" role="dialog" aria-modal="true">
            <div class="wep-slide-over-backdrop" wire:click="close"></div>
            <div class="wep-slide-over-panel" style="width: {{ $width }}px;">
                @livewire($component, $arguments, key('wep-slide-over-' . $component . '-' . md5(json_encode($arguments))))
            </div>
        </div>
    @endif

    <style>
        .wep-slide-over {
            position: fixed;
            inset: 0;
            z-index: 1050;
        }

        .wep-slide-over-backdrop {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.25);
            backdrop-filter: blur(4px);
        }

        .wep-slide-over-panel {
            position: absolute;
            top: 0;
            right: 0;
            height: 100%;
            max-width: 95vw;
            background: #fff;
            box-shadow: -10px 0 40px rgba(0, 0, 0, 0.2);
            overflow-y: auto;
            animation: wep-slide-in 0.2s ease-out;
        }

        @keyframes wep-slide-in {
            from { transform: translateX(100%); }
            to { transform: translateX(0); }
        }
    </style>
</div>
