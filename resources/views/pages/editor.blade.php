@extends('layouts.redesign')

@section('outside-menu')
@php
    $query_params = array_merge(['tour' => $spot->tour_id], request()->all());
@endphp
<div class="menu-links d-flex align-items-center gap-4">
    <x-menu-item text="Current Surface" icon="fal fa-question" :route="route('tours.surfaces', $query_params)"
        data-bs-toggle="modal" data-bs-target="#mapImage" :visible="$surface->getFirstMediaUrl('layout')" />

    <x-menu-item text="List View" icon="fal fa-clone" :route="route('tours.surfaces', $query_params)" target="_self" />
    <x-menu-item text="Map" icon="fal fa-map-marked-alt" data-bs-toggle="modal" data-bs-target="#tourMapModal" />
    <x-menu-item target="_self" text="Share" icon="fal fa-share-nodes" :visible="$layout"
        onclick="Livewire.dispatch('modal.open', {component: 'modals.share-tour', arguments: {'layout': {{ request('layout_id') }} }})" />
    <x-menu-item text="Artwork Collection" icon="fal fa-palette" :route="route('artworks.index')" />
</div>
@endsection

@section('breadcrumbs')
<x-breadcrumb.breadcrumb>
    <x-breadcrumb.item :text="$project ? $project->name : 'No Project'" />
    <x-breadcrumb.separtator />

    <x-breadcrumb.item :text="$layout?->name" />
    <x-breadcrumb.separtator />

    <x-breadcrumb.item :text="$spot->name" />


</x-breadcrumb.breadcrumb>
@endsection

@section('content')
<section class="editor">
    <div class="container-fluid editor-view ">

        @php($sidebar = request('sidebar', 'editor'))
        <div class="row" x-data="{sidebar: @js($sidebar) }">
            <livewire:comments :commentable="$selectedSurfaceState" />
            <livewire:artwork-collection :project="$project" />

            @php($canvasId = $selectedSurfaceState ? $selectedSurfaceState->id : 'new')
            <div class="col-9 main-col position-relative"
                x-data="{ changedCanvases: 0, activeCanvas: @js("artwork_canvas_$canvasId") }">
                <x-editor-actions />
                <div x-cloak style="position: absolute; inset: auto 10px 0 auto; z-index: 10; background:#ffc107 "
                    class="alert alert-warning alert-dismissible fade show rounded-0 border-0"
                    x-show="changedCanvases > 1">
                    <i class="fal fa-exclamation-triangle"></i> Multiple canvases have unsaved changes. <br>
                    Updating one canvas at a time will discard changes on others.
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>

                <div class="d-inline-flex tabs-container mb-1 pe-2">
                    @foreach($canvases as $canvas)
                    @endforeach

                    <div class="d-flex align-items-center gap-2">
                        <button class="btn btn-outline-secondary btn-sm white-bg-button" id="add-horz-guide">
                            <i class="fal fa-arrows-alt-h"></i> Add Horizontal Guide
                        </button>
                        <button class="btn btn-outline-secondary btn-sm white-bg-button" id="add-vert-guide">
                            <i class="fal fa-arrows-alt-v"></i> Add Vertical Guide
                        </button>
                        <button class="btn btn-outline-secondary btn-sm white-bg-button" id="toggle-guides" data-hidden="false">
                            <i class="fal fa-eye"></i> Hide Guides
                        </button>

                        <x-menu-item text="Return to 360 View" class="view-360" :img="asset('redesign/images/360-icon.png')"
                            target="_self" id="return_to_360" :route="route('tours.show', array_merge(request()->all(), ['tour' => $tour]))" />
                    </div>
                </div>

                @foreach($canvases as $canvas)
                    <div x-show="activeCanvas === @js($canvas['canvasId'])" class="main_content w-100"
                        style="overflow: hidden; height: calc(100% - 52px)">
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
                <p>Save art arrangement as:</p>
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

<x-editor.crop-button />

<div class="modal fade" id="mapImage" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
        <div class="modal-content">
            <div class="modal-header">
                <h5>Current Wall</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body text-center">
                <img src="{{ $surface->getFirstMediaUrl('layout') }}">
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    let selectedSurfaceStateId = @js($selectedSurfaceState?->id);
    let canvases = @json($canvases);
</script>
<script type="text/javascript" src="{{ asset('js/fabric.min.js') }}"></script>
<script type="module" src="{{ asset('canvas/canvas.js') }}"></script>

@endsection

<style>
.white-bg-button {
    background-color: white !important;
    color: black !important;
    height: 36px !important;
    font-size: 14px !important;
    font-style: normal !important;
    font-weight: var(--light-font) !important;
    line-height: normal !important;
    text-decoration: none !important;
    font-family: var(--main-font-family) !important;
    box-shadow: none !important;
    border: 1px solid #000000 !important;
    border-radius: 0px !important;
    padding: 4px 8px !important;
}

.white-bg-button:hover {
    background-color: black !important;
    color: white !important;
    border-color: black !important;
}
</style>