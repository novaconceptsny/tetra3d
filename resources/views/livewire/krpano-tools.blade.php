<div>
    <button wire:click="runCommand" class="btn btn-success" wire:loading.attr="disabled">{{ __('Make Panos') }}</button>
    <pre wire:ignore class="command-output" id="command-output"></pre>
</div>
