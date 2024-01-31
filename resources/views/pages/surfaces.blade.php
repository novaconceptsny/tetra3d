@extends('layouts.redesign')
{{--@section('outside-menu')--}}
{{--    @php--}}
{{--        $query_params = array_merge(['tour' => $spot->tour_id], request()->all());--}}
{{--    @endphp--}}
{{--    <div class="menu-links d-flex align-items-center gap-4">--}}
{{--        <x-menu-item--}}
{{--            text="Current Surface" icon="fal fa-question"--}}
{{--            :route="route('tours.surfaces', $query_params)"--}}
{{--            data-bs-toggle="modal" data-bs-target="#mapImage"--}}
{{--            :visible="$surface->getFirstMediaUrl('layout')"--}}
{{--        />--}}

{{--        <x-menu-item text="List View" icon="fal fa-clone" :route="route('tours.surfaces', $query_params)" target="_self"/>--}}
{{--        <x-menu-item--}}
{{--            text="360 View" :img="asset('redesign/images/360.svg')" target="_self"--}}
{{--            :route="route('tours.show', array_merge(request()->all(), ['tour' => $tour]))"--}}
{{--        />--}}
{{--        <x-menu-item text="Map" icon="fal fa-map-marked-alt" data-bs-toggle="modal" data-bs-target="#tourMapModal"/>--}}
{{--        <x-menu-item--}}
{{--            target="_self" text="Share" icon="fal fa-share-nodes" :visible="$layout"--}}
{{--            onclick="Livewire.dispatch('modal.open', {component: 'modals.share-tour', arguments: {'layout': {{ request('layout_id') }} }})"--}}
{{--        />--}}
{{--        <x-menu-item text="Artwork Collection" icon="fal fa-palette" :route="route('artworks.index')"/>--}}
{{--    </div>--}}
{{--@endsection--}}

@section('outside-menu')
    <div class="menu-links d-flex align-items-center gap-4">
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
    </div>
@endsection

@section('content')
    <section class="version">
        <div class="container-fluid version-wrapper">
            <livewire:surface.index :layout="$layout" :tour="$tour"/>
        </div>
    </section>
@endsection
