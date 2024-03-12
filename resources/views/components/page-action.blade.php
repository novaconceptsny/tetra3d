@props([
    'text' => '',
    'url' => '#',
    'icon' => '',
    'type' => 'link',
    'permission' => null,
    'permissionParams' => null,
    'visible' => true
])

@php
    $permission = $permission ? explode('|', $permission) : null;
    $have_permissions = $permission == null || user()->canAny($permission, $permissionParams)
@endphp

@if($visible && $have_permissions)
    <div class="nav-left-btn">
        @if($type == 'button')

            <button {{ $attributes->merge(['class' => 'menu-btn me-1']) }}>
                @if($icon)
                    <i class="{{ $icon }}" style="line-height: inherit"></i>
                @endif
                {{ $text }}
            </button>
        @else
            <a href="{{ $url }}" {{ $attributes->merge(['class' => 'menu-btn me-1']) }}>
                @if($icon)
                    <i class="{{ $icon }}" style="line-height: inherit"></i>
                @endif
                {{ $text }}
            </a>
        @endif
    </div>
@endif
