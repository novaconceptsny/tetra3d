@extends('layouts.backend')

@section('title_right')
    <x-backend::layout.breadcrumbs>
        <x-backend::layout.breadcrumb-item text="Tours" :route="route('backend.tours.index')"/>
        <x-backend::layout.breadcrumb-item text="Form" :active="true"/>
    </x-backend::layout.breadcrumbs>
@endsection

@section('content')
    @php($tour = $tour ?? null)
    @php($edit_mode = (bool)$tour)
    @php($heading = $heading ?? ( $tour ? __('Edit Tour') : __('Add New Tour') ))

    <div class="card">
        <div class="card-header">
            <h5 class="mb-0">{{ $heading }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ $route }}" method="POST">
                @csrf
                @method($method ?? 'POST')

                <x-backend::error-alert />
                <x-backend::tab :padding-x="0">
                    <x-slot name="tabs">
                        <x-backend::tab.item label="Tour" id="tour_form" :active="true"/>
                        <x-backend::tab.item label="Map" id="map_form"/>
                    </x-slot>
                    <x-backend::tab.content id="tour_form" :active="true">
                        <div class="row g-3">
                            <x-backend::inputs.text name="name" value="{{ $tour?->name }}"/>
                        </div>
                    </x-backend::tab.content>
                    <x-backend::tab.content id="map_form">
                        <div class="row g-3">
                            <x-backend::inputs.text name="map.name" value="{{ $tour?->map?->name }}" />
                            <x-backend::inputs.text col="col-6" name="map.width" value="{{ $tour?->map?->width }}" />
                            <x-backend::inputs.text col="col-6" name="map.height" value="{{ $tour?->map?->height }}" />

                            <div class="col-12">
                                <h5>{{ __('Map Image') }}</h5>

                                <x-backend::media-attachment
                                    name="map_image" rules="max:102400"
                                    :media="$tour?->map?->getFirstMedia('image')"
                                />
                            </div>
                        </div>
                        @if($tour?->map)
                            <div class="row g-3 mt-2">
                                <div class="col-12"><h5>{{ __('Spots') }}</h5></div>
                                @foreach($tour->spots as $spot)
                                    @php($map = $spot->maps?->first()?->pivot)
                                    <input
                                        type="hidden" name='{{ dotToHtmlArray("map.spots.{$spot->id}.id") }}'
                                        value="{{ $spot->id }}"
                                    >

                                    <div class="row g-2">
                                        <x-backend::inputs.text
                                            col="col-6" name='{{ "map.spots.{$spot->id}.x" }}'
                                            :value="$map?->x" label='{{ "{$spot->friendly_name} X" }}'
                                        />
                                        <x-backend::inputs.text
                                            col="col-6" name='{{ "map.spots.{$spot->id}.y" }}'
                                            :value="$map?->y" label='{{ "{$spot->friendly_name} Y" }}'
                                        />
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </x-backend::tab.content>
                </x-backend::tab>
                <div class="text-end">
                    <button class="btn btn-primary" type="submit">
                        {{ $submit_text ?? ( $tour ? __('Update') : __('Create') ) }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

