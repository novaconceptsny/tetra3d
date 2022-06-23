@extends('layouts.master')

@section('page_actions')
    <x-page-action id="save_btn" title="{{ __('Save') }}"/>
    <x-page-action id="save_as_btn" title="{{ __('Save As') }}"/>
    <x-page-action id="remove_btn" title="{{ __('Remove') }}"/>
    <x-page-action id="remove_btn" title="{{ __('Return to 360 view') }}"/>
@endsection

@section('content')
    @include('include.partials.collection')
    <button class="btn sidebar__trigger fixed">
        <x-svg.angle-right/>
    </button>
    <x-surface-versions/>
    <div class="dashboard mini" style="margin-left: 27rem!important;">
        <div class="d-flex fs-5 mb-2">
            <p class="room_name mb-0">s4002 > Test</p>
            <p class="ml-2" id="assignment_title">Test</p>
        </div>
        <div class="image__viewer main_content">
            <canvas id="artwork_canvas" style="z-index: 100;"></canvas>
        </div>
    </div>

    <button type="button" id="crop_btn" class="btn btn-outline-info" data-toggle="tooltip" data-placement="top"
            style="z-index:3000;position:fixed;left:500px;top:100px;width:50px;height:50px;" title="Crop">
        <i class="fas fa-crop-alt" style="font-size:20px"></i>
    </button>
@endsection

@section('scripts')
    <script>
        let userId = 1;

        let spotId = 48001;
        let canvasId = 4002;
        let vlookat = 0;
        let hlookat = 0;
        let canvasDBArr = null;
        let latestState = null;
        let versionId = 240;
        let artworkArr = null;
        let scaleArr =  null;
        let locationId = 4;
        let artgroupId = 5;
        let defaultScaleArr = [];
        let artworkTotalNum = null;

        artworkArr = [];
        locationId = 4;
        vlookat = 0;
        hlookat = -90;
        canvasDBArr = {
            "background_url": "{{ $surface->getFirstMediaUrl('background') }}",
            "spot_name": "na",
            "overlay_url": null,
            "bound_box_top": 110,
            "bound_box_left": 205,
            "bound_box_height": 930,
            "bound_box_width": 1510,
            "img_width": 1644,
            "img_height": 1810,
            "hotspot_width_px": null,
            "actual_width_inch": 208
        }; //這裡其實應該是一個json。代表單一surface的data而已
        latestState = {};
        artworkTotalNum = 15;

    </script>
    <script type="module" src="{{ asset('canvas/crop_functions.js') }}"></script>
    <script type="module" src="{{ asset('canvas/artwork_assignment.js') }}"></script>
    <script type="module" src="{{ asset('canvas/canvas.js') }}"></script>
    <script type="text/javascript" src="{{ asset('js/fabric.min.js') }}"></script>
    <script type="module" src="{{ asset('canvas/twbs-pagination/jquery.twbsPagination.min.js') }}"></script>

    <script>
        /*document.addEventListener('livewire:load', function () {
            console.log('her');
            Livewire.on('artworksLoaded', (artwork_scales) => {
                scaleArr = artwork_scales;
                console.log(scaleArr);
            });
        });*/

        window.addEventListener('artworksLoaded', function (){
            console.log('her');
        })
    </script>
@endsection
