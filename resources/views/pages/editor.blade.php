@extends('layouts.redesign')

@section('page_actions')
    @php
        $query_params = array_merge(['tour' => $spot->tour_id], request()->all());
    @endphp

    <x-page-action id="save_btn" text="{{ __('Save') }}" type="button" class="hide"/>
    <x-page-action  data-bs-toggle="modal" data-bs-target="#confirmation_modal"  text="Save As" type="button"/>
    <x-page-action id="remove_btn" text="{{ __('Remove') }}" class="hide"/>
    <x-page-action text="Return to 360 view" :url="route('tours.show', $query_params)" :visible="!$return_to_versions"/>
    <x-page-action text="Return to Versions" :url="route('tours.surfaces', $query_params)"
                   :visible="$return_to_versions"/>
@endsection

@section('breadcrumbs')
    <x-breadcrumb.breadcrumb>
        <x-breadcrumb.item :text="$project?->name"/>
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

{{--<div class="d-flex fs-5 mb-2">
    <p class="room_name mb-0">
        {{ $surface->name }} >
        @if($current_surface_state)
            <livewire:editable-field :model="$current_surface_state" field="name"/>
        @else
            <span>Untitled</span>
        @endif
    </p>
    --}}{{--<p class="ml-2" id="assignment_title">Test</p>--}}{{--
</div>--}}

{{--<div class="alert alert-danger fade slow w-100 row hide" style="position: absolute; z-index: 200; left: 0.8vw;"
     role="alert" id="error_alert">
    <strong>Cannot save! &nbsp</strong>Overlap detected on canvas between 2 or more images.
</div>--}}


@section('content')

    <section class="editor">
        <div class="container-fluid editor-view ">
            <div class="row" x-data="{sidebar:'editor'}">
                {{--<x-surface-versions :surface="$surface" :project="$project" :current_state_id="$current_surface_state?->id"/>--}}
                <template x-if="sidebar === 'editor'">
                    <livewire:artwork-collection :project="$project"/>
                </template>
                <template x-if="sidebar === 'comments'">
                    <livewire:comments :commentable="$current_surface_state"/>
                </template>
                <div class="col-8 main-col ">
                    <div class="main_content w-100 h-100">
                        <canvas id="artwork_canvas" style="z-index: 100;"></canvas>
                    </div>
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
        let user_id = {{ auth()->id() }};
        let project_id = {{ request('project_id') }};
        let updateCanvasRoute = "{{ route('surfaces.update', [$surface, 'return_to_versions' => $return_to_versions]) }}"

        let spot_id = {{ $spot->id }};
        let hlookat = {{ request('hlookat', $spot->xml->view['hlookat']) }};
        let vlookat = {{ request('vlookat', $spot->xml->view['vlookat']) }};
        let surface = @json($surface_data);
        let latestState = @json($canvas_state);
        let surface_state_id = "{{ request('surface_state_id', null) }}";
        let assignedArtworks = @json($assigned_artworks);
        let defaultScales = [];

        latestState = {};

    </script>
    <script type="module" src="{{ asset('canvas/crop_functions.js') }}"></script>
    <script type="module" src="{{ asset('canvas/artwork_assignment.js') }}"></script>
    <script type="module" src="{{ asset('canvas/canvas.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/fabric.min.js') }}"></script>
    <script type="module" src="{{ asset('canvas/twbs-pagination/jquery.twbsPagination.min.js') }}"></script>
@endsection
