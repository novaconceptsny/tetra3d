<x-wire-elements-pro::bootstrap.modal on-submit="submit">
    <x-slot name="title" class="fw-normal">{{ $heading }}</x-slot>

    <div class="row">
        <div class="col-12">
            <div class="row g-2 ps-4 pe-5">
                <x-form-input class="rounded-0" name="layout.name" label="Layout Name" />
            </div>
        </div>
    </div>

    <x-slot name="buttons">
        <button class="btn btn-sm btn-outline-dark rounded-0" type="submit">
            {{ __('Duplicate Layout') }}
        </button>
        <button class="btn btn-sm btn-outline-dark rounded-0" type="button" wire:modal="close">
            {{ __('Cancel') }}
        </button>
    </x-slot>
</x-wire-elements-pro::bootstrap.modal>
