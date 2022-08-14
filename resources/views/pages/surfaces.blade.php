@extends('layouts.master')

@section('page_actions')
    <x-page-action
        onclick="window.livewire.emit('showModal', 'modals.share-tour', '{{ $tour->id }}', '{{ $project->id }}')"
        text="Share" icon="fal fa-share-nodes"
    />

    <x-page-action text="Return to 360 view" :url="route('tours.show', array_merge(request()->all(), ['tour' => $tour]))"/>
@endsection

@section('content')
    <div class="dashboard gallery mini">
        @foreach($surfaces as $surface)
            <livewire:surface.surface-row :project-id="$project->id" :surface="$surface" wire:key="{{$surface->id}}"/>
        @endforeach
    </div>
@endsection
