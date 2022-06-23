@extends('layouts.backend')

@section('title_right')
    <x-backend::layout.breadcrumbs>
        <x-backend::layout.breadcrumb-item text="Projects" :route="route('backend.projects.index')"/>
        <x-backend::layout.breadcrumb-item text="Form" :active="true"/>
    </x-backend::layout.breadcrumbs>
@endsection

@section('content')
    @php($project = $project ?? null)
    @php($edit_mode = (bool)$project)
    @php($heading = $heading ?? ( $project ? __('Edit Project') : __('Add New Project') ))

    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">{{ $heading }}</h5>
        </div>
        <div class="card-body">

            <form class="row g-3" action="{{ $route }}" method="POST">
                @csrf
                @method($method ?? 'POST')

                <x-backend::inputs.text name="name" value="{{ $project ? $project->name : '' }}"/>

                <x-backend::inputs.select2 name="tour_id" label="Tour">
                    @foreach($tours as $tour)
                        <x-backend::inputs.select-option
                            :selected="$project?->tour_id"
                            :value="$tour->id"
                            :text="$tour->name"
                        />
                    @endforeach
                </x-backend::inputs.select2>

                <x-backend::inputs.select2 name="user_ids[]" label="Users" :multiple="true">
                    @foreach($users as $user)
                        <x-backend::inputs.select-option
                            :value="$user->id"
                            :text="$user->name"
                        />
                    @endforeach
                </x-backend::inputs.select2>

                <div class="col-12 d-flex justify-content-end">
                    <button class="btn btn-primary" type="submit">
                        {{ $submit_text ?? ( $project ? __('Update') : __('Create') ) }}
                    </button>
                </div>
            </form>
        </div>
    </div>
@endsection

