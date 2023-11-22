<x-wire-elements-pro::bootstrap.modal on-submit="submit">
    <x-slot name="title">{{ $heading }}</x-slot>

    <div class="row g-2">
        <x-form-input name="layout.name" label="Layout Name" />
        <x-form-select name="layout.tour_id" :options="$tours" label="Select Tour" placeholder="Select Tour"/>
    </div>

    <x-slot name="buttons">
        <button class="btn btn-sm btn-success" type="submit">
            {{ __('Save Changes') }}
        </button>
        <button class="btn btn-sm btn-primary" type="button" wire:modal="close">
            {{ __('Cancel') }}
        </button>
    </x-slot>
</x-wire-elements-pro::bootstrap.modal>
