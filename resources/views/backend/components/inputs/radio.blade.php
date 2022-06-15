@props([
    'label' => '',
    'field' => null
])

<div {{ $attributes->merge() }}>
    <label class="form-label d-block">{{ __($label) }}</label>
    <div class="@error($field) is-invalid @enderror">
        {{ $slot }}
    </div>
    <x-backend::error field="{{$field}}"/>
</div>
