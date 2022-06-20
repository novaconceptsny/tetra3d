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

    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">{{ $heading }}</h5>
        </div>
        <div class="card-body">

            <form class="row g-3" action="{{ $route }}" method="POST">
                @csrf
                @method($method ?? 'POST')

                <x-backend::inputs.text name="name" value="{{ $tour ? $tour->name : '' }}"/>

                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit">
                        {{ $submit_text ?? ( $tour ? __('Update') : __('Create') ) }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

