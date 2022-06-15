@props([
    'id',
    'name' => '',
    'value' => '',
    'date_format' => config('dates.defaults.date'),
    'label' => 'Select Date'
])

@php
    if ($value && $value instanceof \Carbon\Carbon){
        $value = $value->format($date_format);
    }

    if (! $value){
        $value = now()->format($date_format);
    }

    $id = $id ?? $name;
    $container = "{$id}_container";
@endphp

<div {{ $attributes->merge(['class' => 'position-relative']) }} id="{{ $container }}">
    <label class="form-label" for="{{ $id }}">{{ __($label) }}</label>
    <input class="form-control @error($name) is-invalid @enderror datepicker" id="{{ $id }}"
           value="{{ old($name, $value) }}"
           data-provide="datepicker" data-date-container="#{{ $container }}"
           data-date-format="dd-mm-yyyy" data-date-autoclose="true"
           type="text" placeholder="{{ __('Select Date') }}" name="{{ $name }}"
    />
    <x-backend::error field="{{$name}}"/>
</div>
