@extends('layouts.redesign')

@php
    $layout_id = request('layout_id');
    $project = $project ?? null;
    $shared_tour_id = $shared_tour_id ?? null;
    $shared_spot_id = $shared_spot_id ?? null;
    $tour_is_shared = Route::is('shared-tours.show') || request('shared');
    $tracker = request('tracker', 0);
    $parameters = array_merge(request()->all(), ['tour' => $tour]);
    $parameters['tracker'] = $tracker ? 0 : 1;
    $readonly = !$layout_id || $shared_tour_id;

    // only admins can see tracker
    $tracker = user()?->can('perform-admin-actions') ? $tracker : 0;
@endphp

@section('page_actions')
    @auth
        <x-page-action
            :visible="!$tour_is_shared" permission="perform-admin-actions"
            :url="route('tours.show', $parameters)" :class="$tracker ? 'selected' : ''"
            text="Tracker" icon="fal fa-ruler-combined"
        />
    @endauth
@endsection

@section('outside-menu')
    <div class="menu-links d-flex align-items-center gap-4">
        <x-menu-item
            text="List View" icon="fal fa-clone" :visible="$project && !$tour_is_shared"
            :route="route('tours.surfaces', Arr::except($parameters, 'tracker'))"
        />
        <x-menu-item
            route="#" target="_self" text="Map"
            icon="fal fa-map-marked-alt"
            data-bs-toggle="modal" data-bs-target="#tourMapModal"
        />
        <x-menu-item
            :visible="$layout && !$tour_is_shared" target="_self"
            onclick="Livewire.dispatch('modal.open', {component: 'modals.share-tour', arguments: {'layout': {{ request('layout_id') }} }})"
            text="Share" icon="fal fa-share-nodes"
        />
        <x-menu-item text="Artwork Collection" icon="fal fa-palette" :route="route('artworks.index')" :visible="!$tour_is_shared"/>
    </div>
@endsection

@section('breadcrumbs')
    <x-breadcrumb.breadcrumb>
        <x-breadcrumb.item :text="$project ? $project->name : 'No Project'"/>
        <x-breadcrumb.separtator/>

        <x-breadcrumb.item :text="$layout?->name"/>
        <x-breadcrumb.separtator/>

        <x-breadcrumb.item>
            <livewire:editable-field :model="$spot" field="name" element="span"/>
        </x-breadcrumb.item>

    </x-breadcrumb.breadcrumb>
@endsection

@section('content')
    <div style="height: calc(100vh - 52px);">
        <div class="h-100">
            @if ($tracker)
                <div id="tracker"></div>
            @endif
            <div class="w-100 h-100" id="pano">
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

    @if($project?->id && !$tour_is_shared)
        <button class="previous-btn" style="z-index: 39"
                onclick="Livewire.dispatch('slide-over.open', {component: 'tour-switcher', arguments: {'project': {{$project?->id}} }})">
            <i class="fas fa-chevron-left"></i>
        </button>
    @endif
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
                layout_id: "{{ request('layout_id', '') }}",
                tracker: "{{ request('tracker', '') }}",
                shared: "{{ $tour_is_shared }}",
                shared_tour_id: "{{ $shared_tour_id }}", // if tour is shared
                shared_spot_id: "{{ $shared_spot_id }}", // if only single spot is shared
                readonly: "{{ $readonly || $tour_is_shared}}",

                @foreach ($spot->surfaces as $surface)
                    {{ "surface_{$surface->id}" }}: '{{ $surface->getStateThumbnail($surface->state, $tour_is_shared) }}',
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
        document.addEventListener('livewire:init', () => {
            Livewire.on('layoutDeleted', (event) => {
                let currentLayoutId = new URL(window.location.href).searchParams.get("layout_id");
                if(currentLayoutId == event.layoutId){
                    window.location = @js(route('dashboard'));
                }
            });
        });
    </script>
@endsection
