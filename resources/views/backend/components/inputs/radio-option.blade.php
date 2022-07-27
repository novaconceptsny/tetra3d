@props([
    'id',
    'name' => '',
    'field' => '',
    'selected' => null,
    'value' => '',
    'label' => '',
])

@php
    $id = str($id ?? $name)->replace('.', '_');
    $id .= "_{$value}";
    $field = $field ?? htmlArrayToDot($name);
    $name = dotToHtmlArray($name);
    $is_selected = (string)$value == old($field, (string)$selected);
@endphp
<div class="form-check form-check-inline">
    <input type="radio" id="{{ $id }}" name="{{$name}}"
           class="form-check-input" value="{{ $value }}" {{ $attributes->merge() }} @checked($is_selected)>
    <label class="form-check-label" for="{{ $id }}">{{ $label }}</label>
</div>
