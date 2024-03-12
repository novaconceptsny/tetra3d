@extends('layouts.redesign')

@section('menu')
    <x-menu>
        <x-menu-item
            text="360 View" :img="asset('redesign/images/360.svg')" target="_self"
            :route="route('tours.show', array_merge(request()->all(), ['tour' => $tour]))"
        />
        <x-menu-item text="Map" icon="fal fa-map-marked-alt" data-bs-toggle="modal" data-bs-target="#tourMapModal"/>
        <x-menu-item
            onclick="Livewire.dispatch('modal.open', {component: 'modals.share-tour', arguments: {'layout': {{ request('layout_id') }} }})"
            text="Share" icon="fal fa-share-nodes" :visible="$layout" target="_self"
        />
        <x-menu-item text="Artwork Collection" icon="fal fa-palette" :route="route('artworks.index')"/>
    </x-menu>
@endsection

@section('content')
    <section class="version">
        <div class="container-fluid version-wrapper">
            <livewire:surface.index :layout="$layout" :tour="$tour"/>
        </div>
    </section>
@endsection
