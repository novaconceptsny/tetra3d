@props([
    'permission' => null,
    'permissionParams' => null,
])

@php
    $permission = $permission ? explode('|', $permission) : null;
@endphp

@if($permission == null || user()->canAny($permission, $permissionParams) )
<div class="dropdown">
    <a href="#" class="dropdown-toggle arrow-none card-drop" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="far fa-ellipsis-v"></i>
    </a>
    <div class="dropdown-menu dropdown-menu-end py-0 z-10"
         aria-labelledby="order-dropdown-0">
        {{ $slot }}
    </div>
</div>
@endif
