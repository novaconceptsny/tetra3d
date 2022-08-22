@extends('layouts.master')

@php
    $project_id = request('project_id');
    $project = $project ?? null;
    $shared_tour_id = $shared_tour_id ?? null;
    $shared_spot_id = $shared_spot_id ?? null;
    $tour_is_shared = Route::is('shared-tours.show');
    $tracker = request('tracker', 0);
    $parameters = array_merge(request()->all(), ['tour' => $tour]);
    $parameters['tracker'] = $tracker ? 0 : 1;
    $readonly = !$project_id || $shared_tour_id;

    // only admins can see tracker
    $tracker = user()->can('perform-admin-actions') ? $tracker : 0;
@endphp

@section('page_actions')
    <x-page-action
        :visible="!$tour_is_shared" permission="perform-admin-actions"
        :url="route('tours.show', $parameters)" :class="$tracker ? 'selected' : ''"
        text="Tracker" icon="fal fa-ruler-combined"
    />
    <x-page-action
        :visible="$project && !$tour_is_shared"
        onclick="window.livewire.emit('showModal', 'modals.share-tour', '{{ $tour->id }}', '{{ $project?->id }}', '{{ request('spot_id') }}')"
        text="Share" icon="fal fa-share-nodes"
    />
    <x-page-action
        :visible="$project && !$tour_is_shared"
        :url="route('tours.surfaces', Arr::except($parameters, 'tracker'))"
        text="Versions" icon="fal fa-layer-group"
    />
    <x-page-action data-bs-toggle="modal" data-bs-target="#tourMapModal" text="Map" icon="fal fa-map-marker-alt"/>
@endsection

@section('content')
    <div class="dashboard mini">
        <livewire:editable-field :model="$spot" field="name" element="h5"/>
        <div class="image__viewer" style="height: 96%">
            @if ($tracker)
                <div id="tracker"></div>
            @endif
            <div class="featured__img" id="pano">
                <noscript>
                    <table style="width:100%;height:100%;">
                        <tr style="vertical-align:middle;">
                            <td>
                                <div style="text-align:center;">ERROR:<br/><br/>Javascript not activated<br/><br/></div>
                            </td>
                        </tr>
                    </table>
                </noscript>
            </div>
        </div>
    </div>
    <div class="modal fade" id="tourMapModal" tabindex="-1" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tour Map</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <livewire:tour-map :tour="$tour" :project="$project" :shared_tour_id="$shared_tour_id"/>
                    {{--@include('include.partials.tour-map')--}}
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script src="{{ asset("krpano/tour.js") }}"></script>

    <script type="text/javascript">
        let krpano = null;
        let hlookat = {{ request('hlookat', 0) }};
        let vlookat = {{ request('vlookat', 0) }};
        let spotId = "{{ $spot->id }}";
        let timestamp = Date.now();

        let tracker = {{$tracker}};

        embedpano({
            xml: '{{ $spot->xml_url }}' + '?' + timestamp,
            target: "pano",
            html5: "only",
            passQueryParameters: true,
            mobilescale: 1.0,
            onready: krpano_onready_callback,
            initvars: {
                timestamp: timestamp,
                showerrors: false,
                project_id: "{{ request('project_id', '') }}",
                tracker: "{{ request('tracker', '') }}",
                shared: "{{ $tour_is_shared }}",
                shared_tour_id: "{{ $shared_tour_id }}", // if tour is shared
                shared_spot_id: "{{ $shared_spot_id }}", // if only single spot is shared
                readonly: "{{ $readonly }}",

                @foreach ($spot->surfaces as $surface)
                    @php
                        // if tour is shared, or no project is set, don't show edit icon on surfaces!
                        $surface_thumbnail = $surface->getStateThumbnail($surface->state, $tour_is_shared);
                        if (!$tour_is_shared && !$project_id) {
                            $surface_thumbnail = '';
                        }
                    @endphp
                    {{ "surface_{$surface->id}" }}: '{{ $surface_thumbnail }}',
                @endforeach
            },
        });

        // let krpano = document.getElementById("krpanoSWFObject");
        function krpano_onready_callback(krpano_interface) {
            krpano = krpano_interface;
        }


        function setLookat(hlookat, vlookat) {
            if (hlookat != 0 || vlookat != 0) {
                krpano.call("set(view.hlookat," + hlookat + ")");
                krpano.call("set(view.vlookat," + vlookat + ")");
            }
        }

        let track_mouse_enabled = false;
        let track_mouse_interval_id = null;

        function track_mouse_interval_callback() {
            let mx = krpano.get("mouse.x");
            let my = krpano.get("mouse.y");
            let pnt = krpano.screentosphere(mx, my);
            let h = pnt.x;
            let v = pnt.y;
            let str = 'x="' + mx + '" y="' + my + '"<BR><BR>ath="' + h.toFixed(2) + '" atv="' + v.toFixed(2) + '"';

            let hlookat = krpano.get("view.hlookat").toFixed(2);
            let vlookat = krpano.get("view.vlookat").toFixed(2);
            let fov = krpano.get("view.fov").toFixed(2);
            let str2 = '<BR><BR> hlookat:' + hlookat + ', vlookat:' + vlookat + ', fov:' + fov;
            $("#tracker").html(str + str2);
        }

        function track_mouse() {
            if (krpano) {
                if (track_mouse_enabled === false) {
                    // enable - call 60 times per second
                    track_mouse_interval_id = setInterval(track_mouse_interval_callback, 1000.0 / 60.0);
                    track_mouse_enabled = true;
                } else {
                    // disable
                    clearInterval(track_mouse_interval_id);
                    $("#tracker").html("");
                    track_mouse_enabled = false;
                }
            }
        }

        if (tracker === 1) {
            track_mouse();
        }

        krpano.call("set(layer['version'].onclick,openurl('/version/management/spot/{{$spot->id}}'))");
        setLookat(hlookat, vlookat);

    </script>

    <script>
        function setMapScale() {
            let $pin = $('.pin');
            $pin.hide();

            let zoneW;
            let zoneH;
            let defaultW;
            let defaultH;

            $(".floorPlan").each(function () {
                if ($(this).css('display') === 'block') {
                    zoneW = $(this).innerWidth();
                    zoneH = $(this).innerHeight();
                    defaultW = $(this).attr('defaultWidth');
                    defaultH = $(this).attr('defaultHeight');
                }
            });


            let scaleW = zoneW / defaultW;
            let scaleH = zoneH / defaultH;

            let scale;
            let screenRatio = zoneW / zoneH;
            let mapRatio = defaultW / defaultH;

            if (screenRatio > mapRatio) {
                scale = scaleH;
            } else {
                scale = scaleW;
            }

            $pin.each(function () {
                let top = $(this).attr('top');
                let left = $(this).attr('left');
                $(this).css('top', (top * scale - 40 + "px"));
                $(this).css('left', (left * scale - 20 + "px"));
            });

            $pin.show();
        }

        $(document).ready(function () {
            setMapScale();
        });

        $(window).resize(function () {
            setMapScale();
        });

        Livewire.on('mapChanged', () => {
            setMapScale();
        });
    </script>
@endsection
