@extends('layouts.redesign')

@section('page_actions')
    <x-page-action
        onclick="window.livewire.emit('showModal', 'modals.share-tour', '{{ $tour->id }}', '{{ $project->id }}', '{{ request('spot_id') }}')"
        text="Share" icon="fal fa-share-nodes"
    />

    <x-page-action text="Return to 360 view" :url="route('tours.show', array_merge(request()->all(), ['tour' => $tour]))"/>
@endsection

@section('content')
    <section class="version">
        <div class="container-fluid version-wrapper">
            <livewire:surface.index :project="$project" :tour="$tour"/>
        </div>
    </section>
@endsection
