<div id="livewire-modals" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false"
     wire:ignore.self class="modal fade">

    @if($alias)
        @livewire($alias, $params, key($alias))
    @else
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body">
                    Nothing to Show
                </div>
            </div>
        </div>
    @endif

</div>
