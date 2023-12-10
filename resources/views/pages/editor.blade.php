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

        <x-menu-item text="List View" icon="fal fa-clone" :route="route('tours.surfaces', $query_params)"/>
        <a href="{{ route('tours.surfaces', $query_params) }}" target="_blank"  data-content="360 View">
                <svg xmlns="http://www.w3.org/2000/svg"
                     width="512.000000pt" height="512.000000pt" viewBox="0 0 512.000000 512.000000"
                     preserveAspectRatio="xMidYMid meet">

                    <g transform="translate(0.000000,512.000000) scale(0.100000,-0.100000)"
                       fill="#000000" stroke="none">
                        <path d="M2370 5114 c-19 -2 -78 -9 -130 -15 -788 -90 -1517 -582 -1919 -1296
-313 -555 -401 -1238 -241 -1873 113 -450 345 -855 681 -1188 163 -161 195
-177 244 -127 47 46 37 65 -106 203 -215 207 -363 406 -490 657 -58 116 -135
303 -127 311 2 1 26 -25 53 -58 144 -176 449 -398 718 -523 l104 -48 48 -104
c125 -270 347 -574 523 -718 34 -27 58 -51 53 -53 -11 -4 -211 78 -301 123
-41 21 -119 64 -172 96 -54 33 -109 59 -123 59 -59 0 -88 -85 -43 -127 39 -35
156 -103 310 -178 791 -388 1766 -326 2502 159 1028 676 1432 1947 984 3095
-238 609 -753 1140 -1358 1399 -216 93 -419 150 -660 187 -100 15 -472 28
-550 19z m351 -159 c331 -58 629 -272 871 -625 53 -77 168 -273 168 -286 0 -1
-33 8 -72 22 -104 35 -334 91 -470 114 -433 73 -927 71 -1348 -6 -142 -26
-355 -79 -452 -113 -32 -11 -58 -18 -58 -16 0 13 128 229 182 306 319 456 754
679 1179 604z m-966 -147 c-203 -168 -419 -459 -551 -742 l-47 -103 -103 -47
c-282 -132 -572 -347 -743 -551 l-41 -50 14 40 c201 571 618 1054 1156 1338
86 46 341 157 360 157 3 0 -17 -19 -45 -42z m1850 -77 c528 -254 958 -708
1180 -1246 48 -115 68 -172 57 -160 -5 6 -41 47 -80 92 -146 167 -395 349
-652 476 l-145 72 -67 135 c-140 285 -343 555 -538 714 l-45 36 85 -30 c47
-17 139 -57 205 -89z m-871 -651 c381 -20 748 -91 1033 -200 64 -25 83 -36 92
-58 61 -155 117 -350 155 -537 132 -655 73 -1408 -155 -1987 -13 -31 -95 -66
-291 -123 -623 -181 -1348 -185 -1979 -10 -114 32 -304 97 -316 108 -11 12
-67 174 -102 297 -180 629 -180 1346 -1 1980 59 208 96 296 128 309 229 90
511 160 787 196 109 14 196 21 445 33 14 1 106 -3 204 -8z m-1675 -377 c-30
-86 -85 -304 -108 -423 -86 -457 -87 -969 0 -1435 22 -122 77 -338 108 -427
11 -32 18 -58 17 -58 -13 0 -209 115 -286 168 -432 296 -660 686 -637 1086 25
419 312 804 812 1087 55 32 103 58 107 58 4 1 -2 -25 -13 -56z m3089 2 c502
-280 794 -669 819 -1091 23 -400 -206 -791 -637 -1086 -83 -57 -271 -168 -284
-168 -3 0 6 33 20 72 35 104 91 334 114 470 71 420 71 896 0 1316 -23 136 -79
366 -114 470 -14 39 -23 72 -21 72 3 0 49 -25 103 -55z m678 -1967 c-128 -339
-294 -602 -541 -857 -258 -266 -553 -457 -905 -588 l-65 -24 50 42 c202 169
393 424 533 709 l67 135 145 72 c262 130 510 312 659 485 42 48 77 88 78 88 2
0 -8 -28 -21 -62z m-3181 -743 c491 -122 1057 -142 1573 -55 136 23 366 79
470 114 39 14 72 23 72 22 0 -13 -115 -209 -168 -286 -581 -848 -1476 -851
-2059 -8 -51 74 -173 281 -173 294 0 1 33 -8 72 -22 40 -13 136 -40 213 -59z"/>
                        <path d="M3595 3096 c-93 -44 -145 -126 -145 -231 0 -71 23 -126 71 -174 159
-159 432 -49 432 174 0 187 -190 310 -358 231z m164 -142 c65 -46 57 -145 -16
-179 -102 -49 -194 86 -114 166 34 34 92 40 130 13z"/>
                        <path d="M2950 3018 c-75 -29 -150 -105 -179 -183 -21 -56 -22 -71 -19 -334
l3 -276 30 -54 c64 -115 174 -176 305 -169 120 7 212 68 272 180 22 41 23 53
26 325 l3 283 -25 25 c-32 32 -75 33 -104 2 -21 -23 -22 -30 -22 -281 0 -289
-3 -304 -68 -354 -30 -23 -45 -27 -102 -27 -57 0 -72 4 -102 27 -66 51 -69 67
-66 351 3 246 4 253 26 285 31 42 90 72 145 72 59 0 97 29 97 75 0 73 -103 98
-220 53z"/>
                        <path d="M1527 3020 c-99 -25 -184 -103 -214 -196 -19 -62 -10 -97 32 -116 44
-20 76 0 110 67 25 50 38 64 77 83 61 31 109 23 159 -27 47 -47 57 -95 30
-155 -23 -52 -54 -75 -118 -87 -59 -11 -83 -33 -83 -76 0 -44 27 -69 86 -78
61 -9 117 -61 128 -118 9 -51 -21 -120 -66 -147 -89 -54 -218 10 -218 109 0
23 -9 42 -29 62 -35 34 -59 36 -95 8 -38 -30 -37 -104 5 -187 49 -99 128 -152
240 -160 124 -9 230 52 285 164 49 101 31 239 -42 315 l-26 27 35 45 c45 59
61 107 60 182 0 180 -184 327 -356 285z"/>
                        <path d="M2335 2982 c-47 -30 -139 -132 -194 -216 -61 -94 -107 -210 -133
-336 -30 -147 -10 -234 73 -325 60 -65 127 -97 215 -103 134 -9 240 51 304
169 32 59 34 71 35 149 0 95 -19 146 -79 217 -56 64 -125 96 -228 102 l-88 6
36 53 c19 30 67 86 105 127 74 77 84 105 52 143 -26 31 -63 36 -98 14z m62
-509 c78 -37 113 -145 73 -227 -69 -145 -278 -120 -321 37 -36 136 116 253
248 190z"/>
                    </g>
                </svg>
        </a>
        <x-menu-item text="Map" icon="fal fa-map-marked-alt" data-bs-toggle="modal" data-bs-target="#tourMapModal"/>
        <x-menu-item
            target="_self"
            wire:modal="modals.share-tour, @js(['tourId' => $tour->id, 'layoutId' => $layout->id, 'spotId' => request('spot_id')])"
            text="Share" icon="fal fa-share-nodes"
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
                <livewire:artwork-collection :project="$project" />

                @php($canvasId = $selectedSurfaceState ? $selectedSurfaceState->id : 'new')
                <div class="col-9 main-col" x-data="{ activeCanvas: @js("artwork_canvas_$canvasId") }">
                    <x-editor-actions/>

                    <div class="d-inline-flex tabs-container pt-1 mb-1 px-2">
                        @foreach($canvases as $canvas)
                            <div class="tab mt-1"
                                 :class="activeCanvas === @js($canvas['canvasId']) ? 'active' : ''"
                                 @click="activeCanvas = @js($canvas['canvasId']); $dispatch('canvasChanged', { surfaceStateId: @js($canvas['surfaceStateId']) })">
                                {{ $canvas['surfaceStateName'] }}
                            </div>
                        @endforeach

                        @if(!request('new'))
                            <a href="{{ route('surfaces.show', [$surface->id, 'layout_id' => $layout->id, 'new' => 1]) }}"
                               class="h-full d-flex justify-content-center align-items-center px-2 bg-transparent text-decoration-none">
                                <i class="fas fa-plus btn"></i>
                            </a>
                        @endif
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

    <div class="modal fade" id="mapImage" tabindex="-1" >
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
