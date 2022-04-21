@props([
    'title' => '',
    'action' => '#'
])
<li {{ $attributes->class(['nav__item']) }}>
    <a href="{{ $action }}" class="nav__link" {{ $attributes->merge() }}>{{ $title }}</a>
</li>
