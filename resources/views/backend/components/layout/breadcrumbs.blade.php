@props([
    'home' => true
])

<ol class="breadcrumb m-0">
    @if($home)
        <li class="breadcrumb-item">
            <a href="{{ route('backend.projects.index') }}">{{ __('Backend') }}</a>
        </li>
    @endif
    {{ $slot }}
</ol>
