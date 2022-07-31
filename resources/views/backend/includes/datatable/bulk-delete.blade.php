@if($bulkDeleteEnabled && $selectedRows)
    <button class="btn btn-sm btn-danger" data-toggle="modal" data-target="#confirm_bulk_delete">
        <i class="fal fa-trash"></i> {{ __('Bulk Delete') }}
    </button>
    <div class="modal fade border-0" id="confirm_bulk_delete">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-body">
                    <p class="text-center mt-2">{{ __('Are you sure you want to perform this action ?') }}</p>
                    <div class="float-right">
                        <button wire:click="deleteSelectedRows" class="btn btn-sm btn-danger" data-dismiss="modal">{{ __('Yes') }}</button>
                        <button type="button" class="btn btn-sm btn-primary" data-dismiss="modal">{{ __('No') }}
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endif
