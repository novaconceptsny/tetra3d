@props([
    'text' => '',
    'url' => '#',
    'icon' => '',
    'type' => 'link'
])
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
