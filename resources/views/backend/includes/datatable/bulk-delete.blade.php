@if($bulkDeleteEnabled && $selectedRows)
    <button class="btn btn-sm btn-danger" data-bs-toggle="modal" data-bs-target="#confirm_bulk_delete">
        <i class="fal fa-trash"></i> {{ __('Bulk Delete') }}
    </button>
    <div class="modal fade border-0" id="confirm_bulk_delete" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <p class=" mt-2">{{ __('Are you sure you want to perform this action ?') }}</p>
                    <div class="float-end">
                        <button wire:click="deleteSelectedRows" class="btn btn-sm btn-danger" data-bs-dismiss="modal">{{ __('Yes') }}</button>
                        <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">{{ __('No') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
