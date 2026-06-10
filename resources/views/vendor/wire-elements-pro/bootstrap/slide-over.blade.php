{{-- Local replacement for Wire Elements Pro's bootstrap slide-over Blade component --}}
@props(['contentPadding' => true, 'closeButton' => true])

<div {{ $attributes->merge(['class' => 'wep-bs-slide-over h-100 d-flex flex-column']) }}>
    @if ($closeButton)
        <div class="d-flex justify-content-end px-3 pt-2">
            <button type="button" class="btn-close" aria-label="Close"
                    onclick="Livewire.dispatch('slide-over.close')"></button>
        </div>
    @endif

    <div @class(['flex-grow-1 overflow-auto', 'p-3' => $contentPadding])>
        {{ $slot }}
    </div>
</div>
