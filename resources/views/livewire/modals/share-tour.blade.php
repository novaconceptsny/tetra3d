<div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-header">
            <h5>Share Tour</h5>
        </div>
        <div class="modal-body">
            @if($spotSelectionAllowed && !$link)
                <div class="form-check form-check-inline">
                    <input class="form-check-input mt-2" type="radio" id="share_tour" value="tour" wire:model="share_type">
                    <label class="form-check-label" for="share_tour">Share whole tour</label>
                </div>
                <div class="form-check form-check-inline">
                    <input class="form-check-input mt-2" type="radio" id="share_spot" value="spot" wire:model="share_type">
                    <label class="form-check-label" for="share_spot">Share current spot only</label>
                </div>
            @endif

            @if($link && $linkCopied)
                <div class="alert alert-success alert-dismissible py-2 fade show " role="alert">
                   <span><i class="fad fa-check-circle"></i> Link copied to clipboard!</span>
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if($link)
                <div class="my-4">
                    <p class="m-0 d-flex justify-content-between clipboard-container">
                        <input class="clipboard-text form-control" value="{{ $link }}" />
                        @if(!$linkCopied)
                            <button class="btn btn-sm clipboard-copy" wire:click="$set('linkCopied', true)"><i class="fal fa-copy"></i></button>
                        @endif
                    </p>
                </div>
            @endif

            <div class="d-flex justify-content-end">
                <div>
                    @if(!$link)
                        <button type="button" class="btn btn-sm btn-secondary" wire:click="generateLink">{{ __('Generate Link') }}</button>
                    @endif
                    <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">{{ __('Close') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
