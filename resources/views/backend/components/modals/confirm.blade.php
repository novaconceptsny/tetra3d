@props([
    'route',
    'model',
    'label' => lcfirst(class_basename(get_class($model))),
    'button' => false,
    'form' => true,
    'action' => '',
    'message' => __('Are you sure you want to perform this action ?'),
    'method' => 'Delete',
    'permission' => null,
    'permissionParams' => null,
])

@php($action = $action ? $action.'_' : '')

@if($permission == null || user()->can($permission, $permissionParams) )
@if($button)
    <button data-toggle="modal" data-bs-target="#confirm_{{$action}}{{ $label }}_{{ $model->id }}"
            class="btn btn-sm btn-outline-danger" title="">
        <i class="fa fa-trash"></i>
    </button>
@endif

<div class="modal fade border-0" id="confirm_{{$action}}{{ $label }}_{{ $model->id }}" role="dialog">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-body">
                <p class="text-center mt-2">{{ $message }}</p>
                <div class="d-flex justify-content-end">
                    <div>
                        @if($form)
                            <form class="d-inline" action="{{ $route }}" method="post">
                                @csrf
                                @method($method)
                                <button type="submit" class="btn btn-sm btn-danger">{{ __('Yes') }}</button>
                            </form>
                        @else
                            <a href="{{ $route ?? '' }}" class="btn btn-sm btn-danger">{{ __('Yes') }}</a>
                        @endif
                        <button type="button" class="btn btn-sm btn-primary" data-bs-dismiss="modal">{{ __('No') }}</button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endif

<script>
    var route = @json($route);
    console.log("delete route", route);
    </script>
