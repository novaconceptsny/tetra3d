@props([
    'id',
    'label' => 'Enable',
    'name' => 'switch',
    'checked' => false,
    'value' => 1,
    'col' => 'col-12',
    'inline' => true,
])

@php
    $id = str($id ?? $name)->replace('.', '_');
    $name = dotToHtmlArray($name);
@endphp

<div class="{{ $inline ? 'd-inline-block' : $col }}">
    <div class="form-check form-switch form-select-lg {{ $inline ? 'd-inline-block' : '' }}">
        <label class="form-check-label" for="{{ $id }}">{{ $label }}</label>
        <input type="checkbox" class="form-check-input" id="{{ $id }}" name="{{$name}}" @checked($checked) value="{{ $value }}" >
    </div>
</div>

{{--<div class="{{$col}}">
    <input type="checkbox" id="{{ $id }}" name="{{$name}}" @checked($checked) data-switch="bool"/>
    <label for="{{ $id }}" data-on-label="{{ $labelOn }}" data-off-label="{{ $labelOff }}"></label>
</div>--}}
