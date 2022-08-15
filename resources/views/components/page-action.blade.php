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
<li {{ $attributes->class(['nav__item']) }}>
    @if($type == 'button')
        <button {{ $attributes->merge(['class' => 'nav__link']) }}>
            @if($icon)
                <i class="{{ $icon }}" style="line-height: inherit"></i>
            @endif
            {{ $text }}
        </button>
    @else
        <a href="{{ $url }}"  {{ $attributes->merge(['class' => 'nav__link']) }}>
            @if($icon)
                <i class="{{ $icon }}" style="line-height: inherit"></i>
            @endif
            {{ $text }}
        </a>
    @endif
</li>
@endif
