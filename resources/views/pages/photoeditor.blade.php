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
                        <button class="btn btn-outline-secondary btn-sm white-bg-button" id="add-horz-guide"  data-bs-toggle="popover"
                                data-bs-trigger="hover focus"
                                data-bs-placement="bottom"
                                data-bs-content="Add Horizontal Guide">
                            <i class="fal fa-arrows-alt-h"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm white-bg-button" id="add-vert-guide" data-bs-toggle="popover"
                                data-bs-trigger="hover focus"
                                data-bs-placement="bottom"
                                data-bs-content="Add Vertical Guide">
                            <i class="fal fa-arrows-alt-v"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm white-bg-button" id="toggle-guides" data-hidden="false" data-bs-toggle="popover"
                                data-bs-trigger="hover focus"
                                data-bs-placement="bottom"
                                data-bs-content="Hide Guides">
                            <i class="fal fa-eye"></i>
                            <i class="fal fa-arrows-alt-v"></i>
                        </button>
                        <button class="btn btn-outline-secondary btn-sm white-bg-button" id="toggle-area" data-hidden="false" data-bs-toggle="popover"
                                data-bs-trigger="hover focus"
                                data-bs-placement="bottom"
                                data-bs-content="Hide Area">
                            <i class="fal fa-eye"></i>
                            <i class="fal fa-square"></i>
                        </button>
                        <x-menu-item
                            text="Save and Return"
                            class="view-360"
                            :img="null"
                            target="_self"
                            id="save-and-return"
                        />
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

@section('styles')
    <link href="{{ mix('css/page/photoeditor.css') }}" rel="stylesheet">
@endsection
@section('scripts')
    <script>
        let canvases = @json($canvases);
    </script>
    <script type="module" src="{{ asset('canvas/maineditor.js') }}"></script>

    <!-- Define onOpenCvReady before loading OpenCV.js -->
    <script>
        async function onOpenCvReady() { window.cv = await window.cv }
        document.querySelectorAll('.card').forEach(card => {
            card.addEventListener('click', function () {
                this.classList.toggle('active');
            });
        });

        function setListLayout() {
            const cardCols = document.querySelectorAll('.card-row .card-col');
            cardCols.forEach(col => {
                col.classList.remove('fix-height');
                col.classList.remove('col-md-6');
                col.classList.add('col-12');
            });

        }

        function setGridLayout() {
            const cardCols = document.querySelectorAll('.card-row .card-col');
            cardCols.forEach(col => {
                col.classList.remove('col-12');
                col.classList.add('col-md-6');
                col.classList.add('fix-height');
            });

        }

        document.addEventListener("DOMContentLoaded", function () {
            const popoverTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="popover"]'));
            popoverTriggerList.forEach(function (popoverTriggerEl) {
                new bootstrap.Popover(popoverTriggerEl);
            });
        });

    </script>

    <!-- OpenCV.js -->
    <script src="https://docs.opencv.org/4.x/opencv.js" onload="onOpenCvReady();" type="text/javascript"></script>
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
