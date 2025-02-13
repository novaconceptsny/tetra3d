@extends('layouts.backend')

@section('title_right')
<x-backend::layout.breadcrumbs>
    <x-backend::layout.breadcrumb-item text="Artworks" :route="route('backend.artworks.index')" />
    <x-backend::layout.breadcrumb-item text="Form" :active="true" />
</x-backend::layout.breadcrumbs>
@endsection

@section('content')
@php($artwork = $artwork ?? null)
@php($edit_mode = (bool) $artwork)
@php($heading = $heading ?? ($artwork ? __('Edit Artwork') : __('Add New Artwork')))

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
                        <x-backend::inputs.select-option :value="$collection->id" :text="$collection->name"
                            :selected="$artwork?->artwork_collection_id" />
                    @endforeach
                </x-backend::inputs.select>

                <x-backend::inputs.text col="col-6" name="name" value="{!! $artwork?->name !!}" label="Title" />
                <x-backend::inputs.text col="col-6" name="artist" value="{{ $artwork?->artist }}" />
                <x-backend::inputs.text col="col-6" name="type" value="{{ $artwork?->type }}" />
                <x-backend::inputs.text col="col-6" name="data.height_inch" value="{{ $artwork?->data->height_inch }}"
                    label="Height" />
                <x-backend::inputs.text col="col-6" name="data.width_inch" value="{{ $artwork?->data->width_inch }}"
                    label="Width" />
                <div class="col-12">
                    <h5>{{ __('Artwork') }}</h5>
                    <x-backend::media-attachment name="image" rules="max:20480"
                        :media="$artwork?->getFirstMedia('image')" />
                </div>

                <div class="col-12 text-end">
                    <button class="btn btn-primary" type="submit">
                        {{ $submit_text ?? ($edit_mode ? __('Update') : __('Create')) }}
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

@section('scripts')
<script>
    function previewImage(input) {
        const previewContainer = input.closest('.d-flex').querySelector('.media-library-thumb');
        const file = input.files[0];
        
        if (file) {
            // Create preview container if it doesn't exist
            if (!previewContainer) {
                const newPreview = document.createElement('div');
                newPreview.className = 'media-library-thumb m-0 me-2';
                input.closest('.d-flex').prepend(newPreview);
            }
            
            const container = previewContainer || input.closest('.d-flex').querySelector('.media-library-thumb');
            
            // Clear existing content
            container.innerHTML = '';
            
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.className = 'media-library-thumb-img';
                img.style.objectFit = 'fill';
                img.src = URL.createObjectURL(file);
                img.alt = file.name;
                container.appendChild(img);
                
                const span = document.createElement('span');
                span.className = 'fs-6';
                span.style.whiteSpace = 'nowrap';
                span.textContent = file.name;
                container.appendChild(span);
            }
        }
    }

    // Add event listeners for file inputs
    document.addEventListener('DOMContentLoaded', function() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', function() {
                previewImage(this);
            });
        });
    });
</script>
@endsection