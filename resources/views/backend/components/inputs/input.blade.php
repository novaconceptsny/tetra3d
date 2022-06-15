@props([
    'id',
    'name' => '',
    'value' => '',
    'label' => '',
    'type' => 'text',
    'placeholder',
    'field',
    'col' => 'col-12'
])

@php
    $id = \Livewire\str($id ?? $name)->replace('.', '_');
    $field = $field ?? $name;
    $name = dotToHtmlArray($name);
    $placeholder = $placeholder ?? $label;
@endphp
<div class="{{ $col }}">
    <label class="form-label" for="{{$id}}">{{ __($label) }}</label>
    <input class="form-control @error($field) is-invalid @enderror" name="{{$name}}"
           placeholder="{{ __($label) }}"
           id="{{$id}}" type="{{$type}}" value="{{ old($name, $value ?? '') }}"
        {{ $attributes->merge() }}>
    <x-backend::error field="{{$field}}"/>
</div>
