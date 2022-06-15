@props([
    'id',
    'name' => '',
    'value' => '',
    'label' => '',
    'placeholder' => '',
])

@php
    $id = \Livewire\str($id ?? $name)->replace('.', '_');
    $id .= "_{$value}";
    $name = dotToHtmlArray($name);
@endphp
<div class="form-check form-check-inline">
    <input type="radio" id="{{ $id }}" name="{{$name}}"
           class="form-check-input" value="{{ $value }}" {{ $attributes->merge() }}>
    <label class="form-check-label" for="{{ $id }}">{{ $label }}</label>
</div>
