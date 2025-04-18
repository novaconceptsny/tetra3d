@props([
    'route' => '#',
    'img' => null,
    'icon' => null,
    'text' => null,
    'permission' => null,
    'permissionParams' => null,
    'visible' => true
])
@php
    $permission = $permission ? explode('|', $permission) : null;
    $have_permissions = $permission == null || user()->canAny($permission, $permissionParams)
@endphp

@if($visible && $have_permissions)
    <a href="{{ $route }}" {{ $attributes->merge() }} target="_blank" data-content="{{ $text }}">
        @if($img)
            <img src="{{ $img }}" alt="menu-item" style="width: unset"/>
        @endif
        @if($icon)
            <i class="{{ $icon }}" style="font-size: 19px;"></i>
        @endif
        @if($text)
        <span>
            {{ $text }}
        </span>
        @endif
    </a>
@endif
