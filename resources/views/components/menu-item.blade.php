@props([
    'route' => '#',
    'img' => null,
    'icon' => null,
    'text' => '',
    'permission' => null,
    'permissionParams' => null,
    'visible' => true
])
@php
    $permission = $permission ? explode('|', $permission) : null;
    $have_permissions = $permission == null || user()->canAny($permission, $permissionParams)
@endphp

@if($visible && $have_permissions)
<a href="{{ $route }}" {{ $attributes->merge() }}>
    @if($img)
        <img src="{{ $img }}" alt="menu-item"/>
    @endif
    @if($icon)
        <i class="{{ $icon }}" style="font-size: 19px;"></i>
    @endif
    {{ $text }}
</a>
@endif
