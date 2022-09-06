@extends('layouts.backend')

@section('title_right')
    <x-backend::layout.breadcrumbs>
        <x-backend::layout.breadcrumb-item text="Tours" :route="route('backend.tours.index')" />
        <x-backend::layout.breadcrumb-item text="Surfaces" :route="route('backend.tours.surfaces.index', $tour)" />
        <x-backend::layout.breadcrumb-item text="Form" :active="true" />
    </x-backend::layout.breadcrumbs>
@endsection

@section('content')
    @php($surface = $surface ?? null)
    @php($edit_mode = (bool)$surface)
    @php($heading = $heading ?? ( $surface ? __("Edit Surface ($surface->name)") : __('Add New Surface') ))

    <div class="card mb-3">
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
                        <x-backend::tab.item label="Surface Details" id="surface_form" :active="true"/>
                        <x-backend::tab.item label="Canvas Details" id="canvas_form"/>
                    </x-slot>
                    <x-backend::tab.content id="surface_form" :active="true">
                        <div class="row g-3">
                            <x-backend::inputs.text name="name" value="{{ $surface ? $surface->name : '' }}"/>
                            <div class="col-6">
                                <h5>{{ __('Background Image') }}</h5>
                                <x-backend::media-attachment
                                    name="background" rules="max:102400"
                                    :media="$surface?->getFirstMedia('background')"
                                />
                            </div>
                            <div class="col-6">
                                <h5>{{ __('Main Image') }}</h5>
                                <x-backend::media-attachment
                                    name="main" rules="max:102400"
                                    :media="$surface?->getFirstMedia('main')"
                                />
                            </div>
                        </div>
                    </x-backend::tab.content>
                    <x-backend::tab.content id="canvas_form">
                        <div class="row g-3">
                            <x-backend::inputs.text
                                col="col-6" name="data[bounding_box_top]" label="Bounding box top"
                                value="{{ $surface?->data->bounding_box_top }}"
                            />
                            <x-backend::inputs.text
                                col="col-6" name="data[bounding_box_left]" label="Bounding box left"
                                value="{{ $surface?->data->bounding_box_left }}"
                            />
                            <x-backend::inputs.text
                                col="col-6" name="data[bounding_box_height]" label="Bounding box height"
                                value="{{ $surface?->data->bounding_box_height }}"
                            />
                            <x-backend::inputs.text
                                col="col-6" name="data[bounding_box_width]" label="Bounding box width"
                                value="{{ $surface?->data->bounding_box_width }}"
                            />
                            <x-backend::inputs.text
                                col="col-12" name="data[actual_width_inch]" label="Actual width (in)"
                                value="{{ $surface?->data->actual_width_inch }}"
                            />
                            <x-backend::inputs.text
                                col="col-6" name="data[img_width]" label="Image width"
                                value="{{ $surface?->data->img_width }}"
                            />
                            <x-backend::inputs.text
                                col="col-6" name="data[img_height]" label="Image height"
                                value="{{ $surface?->data->img_height }}"
                            />
                        </div>
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

@section('styles')
    <link rel="stylesheet" href="{{ asset('backend/css/media-library.css') }}">
@endsection

