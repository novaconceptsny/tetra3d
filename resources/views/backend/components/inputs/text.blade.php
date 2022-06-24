@props([
    'id',
    'name' => '',
    'value' => null,
    'label' => null,
    'type' => 'text',
    'placeholder',
    'field',
    'col' => 'col-12'
])

@php
    $id = str($id ?? $name)->replace('.', '_');
    $label = $label ?? str_to_title($name);
    $field = $field ?? $name;
    $name = dotToHtmlArray($name);
    $placeholder = $placeholder ?? $label;
@endphp
<div class="{{ $col }}">
    <label class="form-label" for="{{$id}}">{{ __($label) }}</label>
    <input class="form-control @error($field) is-invalid @enderror" name="{{$name}}"
           placeholder="{{ __($label) }}"
           id="{{$id}}" type="{{$type}}" value="{{ old($field, $value ?? '') }}"
        {{ $attributes->merge() }}>
    <x-backend::error field="{{$field}}"/>
</div>
