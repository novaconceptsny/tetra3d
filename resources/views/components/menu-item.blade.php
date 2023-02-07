@props([
    'route' => '#',
    'img' => null,
    'icon' => null,
    'text' => ''
])
<a href="{{ $route }}">
    @if($img)
        <img src="{{ $img }}" alt="menu-item"/>
    @endif
    @if($icon)
        <i class="{{ $icon }}" style="font-size: 19px;"></i>
    @endif
    {{ $text }}
</a>
