<div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header py-1 border-0">
            <h4 class="modal-title">{{ __('Krpano Tools') }}</h4>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-hidden="true"></button>
        </div>
        <div class="modal-body">
            <div class="row">
                <div class="col-12 pb-3 border-bottom">
                    <button wire:click="runCommand" class="btn btn-success btn-sm" wire:loading.attr="disabled">
                        {{ __('Make Panos') }}
                    </button>
                </div>
                <div class="col-12 pt-3">
                    <h5>Output:</h5>
                    @if($output)
                        <pre style="color: red">{{ $output }}</pre>
                    @else
                        <pre wire:ignore class="command-output" id="command-output"></pre>
                    @endif
                </div>
            </div>
        </div>
        <div class="modal-footer">
            <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">
                Close
            </button>
        </div>
    </div>
</div>
