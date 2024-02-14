<x-wire-elements-pro::bootstrap.modal>
    <x-slot name="title">Krpano Tools</x-slot>

    <div class="row">
        <div class="col-12 pb-3 border-bottom">
            <button wire:click="runCommand" class="btn btn-success btn-sm" wire:loading.attr="disabled">
                {{ __('Make Panos') }}
            </button>
        </div>
        <div class="col-12 pt-3">
            <h5>Output:</h5>
            @if($output)
                <p style="color: red">{{ $output }}</p>
            @endif
            <pre wire:ignore class="command-output" id="command-output"></pre>

            @if($confirmation_required && !$confirmed)
                <p class=" mt-2">{{ __('Do you want to re-create panos ?') }}</p>
                <div>
                    <button class="btn btn-sm btn-danger rounded-0 me-2" wire:click="confirm">{{ __('Yes') }}</button>
                    <button class="btn btn-sm btn-success" data-bs-dismiss="modal">{{ __('No') }}</button>
                </div>
            @endif
        </div>
    </div>

    <x-slot name="buttons">
        <button class="btn btn-sm btn-primary" type="button" wire:modal="close">
            {{ __('Close') }}
        </button>
    </x-slot>
</x-wire-elements-pro::bootstrap.modal>
