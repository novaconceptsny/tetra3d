@extends('layouts.backend')

@section('content')
    @php($surface = $surface ?? null)
    @php($edit_mode = (bool)$surface)
    @php($heading = $heading ?? ( $surface ? __('Edit Surface') : __('Add New Surface') ))

    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">{{ $heading }}</h5>
        </div>
        <div class="card-body">

            <form class="row g-3" action="{{ $route }}" method="POST">
                @csrf
                @method($method ?? 'POST')

                <x-backend::inputs.text name="name" value="{{ $surface ? $surface->name : '' }}"/>

                <div class="col-6">
                    <h5>{{ __('Main Image') }}</h5>
                    <x-media-library-attachment name="main" rules="max:102400"/>
                </div>

                <div class="col-6">
                    <h5>{{ __('Shared Image') }}</h5>
                    <x-media-library-attachment name="shared" rules="max:102400"/>
                </div>

                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit">
                        {{ $submit_text ?? ( $surface ? __('Update') : __('Create') ) }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('backend/css/media-library.css') }}">
@endsection

