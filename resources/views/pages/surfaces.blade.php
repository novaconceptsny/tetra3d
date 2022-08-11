@extends('layouts.master')

@section('page_actions')
    <x-page-action text="Return to 360 view" :url="route('tours.show', [$tour, 'project_id' => $project->id])"/>
@endsection

@section('content')
    <div class="dashboard gallery mini">
        @foreach($surfaces as $surface)
            <livewire:surface.surface-row :project-id="$project->id" :surface="$surface" wire:key="{{$surface->id}}"/>
        @endforeach
    </div>
@endsection
