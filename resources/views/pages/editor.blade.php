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
                            <!-- <div class="tab" :class="activeCanvas === @js($canvas['canvasId']) ? 'active' : ''" x-data="{
                            hasChanges: false,
                            surfaceStateId: @js($canvas['surfaceStateId']),

                            init(){
                              document.addEventListener('onCanvasUpdated', (e) => {
                                if (e.detail.surfaceStateId === this.surfaceStateId && this.hasChanges === false){
                                  this.hasChanges = true;
                                  this.changedCanvases++
                                }
                              })
                            }
                           }"
                                @click="activeCanvas = @js($canvas['canvasId']); $dispatch('canvasChanged', { surfaceStateId: @js($canvas['surfaceStateId']) })">
                                <div> -->

                                    <!-- <span> -->
                                        <!-- <i x-cloak x-show="hasChanges" class="fa fa-circle fa-xs text-warning change-icon"></i> -->
                                        <!-- <span class="surface-name">{{ $canvas['surfaceStateName'] }}</span> -->
                                    <!-- </span> -->
                                    
                                    <!-- @if($canvas['surfaceStateId'])
                              <a href="{{ route('surfaces.active', $canvas['surfaceStateId']) }}"
                                 class="surface-active">
                                <i class="fa-regular {{ $canvas['surfaceStateId'] == $currentSurfaceStateId ? ' fa-circle-check' : ' fa-circle' }}"></i>
                              </a>
                            @endif
                            @if($canvas['surfaceStateId'])
                              <form class="d-inline" method="post"
                                  action="{{ route('surfaces.destroy', $canvas['surfaceStateId']) }}">
                                @method('delete')
                                @csrf
                                <button class="cross-btn"
                                    onclick="return confirm('Are you sure you want to delete this version?');"
                                    style="line-height: 0">
                                  {{--                                                <i class="fas fa-trash"></i>--}}
                                  <svg width="15" height="15" viewBox="0 0 15 15" fill="none"
                                     xmlns="http://www.w3.org/2000/svg">
                                    <path d="M1.875 3.75H3.125H13.125" stroke="black" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"/>
                                    <path
                                      d="M5 3.75V2.5C5 2.16848 5.1317 1.85054 5.36612 1.61612C5.60054 1.3817 5.91848 1.25 6.25 1.25H8.75C9.08152 1.25 9.39946 1.3817 9.63388 1.61612C9.8683 1.85054 10 2.16848 10 2.5V3.75M11.875 3.75V12.5C11.875 12.8315 11.7433 13.1495 11.5089 13.3839C11.2745 13.6183 10.9565 13.75 10.625 13.75H4.375C4.04348 13.75 3.72554 13.6183 3.49112 13.3839C3.2567 13.1495 3.125 12.8315 3.125 12.5V3.75H11.875Z"
                                      stroke="black" stroke-width="1.5" stroke-linecap="round"
                                      stroke-linejoin="round"/>
                                    <path d="M6.25 6.875V10.625" stroke="black" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"/>
                                    <path d="M8.75 6.875V10.625" stroke="black" stroke-width="1.5"
                                        stroke-linecap="round" stroke-linejoin="round"/>
                                  </svg>

                                </button>
                              </form>
                            @endif -->
                                <!-- </div>
                            </div> -->
                    @endforeach

                    <!-- @if(!request('new'))
                            <a href="{{ route('surfaces.show', [$surface->id, 'layout_id' => $layout->id, 'new' => 1]) }}"
                               class="h-full d-flex justify-content-center align-items-center px-2 bg-transparent text-decoration-none">
                                <i class="fas fa-plus btn"></i>
                            </a>
                        @endif -->

                    <x-menu-item text="Return to 360 View" class="view-360" :img="asset('redesign/images/360-icon.png')"
                        target="_self" id="return_to_360" :route="route('tours.show', array_merge(request()->all(), ['tour' => $tour]))" />

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