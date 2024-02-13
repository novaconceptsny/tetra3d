@extends('layouts.redesign')

@section('outside-menu')
    @php
        $query_params = array_merge(['tour' => $spot->tour_id], request()->all());
    @endphp
    <div class="menu-links d-flex align-items-center gap-4">
        <x-menu-item
            text="Current Surface" icon="fal fa-question"
            :route="route('tours.surfaces', $query_params)"
            data-bs-toggle="modal" data-bs-target="#mapImage"
            :visible="$surface->getFirstMediaUrl('layout')"
        />

        <x-menu-item text="List View" icon="fal fa-clone" :route="route('tours.surfaces', $query_params)"
                     target="_self"/>
        <x-menu-item text="Map" icon="fal fa-map-marked-alt" data-bs-toggle="modal" data-bs-target="#tourMapModal"/>
        <x-menu-item
            target="_self" text="Share" icon="fal fa-share-nodes" :visible="$layout"
            onclick="Livewire.dispatch('modal.open', {component: 'modals.share-tour', arguments: {'layout': {{ request('layout_id') }} }})"
        />
        <x-menu-item text="Artwork Collection" icon="fal fa-palette" :route="route('artworks.index')"/>
    </div>
@endsection

@section('breadcrumbs')
    <x-breadcrumb.breadcrumb>
        <x-breadcrumb.item :text="$project ? $project->name : 'No Project'"/>
        <x-breadcrumb.separtator/>

        <x-breadcrumb.item :text="$layout?->name"/>
        <x-breadcrumb.separtator/>

        <x-breadcrumb.item :text="$spot->name"/>
        <x-breadcrumb.separtator/>

        <x-breadcrumb.item>
            @if($selectedSurfaceState)
                <livewire:editable-field :model="$selectedSurfaceState" field="name"/>
            @else
                <span>Untitled</span>
            @endif
        </x-breadcrumb.item>

    </x-breadcrumb.breadcrumb>
@endsection

@section('content')
    <section class="editor">
        <div class="container-fluid editor-view ">

            @php($sidebar = request('sidebar', 'editor'))
            <div class="row" x-data="{sidebar: @js($sidebar) }">
                <livewire:comments :commentable="$selectedSurfaceState"/>
                <livewire:artwork-collection :project="$project"/>

                @php($canvasId = $selectedSurfaceState ? $selectedSurfaceState->id : 'new')
                <div class="col-9 main-col" x-data="{ activeCanvas: @js("artwork_canvas_$canvasId") }">
                    <x-editor-actions/>
                    <div class="d-inline-flex tabs-container mb-1 px-2">
                        @foreach($canvases as $canvas)
                            <div class="tab"
                                 :class="activeCanvas === @js($canvas['canvasId']) ? 'active' : ''"
                                 {{--@onCanvasUpdated.window="console.log('foo was dispatched')"--}}
                                 x-data="{
                                    hasChanges: false,
                                    surfaceStateId: @js($canvas['surfaceStateId']),

                                    init(){
                                        document.addEventListener('onCanvasUpdated', (e) => {
                                            if (e.detail.surfaceStateId == this.surfaceStateId){
                                                this.hasChanges = true;
                                            }
                                        })
                                    }
                                 }"
                                 @click="activeCanvas = @js($canvas['canvasId']); $dispatch('canvasChanged', { surfaceStateId: @js($canvas['surfaceStateId']) })">
                                <div>
                                    <span>
                                        @if($canvas['surfaceStateId'] && $canvas['surfaceStateId'] !== $currentSurfaceStateId)
                                            <a href="{{ route('surfaces.active', $canvas['surfaceStateId']) }}"
                                               class="surface-active">
                                                <i class="fal fa-right-to-bracket"></i>
                                            </a>
                                        @endif

                                        <i x-show="hasChanges" class="fa fa-circle fa-xs text-warning change-icon"></i>
                                        <span class="surface-name">{{ $canvas['surfaceStateName'] }}</span>
                                    </span>

                                    @if($canvas['surfaceStateId'])
                                        <form class="d-inline" method="post"
                                              action="{{ route('surfaces.destroy', $canvas['surfaceStateId']) }}">
                                            @method('delete')
                                            @csrf
                                            <button class="btn cross-btn" onclick="return confirm('Are you sure you want to delete this version?');"
                                                    style="line-height: 0"><i class="fal fa-times"></i>
                                            </button>
                                        </form>
                                    @endif

                                </div>
                            </div>
                        @endforeach

                        @if(!request('new'))
                            <a href="{{ route('surfaces.show', [$surface->id, 'layout_id' => $layout->id, 'new' => 1]) }}"
                               class="h-full d-flex justify-content-center align-items-center px-2 bg-transparent text-decoration-none">
                                <i class="fas fa-plus btn"></i>
                            </a>
                        @endif
                            <x-menu-item
                                text="Return to 360 View" class="view-360" :img="asset('redesign/images/360-icon.png')" target="_self"
                                :route="route('tours.show', array_merge(request()->all(), ['tour' => $tour]))"
                            />

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

    <x-editor.crop-button/>

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
