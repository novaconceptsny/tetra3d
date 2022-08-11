@extends('layouts.backend')

@section('title_right')
    <x-backend::layout.breadcrumbs>
        <x-backend::layout.breadcrumb-item text="Artworks" :route="route('backend.artworks.index')" />
        <x-backend::layout.breadcrumb-item text="Form" :active="true" />
    </x-backend::layout.breadcrumbs>
@endsection

@section('content')
    @php($artwork = $artwork ?? null)
    @php($edit_mode = (bool)$artwork)
    @php($heading = $heading ?? ( $artwork ? __('Edit Artwork') : __('Add New Artwork') ))

    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">{{ $heading }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ $route }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method($method ?? 'POST')

                <div class="row g-3">
                    <x-backend::inputs.select col="col-6" name="artwork_collection_id" label="Collection">
                        @foreach($artwork_collections as $collection)
                            <x-backend::inputs.select-option
                                :value="$collection->id" :text="$collection->name"
                                :selected="$artwork?->artwork_collection_id"
                            />
                        @endforeach
                    </x-backend::inputs.select>

                    <x-backend::inputs.text col="col-6" name="name" value="{!! $artwork?->name !!}"/>
                    <x-backend::inputs.text col="col-6" name="artist" value="{{ $artwork?->artist }}"/>
                    <x-backend::inputs.text col="col-6" name="type" value="{{ $artwork?->type }}"/>
                    <x-backend::inputs.text col="col-4" name="data.scale" value="{{ $artwork?->data->scale }}"/>
                    <x-backend::inputs.text col="col-4" name="data.width_inch" value="{{ $artwork?->data->width_inch }}"/>
                    <x-backend::inputs.text col="col-4" name="data.height_inch" value="{{ $artwork?->data->height_inch }}"/>
                    <div class="col-12">
                        <h5>{{ __('Artwork') }}</h5>
                        <x-backend::media-attachment
                            name="image" rules="max:102400"
                            :media="$artwork?->getFirstMedia('image')"
                        />
                    </div>

                    <div class="col-12 text-end">
                        <button class="btn btn-primary" type="submit">
                            {{ $submit_text ?? ( $edit_mode ? __('Update') : __('Create') ) }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('backend/css/media-library.css') }}">
@endsection

