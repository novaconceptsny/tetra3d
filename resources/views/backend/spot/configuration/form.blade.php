@extends('layouts.backend')

@section('content')
    <div class="card shadow-none bg-body">
        <div class="card-header bg-body">
            <div class="float-end">
                <a href="{{ route('backend.spot-configuration.show', $spot) }}" class="btn btn-sm btn-outline-primary"
                   target="_blank">
                    <i class="fal fa-code"></i> {{ __('Show Configuration') }}
                </a>
            </div>
            <h5 class="mb-0">{{ __('Configure Spot') }}</h5>
        </div>
        <div class="card-body">
            <livewire:xml-form :spot="$spot"/>
        </div>
    </div>
@endsection
