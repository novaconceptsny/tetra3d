@extends('layouts.master')

@section('page_actions')
    <x-page-action text="Return to 360 view" :url="route('tours.show', [$tour, 'project_id' => $project->id])"/>
@endsection

@section('content')
    <div class="dashboard gallery mini">
        @foreach($surfaces as $surface)
            @include('include.surface.row')
        @endforeach
    </div>
@endsection
