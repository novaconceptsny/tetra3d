@extends('layouts.redesign')

@section('menu')
    <x-menu>
        <x-menu-item
            onclick="window.livewire.emit('showModal', 'modals.share-tour', '{{ $tour->id }}', '{{ $project->id }}', '{{ request('spot_id') }}')"
            text="Share" icon="fal fa-share-nodes"
        />
        <x-menu-item text="360 View" icon="fal fa-vr-cardboard" :route="route('tours.show', array_merge(request()->all(), ['tour' => $tour]))"/>
        <x-menu-item text="Artwork Collection" icon="fal fa-palette" :route="route('artworks.index')"/>
    </x-menu>
@endsection

@section('content')
    <section class="version">
        <div class="container-fluid version-wrapper">
            <livewire:surface.index :project="$project" :tour="$tour"/>
        </div>
    </section>
@endsection
