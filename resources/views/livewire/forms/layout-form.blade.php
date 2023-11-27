<x-wire-elements-pro::bootstrap.modal on-submit="submit">
    <x-slot name="title">{{ $heading }}</x-slot>

    <div class="row">
        <div class="col-6">
            <div class="row g-2">
                <x-form-input name="layout.name" label="Layout Name" />
                <x-form-select name="layout.tour_id" :options="$toursArray" label="Select Tour" placeholder="Select Tour"/>
            </div>
        </div>

        <div class="col-6" x-show="$wire.layout.tour_id">
            <img :src="$wire.tourImages[$wire.layout.tour_id]">
        </div>
    </div>

    <x-slot name="buttons">
        <button class="btn btn-sm btn-danger" type="button" wire:click="deleteLayout">
            {{ __('Delete Layout') }}
        </button>

        <button class="btn btn-sm btn-success" type="submit">
            {{ __('Save Changes') }}
        </button>
        {{--<button class="btn btn-sm btn-primary" type="button" wire:modal="close">
            {{ __('Cancel') }}
        </button>--}}
    </x-slot>
</x-wire-elements-pro::bootstrap.modal>
