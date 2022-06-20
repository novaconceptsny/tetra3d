@props([
    'text' => '',
    'route' => 'javascript: void(0);',
    'active' => false,
])

<li class="breadcrumb-item {{ $active ? 'active' : '' }}">
    @if($active)
        {{ $text }}
    @else
        <a href="{{ $route }}">{{ $text }}</a>
    @endif
</li>
