@props([
    'title' => '',
    'url' => '#'
])
<li {{ $attributes->class(['nav__item']) }}>
    <a href="{{ $url }}" class="nav__link" {{ $attributes->merge() }}>{{ $title }}</a>
</li>
