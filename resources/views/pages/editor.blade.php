@extends('layouts.redesign')

@section('menu')
    @php
        $query_params = array_merge(['tour' => $spot->tour_id], request()->all());
    @endphp
    <x-menu>
        <x-menu-item text="Versions" icon="fal fa-clone" :route="route('tours.surfaces', $query_params)"/>
        <x-menu-item
            text="360 View" icon="fal fa-vr-cardboard"
            :route="route('tours.show', $query_params)"
        />
        <x-menu-item text="Map" icon="fal fa-map-marked-alt" data-bs-toggle="modal" data-bs-target="#tourMapModal"/>
        <x-menu-item
            target="_self"
            wire:modal="modals.share-tour, @js(['tourId' => $tour->id, 'layoutId' => $layout->id, 'spotId' => request('spot_id')])"
            text="Share" icon="fal fa-share-nodes"
        />
        <x-menu-item text="Artwork Collection" icon="fal fa-palette" :route="route('artworks.index')"/>
    </x-menu>
@endsection

@section('breadcrumbs')
    <x-breadcrumb.breadcrumb>
        <x-breadcrumb.item :text="$project ? $project->name : 'No Project'"/>
        <x-breadcrumb.separtator/>

        <x-breadcrumb.item :text="$tour?->name"/>
        <x-breadcrumb.separtator/>

        <x-breadcrumb.item :text="$spot->name"/>
        <x-breadcrumb.separtator/>

        <x-breadcrumb.item>
            @if($current_surface_state)
                <livewire:editable-field :model="$current_surface_state" field="name"/>
            @else
                <span>Untitled</span>
            @endif
        </x-breadcrumb.item>

    </x-breadcrumb.breadcrumb>
@endsection

{{--<div class="alert alert-danger fade slow w-100 row hide" style="position: absolute; z-index: 200; left: 0.8vw;"
     role="alert" id="error_alert">
    <strong>Cannot save! &nbsp</strong>Overlap detected on canvas between 2 or more images.
</div>--}}


@section('content')
    <section class="editor">
        <div class="container-fluid editor-view ">
            @php($sidebar = request('sidebar', 'editor'))
            <div class="row" x-data="{sidebar: '{{$sidebar}}' }">
                <livewire:comments :commentable="$current_surface_state"/>
                <livewire:artwork-collection :project="$project" />

                @php($canvasId = $current_surface_state ? $current_surface_state->id : 'new')
                <div class="col-9 main-col" x-data="{ activeCanvas: @js("artwork_canvas_$canvasId") }">
                    <x-editor-actions :surface-id="$surface->id" :layout-id="$layout->id"/>

                    <div class="d-flex w-full">
                        @foreach($canvases as $canvas)
                            <div class="btn btn-light tab"
                                 :class="activeCanvas === @js($canvas['canvasId']) ? 'active' : ''"
                                 @click="activeCanvas = @js($canvas['canvasId']); $dispatch('canvasChanged', { surfaceStateId: @js($canvas['surfaceStateId']) })">
                                {{ $canvas['surfaceStateName'] }}
                            </div>
                        @endforeach
                        <div class="h-full d-flex justify-content-center align-items-center px-2 bg-gray-100">
                            <i class="fas fa-plus btn text-black"></i>
                        </div>
                    </div>

                    @foreach($canvases as $canvas)
                        <div x-show="activeCanvas === @js($canvas['canvasId'])" class="main_content w-100"
                             style="overflow: hidden; height: calc(100% - 38px)">
                            <canvas id="{{ $canvas['canvasId'] }}"></canvas>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    </section>

    <div class="modal fade" id="confirmation_modal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Save Canvas</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <p>Save artwork assignment as:</p>
                    <form>
                        <div class="form-row">
                            <input type="text" class="form-control ml-2 mr-2" placeholder="assignment1" id="file_name">
                            <div class="invalid-feedback ml-3">
                                Invalid file name provided.
                            </div>
                        </div>
                    </form>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-outline-secondary" id="confirm_save_btn">Save Changes
                    </button>
                </div>
            </div>
        </div>
    </div>

    <x-editor.crop-button/>
@endsection

@section('scripts')
    <script>
        let canvases = @json($canvases);
    </script>
    <script type="text/javascript" src="{{ asset('js/fabric.min.js') }}"></script>
    <script type="module" src="{{ asset('canvas/canvas.js') }}"></script>

@endsection
