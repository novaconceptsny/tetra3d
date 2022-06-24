@props([
    'text' => '',
    'url' => '#',
    'icon' => ''
])
<li {{ $attributes->class(['nav__item']) }}>
    <a href="{{ $url }}"  {{ $attributes->merge(['class' => 'nav__link']) }}>
        <i class="{{ $icon }}" style="line-height: inherit"></i>
        {{ $text }}
    </a>
</li>
