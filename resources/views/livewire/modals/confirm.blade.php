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
                            <button type="submit" {!! $confirmBtnAttributes !!} class="btn btn-sm btn-danger c-btn-primary rounded-0">{{ __('Yes') }}</button>
                        </form>
                    @else
                        @if($button)
                            <button type="button" {!! $confirmBtnAttributes !!} class="btn btn-sm btn-danger c-btn-primary rounded-0">{{ __('Yes') }}</button>
                        @else
                            <a href="{{ $route ?? '#' }}" {!! $confirmBtnAttributes !!} class="btn btn-sm btn-danger c-btn-primary rounded-0">{{ __('Yes') }}</a>
                        @endif
                    @endif
                    <button type="button" class="btn btn-sm btn-primary c-btn-primary ms-1" data-bs-dismiss="modal">{{ __('No') }}</button>
                </div>
            </div>
        </div>
    </div>
</div>
