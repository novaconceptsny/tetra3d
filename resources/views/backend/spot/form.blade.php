@extends('layouts.backend')

@section('title_right')
    <x-backend::layout.breadcrumbs>
        <x-backend::layout.breadcrumb-item text="Tours" :route="route('backend.tours.index')" />
        <x-backend::layout.breadcrumb-item text="Spots" :route="route('backend.tours.spots.index', $tour)" />
        <x-backend::layout.breadcrumb-item text="Form" :active="true" />
    </x-backend::layout.breadcrumbs>
@endsection

@section('content')
    @php($spot = $spot ?? null)
    @php($edit_mode = (bool)$spot)
    @php($heading = $heading ?? ( $spot ? __('Edit Spot') : __('Add New Spot') ))

    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">{{ $heading }}</h5>
        </div>
        <div class="card-body">

            <form class="row g-3" action="{{ $route }}" method="POST">
                @csrf
                @method($method ?? 'POST')

                <x-backend::inputs.text name="name" value="{{ $spot?->name }}"/>

                <x-backend::inputs.select2 name="surfaces" :multiple="true" :placeholder="false">
                    @foreach($tour->surfaces as $surface)
                        <x-backend::inputs.select-option
                            :multiple="true"
                            :selected="$spot?->surfaces->pluck('id')->toArray()"
                            :value="$surface->id"
                            :text="$surface->name"
                        />
                    @endforeach
                </x-backend::inputs.select2>

                <div class="col-12">
                    <x-media-library-attachment name="image_360" rules="max:102400" />
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit">
                        {{ $submit_text ?? ( $spot ? __('Update') : __('Create') ) }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('backend/css/media-library.css') }}">
@endsection
