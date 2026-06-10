{{-- Local replacement for Wire Elements Pro's bootstrap modal Blade component --}}
@props(['onSubmit' => null, 'contentPadding' => true, 'closeButton' => true])

<div {{ $attributes->merge(['class' => 'wep-bs-modal']) }}>
    @if ($onSubmit)
        <form wire:submit="{{ $onSubmit }}">
    @endif

    @isset($title)
        <div class="d-flex align-items-center justify-content-between border-bottom px-3 py-2">
            <h5 {{ $title->attributes->merge(['class' => 'mb-0']) }}>{{ $title }}</h5>
            @if ($closeButton)
                <button type="button" class="btn-close" aria-label="Close"
                        onclick="Livewire.dispatch('modal.close')"></button>
            @endif
        </div>
    @endisset

    <div @class(['p-3' => $contentPadding])>
        {{ $slot }}
    </div>

    @isset($buttons)
        <div class="d-flex justify-content-end gap-2 border-top px-3 py-2">
            {{ $buttons }}
        </div>
    @endisset

    @if ($onSubmit)
        </form>
    @endif
</div>
