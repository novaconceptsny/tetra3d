@extends('layouts.backend')

@section('title_right')
    <x-backend::layout.breadcrumbs>
        <x-backend::layout.breadcrumb-item text="Tours" :route="route('backend.tours.index')" />
        <x-backend::layout.breadcrumb-item text="Spots" :route="route('backend.tours.spots.index', $spot->tour)" />
        <x-backend::layout.breadcrumb-item :text="$spot->name"  />
        <x-backend::layout.breadcrumb-item text="Configuration" :active="true" />
    </x-backend::layout.breadcrumbs>
@endsection

@section('content')
    <div class="card shadow-none bg-body">
        <div class="card-header bg-body">
            <div class="float-end">
                <a href="{{ route('backend.spot-configuration.show', $spot) }}" class="btn btn-sm btn-outline-primary"
                   target="_blank">
                    <i class="fal fa-code"></i> {{ __('Show Configuration') }}
                </a>
            </div>
            <h5 class="mb-0">{{ __('Configure Spot '). "'$spot->name'" }}</h5>
        </div>
        <div class="card-body">
            <livewire:xml-form :spot="$spot"/>
        </div>
    </div>
@endsection
