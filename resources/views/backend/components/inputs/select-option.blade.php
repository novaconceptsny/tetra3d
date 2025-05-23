@props([
    'value' => '',
    'text' => '',
    'field' => '',
    'selected' => null,
    'multiple' => false,
    'value' => $value ?? $text
])

@php
    if ($multiple){
        $selected = !is_array($selected) ? array($selected) : $selected;
        $is_selected = in_array($value, old($field, $selected));
    } else {
        $is_selected = $value == old($field, $selected);
    }
@endphp

<option value="{{$value}}" @selected($is_selected)>{{ __($text) }}</option>
