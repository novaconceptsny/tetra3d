@props([
    'id',
    'name' => '',
    'value' => '',
    'label' => null,
    'field',
    'placeholder',
    'col' => 'col-12',
    'optional' => true,
    'multiple' => false
])

@php
    $id = str($id ?? $name)->replace('.', '_');
    $label = $label ?? str_to_title($name);;
    $field = $field ?? $name;
    $name = dotToHtmlArray($name);
    $placeholder = $placeholder ?? "Select $label";

    if ($multiple){
        $name .= '[]';
    }
@endphp

<div class="{{ $col }}">
    <label class="form-label" for="{{$id}}">{{ __($label) }}</label>
    <select class="form-control select2 @error($field) is-invalid @enderror "
            data-toggle="select2"
            multiple="{{ $multiple }}"
            name="{{$name}}" id="{{$id}}"
            {{ $attributes->merge() }}>
        @if($placeholder)
            <option value="">{{ __($placeholder) }}</option>
        @endif
        {{ $slot }}
    </select>
    <x-backend::error field="{{$field}}"/>
</div>
