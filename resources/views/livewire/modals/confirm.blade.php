<div class="modal-dialog modal-lg modal-dialog-centered">
    <div class="modal-content">
        <div class="modal-body">
            <p class="mt-2">{{ $message }}</p>
            <div class="d-flex justify-content-end">
                <div>
                    @if($form)
                        <form class="d-inline" action="{{ $route }}" method="post">
                            @csrf
                            @method($method)
                            <button type="submit" class="btn btn-sm btn-danger">{{ __('Yes') }}</button>
                        </form>
                    @else
                        <a href="{{ $route }}" class="btn btn-sm btn-danger">{{ __('Yes') }}</a>
                    @endif
                    <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">{{ __('No') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
