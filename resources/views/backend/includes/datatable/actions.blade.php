@php
    $route_prefix = $row->route_prefix ? '-' . $row->route_prefix : null
@endphp

<x-backend::dropdown.container permission="update|delete" :permission-params="$row">
    <x-backend::dropdown.item :route="route($routes['edit' . $route_prefix], $row->id)" permission="update" :permission_params="$row">
        <i class="fal fa-pen mr-1"></i> {{ __('Edit') }}
    </x-backend::dropdown.item>
    <x-backend::dropdown.item
        class="text-danger" data-bs-toggle="modal" data-bs-target="#confirm_{{$label}}_{{ $row->id }}"
        permission="delete" :permission_params="$row">
        <i class="fa fa-trash mr-1"></i> {{ __('Delete') }}
    </x-backend::dropdown.item>
</x-backend::dropdown.container>
<x-backend::modals.confirm
    :route="route($routes['delete' . $route_prefix], $row)" :model="$row"
    permission="delete" :permission_params="$row"
    :button="false">
</x-backend::modals.confirm>
