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

                @if(session()->get('remove_projects_alert'))
                    <div class="alert alert-danger">
                        <strong>Warning!</strong>
                        <span>This tour is currently being used in the following projects:</span>
                        <ul class="my-1">
                            @foreach($tour->projects as $project)
                                <li>{{ $project->name }}</li>
                            @endforeach
                        </ul>
                        <span>Unlink all projects from this tour before changing the company</span>
                    </div>
                @endif

                <x-backend::tab :padding-x="0">
                    <x-slot name="tabs">
                        <x-backend::tab.item label="Tour" id="tour_form" :active="true"/>
                        @if($tour)
                            <x-backend::tab.item label="Maps" id="map_forms"/>
                            <x-backend::tab.item label="Model" id="model_forms"/>
                        @endif
                    </x-slot>
                    <x-backend::tab.content id="tour_form" :active="true">
                        <div class="row g-3">
                            <x-backend::inputs.company :value="$tour?->company_id"/>
                            <x-backend::inputs.text name="name" value="{{ $tour?->name }}"/>
                            <div class="col-12">
                                <h5>{{ __('Thumbnail') }}</h5>
                                <x-backend::media-attachment
                                    name="thumbnail" rules="max:102400"
                                    :media="$tour?->getFirstMedia('thumbnail')"
                                />
                            </div>
                            <div class="text-end">
                                <button class="btn btn-primary" type="submit">
                                    {{ $submit_text ?? ( $tour ? __('Update') : __('Create') ) }}
                                </button>
                            </div>
                        </div>
                    </x-backend::tab.content>
                    @if($tour)
                        <x-backend::tab.content id="map_forms">
                            <livewire:map.index :tour="$tour"/>
                        </x-backend::tab.content>
                    @endif
                    @if($tour)
                        <x-backend::tab.content id="model_forms">
                            <livewire:model.index :tour="$tour"/>
                        </x-backend::tab.content>
                    @endif
                </x-backend::tab>
            </form>
        </div>
    </div>
@endsection

