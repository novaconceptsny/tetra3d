<x-wire-elements-pro::bootstrap.modal on-submit="submit">
    <x-slot name="title" class="fw-normal">{{ $heading }}</x-slot>

    <div class="row">
        <div class="col-6">
            <div class="row g-2 ps-4 pe-5">
                <x-form-input class="rounded-0" name="layout.name" label="Layout Name" />
                <x-form-select
                    class="rounded-0"
                    :disabled="$layout->id"
                    name="layout.tour_id" :options="$toursArray"
                    label="Select Tour" placeholder="Select Tour"
                />
            </div>
        </div>

        <div class="col-6 pe-4" x-show="$wire.layout.tour_id">
            <img :src="$wire.tourImages[$wire.layout.tour_id]">
        </div>
    </div>

    <x-slot name="buttons">
        @if($layout->id)
        <button class="btn btn-sm btn-outline-dark rounded-0 delete-btn" type="button" wire:click="deleteLayout({{ $layout->id }})">
            {{ __('Delete Layout') }}
        </button>
        @endif

        <button class="btn btn-sm btn-outline-dark rounded-0" type="submit">
            {{ __('Save Changes') }}
        </button>
        {{--<button class="btn btn-sm btn-primary" type="button" wire:modal="close">
            {{ __('Cancel') }}
        </button>--}}
    </x-slot>
</x-wire-elements-pro::bootstrap.modal>
