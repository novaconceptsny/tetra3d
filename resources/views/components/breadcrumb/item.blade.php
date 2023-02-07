@props([
    'text' => null,
    'route' => '#'
])
<a class="breadcrumb-item" aria-current="page" href="{{ $route }}">
    {{ $text ?? $slot }}
</a>
