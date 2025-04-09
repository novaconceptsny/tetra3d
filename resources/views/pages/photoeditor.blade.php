@extends('layouts.redesign')

@section('outside-menu')


@section('breadcrumbs')
<x-breadcrumb.breadcrumb>
    <x-breadcrumb.item :text="$project ? $project->name : 'No Project'" />
    <x-breadcrumb.separtator />

    <x-breadcrumb.item :text="$layout?->name" />
    <x-breadcrumb.separtator />

    <x-breadcrumb.item :text="$surface->name" />

</x-breadcrumb.breadcrumb>
@endsection

@section('content')
<section class="editor">
    <div class="container-fluid editor-view ">

        @php($sidebar = request('sidebar', 'editor'))
        <div class="row" x-data="{sidebar: @js($sidebar) }">
            <livewire:artwork-collection :project="$project" />

            @php($canvasId = $surface ? $surface->id : 'new')
            <div class="col-9 main-col position-relative"
                x-data="{ changedCanvases: 0, activeCanvas: @js("artwork_canvas_$canvasId") }">

                <div class="d-inline-flex tabs-container mb-1 pe-2">

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


<x-editor.crop-button />


@endsection

@section('scripts')
<script>
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