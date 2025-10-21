@extends('layouts.redesign')

@section('styles')
<style>
    /* Hide the white header on tour page */
    #header {
        display: none !important;
    }
    
    /* Remove margin-top from main content when header is hidden */
    #site__body {
        margin-top: 0 !important;
    }
    
    /* Make tour page full height */
    body {
        height: 100vh;
        overflow: hidden;
    }
    
    #site__body {
        height: 100vh;
    }
    
    /* Custom Tour Header Styles */
    .tour-custom-header {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        z-index: 1000;
        padding: 12px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        border-bottom: 1px solid rgba(255, 255, 255, 0.1);
 
    }
    
    .tour-header-left {
        display: flex;
        align-items: center;
        gap: 12px;
    }
    
    .tour-header-icon {
        color: white;
        font-size: 16px;
        opacity: 0.8;
    }
    
    .tour-header-text {
        color: white;
        font-size: 18px;
        font-weight: 500;
        letter-spacing: 0.5px;
        text-shadow: 
            -1px -1px 0 rgba(0, 0, 0, 0.6),
            1px -1px 0 rgba(0, 0, 0, 0.6),
            -1px 1px 0 rgba(0, 0, 0, 0.6),
            1px 1px 0 rgba(0, 0, 0, 0.6);
    }
    
    .tour-header-right {
        display: flex;
        align-items: center;
        margin-right: 20px;
        gap: 50px;
    }
    
    .tour-header-buttons {
        display: flex;
        gap: 8px;
    }
    
    .tour-header-btn {
        background:  rgba(255, 255, 255, 0.6);
        border: none;
        color: black;
        width: 40px;
        height: 40px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        transition: all 0.2s ease;
        font-size: 16px;
    }
    
    
    .tour-header-btn:active {
        transform: translateY(0);
    }
    
    .tour-header-btn i {
        font-size: 16px;
    }
  
</style>
@endsection

@php
    $layout_id = request('layout_id');
    $project = $project ?? null;
    $shared_tour_id = $shared_tour_id ?? null;
    $shared_spot_id = $shared_spot_id ?? null;
    $tour_is_shared = $tour_is_shared ?? (Route::is('shared-tours.show') || request('shared'));
    $tracker = request('tracker', 0);
    $parameters = array_merge(request()->all(), ['tour' => $tour]);
    $parameters['tracker'] = $tracker ? 0 : 1;
    $readonly = !$layout_id || $shared_tour_id;
    $layout = $layout ?? null;
    $spotPosition = $spotPosition ?? null;
    $tourModel = $tourModel ?? null;
    $sculptureData = $sculptureData ?? null;
    $artworkData = $artworkData ?? null;
    $surfaceData = $surfaceData ?? null;
    $sculptures = $sculptures ?? array();

    // only admins can see tracker
    $tracker = user()?->can('perform-admin-actions') ? $tracker : 0;
    $userName = user()?->name;

    $query_params = array_merge(['tour' => $spot->tour_id], request()->all());
@endphp

@section('page_actions')
@auth
    <x-page-action :visible="!$tour_is_shared" permission="perform-admin-actions" :url="route('tours.show', $parameters)"
        :class="$tracker ? 'selected' : ''" text="Tracker" icon="fal fa-ruler-combined" />
@endauth
@endsection

@section('outside-menu')
<div class="menu-links d-flex align-items-center gap-4">
    @if ($userName == "Super Admin")
        <div id="toggle_layout">
            <button onclick="toggleLayout()" class="toggle-layout">Space Model</button>
        </div>
    @else
        <div id="toggle_layout" hidden>
            <button onclick="toggleLayout()" class="toggle-layout">Space Model</button>
        </div>
    @endif
    <!-- @if (!isset($tourModel))
        <x-menu-item text="List View" icon="fal fa-clone" :visible="$project && !$tour_is_shared"
            :route="route('tours.surfaces', Arr::except($parameters, 'tracker'))" />
    @endif -->
    <x-menu-item :visible="false" text="List View" icon="fal fa-clone" :route="route('tours.surfaces', $query_params)" target="_self" />
    <x-menu-item :visible="$layout && !$tour_is_shared" target="_blank"
        :route="route('share.index', ['layout_id' => $layout?->id])"
        text="Share" icon="fal fa-share-nodes" />
    <x-menu-item text="Artwork Collection" icon="fal fa-palette" :route="route('inventory.index')"
        :visible="!$tour_is_shared" />
    <x-menu-item text="Sculpture List" icon="fal fa-cube" target="_self" route="#" :visible="$project && !$tour_is_shared && $tourModel" data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample"
        aria-controls="offcanvasExample" />
</div>
@endsection

@section('breadcrumbs')
<x-breadcrumb.breadcrumb>
    <x-breadcrumb.item :text="$project ? $project->name : 'No Project'" />
    <x-breadcrumb.separtator />

    <x-breadcrumb.item :text="$layout?->name" />
    <x-breadcrumb.separtator />

    <x-breadcrumb.item :text="$spot?->name" />

</x-breadcrumb.breadcrumb>
@endsection

@section('content')
<!-- Landscape Orientation Prompt -->
<div id="landscapePrompt" class="landscape-prompt">
    <div class="landscape-prompt-icon">
        <i class="fas fa-mobile-alt"></i>
    </div>
    <div class="landscape-prompt-title">Rotate Your Device</div>
    <div class="landscape-prompt-message">
        For the best experience, please rotate your device to landscape mode to view the tour.
    </div>
</div>

<div style="height: 100vh;">
    <div class="h-100 position-relative">
        <div class="tour-custom-header">
            <div class="tour-header-left">
                <div class="tour-header-icon">
                    <img src="{{ asset('backend/images/logo/logo_small.png') }}" alt="dash-logo" style="width: 40px; height: 40px;"/>
                </div>
                <div class="tour-header-text">{{ $project ? $project->name : 'No Project' }} > {{ $layout ? $layout->name : 'No Layout' }} > {{ $spot ? $spot->name : 'No Spot' }}</div>
            </div>
            <div class="tour-header-right">
                <div class="tour-header-buttons">
                    <button class="tour-header-btn" title="Share" onclick="toggleShare()">
                        <i class="fas fa-share-nodes"></i>
                    </button>
                    <button class="tour-header-btn" title="Artwork Collection" onclick="toggleArtworkCollection()">
                        <i class="fas fa-palette"></i>
                    </button>
                    <button class="tour-header-btn" title="Sculpture List" onclick="toggleSculptureList()">
                        <i class="fas fa-monument"></i>
                    </button>
                    <button class="tour-header-btn" title="3D Model" onclick="toggleLayout()">
                        <i class="fas fa-cube"></i>
                    </button>
                    <button class="tour-header-btn" title="Tracker" onclick="toggleTracker()">
                        <i class="fas fa-ruler-combined"></i>
                    </button>          
                </div>
                <div class="tour-header-user-buttons">
                    @auth
                        <div class="nav-item dropdown">
                            <a class="dropdown-link nav-link text-white profile-menu-btn" href="#" id="tourUserDropdown" role="button"
                                data-bs-toggle="dropdown" style="padding: 0;">
                                <img class="user-img-border" src="{{ user()->avatar_url }}"
                                        alt="{{ user()->name }}"/>
                            </a>
                            <ul class="dropdown-menu pro-drop" aria-labelledby="tourUserDropdown">
                                <div class="drop-profile">
                                    <img class="user-img-border" src="{{ user()->avatar_url }}"
                                            alt="{{ user()->name }}">
                                    <div class="user-detail">
                                        <h6>{{ user()->name }}</h6>
                                        <p class="profile-email">{{ user()->email }}</p>
                                    </div>
                                </div>
                                <div class="link">
                                    @can('access-backend')
                                        <li>
                                            <a class="dropdown-item" href="{{ route('backend.dashboard') }}"
                                                target="_blank">
                                                <i class="fal fa-user-shield"></i>
                                                {{ __('Admin Area') }}
                                            </a>
                                        </li>
                                    @endcan
                                    @if(session()->has('admin_id'))
                                        <li>
                                            <a class="dropdown-item" href="javascript:void(0);"
                                                target="_blank"
                                                onclick="event.preventDefault(); document.getElementById('back-to-admin-form').submit();">
                                                <i class="fal fa-arrow-from-left"></i>
                                                {{ __('Back to Admin') }}
                                            </a>
                                            <form id="back-to-admin-form" target="_blank" class="d-none"
                                                    action="{{ route('back.to.admin') }}" method="post">
                                                @csrf
                                            </form>
                                        </li>
                                    @endif
                                    <li>
                                        <a class="dropdown-item" href="{{ route('profile.edit') }}">
                                            <i class="fal fa-user"></i>
                                            {{ __('My Profile') }}
                                        </a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item" href="#"
                                            onclick="event.preventDefault(); document.getElementById('logout-form').submit();">
                                            <i class="fal fa-sign-out"></i>
                                            {{ __('Logout') }}
                                        </a>
                                    </li>
                                    <form id="logout-form" action="{{ route('logout') }}" method="POST"
                                            class="d-none">
                                        @csrf
                                    </form>
                                </div>
                            </ul>
                        </div>
                    @endauth
                </div>
            </div>
        </div>
        @if ($tracker)
            <div id="tracker"></div>
        @endif
        <div class="w-100 h-100" id="pano">
            <noscript>
                <table style="width:100%;height:100%;">
                    <tr style="vertical-align:middle;">
                        <td>
                            <div style="text-align:center;">ERROR:<br /><br />Javascript not activated<br /><br /></div>
                        </td>
                    </tr>
                </table>
            </noscript>
        </div>
        <div class="view-map-btn">
            <x-menu-item route="#" target="_self" icon="fal fa-map-marked-alt" data-bs-toggle="modal"
                data-bs-target="#tourMapModal" />
        </div>
    </div>
</div>

@if($project?->id && !$tour_is_shared)
    <button class="previous-btn" style="z-index: 39"
        onclick="Livewire.dispatch('slide-over.open', {component: 'updated-tour-switcher', arguments: {'project': {{$project?->id}} }})">
        <i class="fas fa-chevron-left"></i>
    </button>
@endif
@endsection

@section('modellist')
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Sculpture List</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div class="row mt-3 modellist">
            <div class="col-6">
                @foreach($sculptures as $sculpture)
                    @if ($sculpture->id % 2 == 1)
                        <div class='sculpture-list'>
                            <img class="image-list-item" src="{{$sculpture->getFirstMediaUrl('thumbnail') }}"
                                data-bs-dismiss="offcanvas" alt="Image 1" data-image-id="{{ $sculpture->id }}">
                            <div class='sculpture-list-data-container'>
                                <div class='sculpture-list-data-artist'>{{ $sculpture->artist }}</div>
                                <div class='sculpture-list-data-name'>{{ $sculpture->name }}</div>
                                <div class='sculpture-list-data-dimention'>{{ $sculpture->dimensions }}</div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
            <div class="col-6">
                @foreach($sculptures as $sculpture)
                    @if ($sculpture->id % 2 == 0)
                        <div class='sculpture-list'>
                            <img class="image-list-item" src="{{$sculpture->getFirstMediaUrl('thumbnail') }}"
                                data-bs-dismiss="offcanvas" alt="Image 1" data-image-id="{{ $sculpture->id }}">
                            <div class='sculpture-list-data-container'>
                                <div class='sculpture-list-data-artist'>{{ $sculpture->artist }}</div>
                                <div class='sculpture-list-data-name'>{{ $sculpture->name }}</div>
                                <div class='sculpture-list-data-dimention'>{{ $sculpture->dimensions }}</div>
                            </div>
                        </div>
                    @endif
                @endforeach
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="{{ asset("krpano/tour.js") }}"></script>
<script src="https://unpkg.com/three@0.100.0/build/three.min.js"></script>
<script src="https://unpkg.com/three@0.100.0/examples/js/loaders/GLTFLoader.js"></script>
<script src="https://unpkg.com/three@0.100.0/examples/js/loaders/DRACOLoader.js"></script>

<script type="module">
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
                {{ "surface_{$surface->id}" }}: '{{ $surface->getStateThumbnail($surface->state, $tour_is_shared, $tour->has_model) }}',
            @endforeach
            },
        });

    function krpano_onready_callback(krpano_interface) {
        krpano = krpano_interface;

        console.log("🕵️ krpano ready, interface:", krpano_interface);

        // helper to actually resize both krpano AND the raw canvas
        let doResize = () => {
            const w = window.innerWidth;
            const h = window.innerHeight;
            const dpr = window.devicePixelRatio;
            console.log("🔄 doResize() → viewport:", w, "×", h, "DPR:", dpr);

            // 1️⃣ tell krpano to update
            krpano_interface.call("resize()");
            console.log("   → called krpano.resize()");

            // 2️⃣ then override the raw canvas element
            const c = document.querySelector("#krpanoSWFObject canvas");
            if (c) {
                c.style.width  = w + "px";
                c.style.height = h + "px";
                c.width  = w * dpr;
                c.height = h * dpr;
                console.log(
                "   → canvas forced to CSS:", c.style.width, "×", c.style.height,
                "ACTUAL:", c.width, "×", c.height
                );
            } else {
                console.warn("   ⚠️ canvas element not found!");
            }
        };

        // initial pass after layout
        setTimeout(doResize, 500);

        // debounce real resizes
        let resizeTimeout;
        window.addEventListener("resize", () => {
            console.log("📐 window.resize fired");
            clearTimeout(resizeTimeout);
            resizeTimeout = setTimeout(doResize, 250);
        });
        window.addEventListener("orientationchange", () => {
            console.log("🔃 orientationchange fired");
            setTimeout(doResize, 300);
        });
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
    var M_RAD = Math.PI / 180.0;
    var clock = null;
    var animatedobjects = [];
    var box = null;
    var walls = [];
    var isAnimate = false;
    var selectedObj = null;
    var isDown = false;
    var init_depth = 40;
    var sculpture_id = 0;
    var sculpture_id_list = [];
    var artwork_id_list = [];
    var offset_x = 0;
    var offset_y = 0;
    var offset_z = 0;
    var planeMesh = null;
    var wallMesh1 = null;
    var wallMesh2 = null;
    var wallMesh3 = null;
    var model = null;
    var surface = null;
    var user_name = '';
    var toggle_space_model = false;
    var sculpture_change_list = [];
    var surface_meshes = [];
    var tourScale = 200;
    var offsetScale = 200;
    var sculptureScale = 200;

    var layout_id = '{{ $layout_id }}';
    var shared_tour_id = '{{ $shared_tour_id }}';
    var sculptures = @json($sculptures);
    var sculpture_data = @json($sculptureData);
    var artworks_data = @json($artworkData);
    console.log(artworks_data, "assigned artworks");
    var surface_data = @json($surfaceData);
    var spot_id = "{{ $spot->id }}";

    var spot_position = @json($spotPosition);
    var space_model = @json($tourModel);
    var tour_is_shared = @json($tour_is_shared);
    var user_name = @json($userName);
    var has_model = {{ $tour->has_model === 1 ? 'true' : 'false' }};

    var sculptureUrls = {
            @foreach($sculptures as $sculpture)
                "{{ $sculpture->id }}": {
                    sculpture: "{{ $sculpture->getFirstMediaUrl('sculpture') }}",
                    interaction: "{{ $sculpture->getFirstMediaUrl('interaction') }}"
                },
            @endforeach
    };

    function toggleLayout() {
        if (surface_meshes.length > 0) {
            surface_meshes.forEach(mesh => {
                mesh.material.visible = !mesh.material.visible;
            });
        }

        if (space_model == null || space_model.name == 'null' || model == null) {
            alert("No 3D space model");
        } else {
            toggle_space_model = !toggle_space_model;
            if (toggle_space_model) {
                model.traverse((obj) => {
                    if (obj instanceof THREE.Mesh) {
                        obj.material.colorWrite = true;
                        obj.material.visible = true;
                        obj.material.transparent = true;
                        obj.material.opacity = 0.5;
                    }
                });
            } else {
                model.traverse((obj) => {
                    if (obj instanceof THREE.Mesh) {
                        obj.material.colorWrite = false;
                        obj.material.visible = true;
                        obj.material.transparent = false;
                        obj.material.opacity = 1;
                    }
                });
            }
        }

    };

    window.addEventListener('beforeunload', function (e) {
        scene.children.forEach(function (object) {
            if (object.userData.changed) {
                e.preventDefault();
            }
        });
    });

    var delay_interval = setInterval(function () {
        if (window.scene !== undefined && has_model) {
            clearInterval(delay_interval);
            if (space_model == null || space_model.name == 'null') {
                alert("No 3D space model");
            } else {

                offset_x = spot_position.x * offsetScale;
                offset_y = spot_position.y * offsetScale;
                offset_z = spot_position.z * offsetScale;

                var loader = new THREE.GLTFLoader();
                var dracoLoader = new THREE.DRACOLoader();
                loader.setDRACOLoader(dracoLoader);

                var model_url = 'storage/3dmodel/' + space_model.name;
                var surface_url = 'storage/3dmodel/surface/' + space_model.surface;
                var asset_url = '<?php echo asset(''); ?>';
                var full_model_url = asset_url + model_url;
                var full_surface_url = asset_url + surface_url;

                // Load Base Space model
                loader.load(full_model_url, function (gltf) {
                    model = gltf.scene;
                    model.traverse((obj) => {
                        if (obj instanceof THREE.Mesh) {
                            obj.material = new THREE.MeshBasicMaterial({ color: 0x00ff00, colorWrite: false })
                        }
                    });
                    model.rotation.x = -Math.PI;
                    model.rotation.y = Math.PI / 2;
                    model.scale.set(tourScale, tourScale, tourScale);
                    model.position.set(-offset_x, offset_y, offset_z);
                    scene.add(model);

                    loader.load(full_surface_url, function (gltf) {
                        surface = gltf.scene;
                        surface.traverse((obj) => {
                            if (obj instanceof THREE.Mesh) {
                                obj.name = "surface-model";
                                obj.material = new THREE.MeshBasicMaterial({ color: 0x00ffff, colorWrite: false })
                            }
                        });
                        surface.rotation.x = -Math.PI;
                        surface.rotation.y = Math.PI / 2;

                        surface.scale.set(tourScale, tourScale, tourScale);
                        surface.position.set(-offset_x, offset_y, offset_z);

                        scene.add(surface);
                    });


                    for (let i = 0; i < surface_data.length; i++) {
                        loadSurfaces(
                            surface_data[i].surface_id,
                            surface_data[i].width,
                            surface_data[i].height,
                            surface_data[i].start_pos['x'] * offsetScale - spot_position.x * offsetScale,
                            -surface_data[i].start_pos['y'] * offsetScale + spot_position.y * offsetScale,
                            -surface_data[i].start_pos['z'] * offsetScale + spot_position.z * offsetScale,
                            surface_data[i].normal['x'],
                            surface_data[i].normal['y'],
                            surface_data[i].normal['z']
                        );
                    }

                    if (sculpture_data !== null && Object.keys(sculptureUrls).length > 0) {
                        for (let i = 0; i < sculpture_data.length; i++) {
                            sculpture_id_list.push(sculpture_data[i].sculpture_id);

                            load_model(sculpture_data[i].sculpture_id,
                                sculpture_data[i].model_id,
                                sculpture_data[i].position_x - spot_position.x * offsetScale,
                                sculpture_data[i].position_y - spot_position.y * offsetScale,
                                sculpture_data[i].position_z + spot_position.z * offsetScale,
                                sculpture_data[i].rotation_x,
                                sculpture_data[i].rotation_y,
                                sculpture_data[i].rotation_z
                            );
                        }
                    }


                    for (let i = 0; i < artworks_data.length; i++) {
                        artwork_id_list.push(artworks_data[i].artwork_id);
                        load_artModels(artworks_data[i].artwork_id,
                            artworks_data[i].surface_id,
                            artworks_data[i].image_url,
                            artworks_data[i].surfacestateId,
                            artworks_data[i].imageWidth,
                            artworks_data[i].imageHeight,
                            artworks_data[i].position_x * offsetScale - spot_position.x * offsetScale,
                            -artworks_data[i].position_y * offsetScale + spot_position.y * offsetScale,
                            -artworks_data[i].position_z * offsetScale + spot_position.z * offsetScale,
                            artworks_data[i].normal['x'],
                            artworks_data[i].normal['y'],
                            artworks_data[i].normal['z']
                        );
                    }
                });

                // Load Surface Model

            }
        }
    }, 500);

    function assign_object_properties(obj, name, properties) {
        if (properties === undefined) properties = {};
        if (properties.name === undefined) properties.name = name;
        if (properties.ath === undefined) properties.ath = 0;
        if (properties.atv === undefined) properties.atv = 0;
        if (properties.depth === undefined) properties.depth = offset_y;
        if (properties.scale === undefined) properties.scale = 1;
        if (properties.rx === undefined) properties.rx = 0;
        if (properties.ry === undefined) properties.ry = 0;
        if (properties.rz === undefined) properties.rz = 0;
        if (properties.rorder === undefined) properties.rorder = "YXZ";
        if (properties.enabled === undefined) properties.enabled = true;
        if (properties.capture === undefined) properties.capture = true;
        if (properties.onover === undefined) properties.onover = null;
        if (properties.onout === undefined) properties.onout = null;
        if (properties.ondown === undefined) properties.ondown = null;
        if (properties.onup === undefined) properties.onup = null;
        if (properties.onclick === undefined) properties.onclick = null;
        properties.pressed = false;
        properties.hovering = false;

        obj.properties = properties;

        update_object_properties(obj, name);
    }


    function update_object_properties(obj, name) {

        var p = obj.properties;

        var px = p.depth * Math.sin(p.atv * M_RAD) * Math.cos(p.ath * M_RAD);
        var py = p.depth * Math.cos(p.atv * M_RAD);
        var pz = p.depth * Math.sin(p.atv * M_RAD) * Math.sin(p.ath * M_RAD);

        if (name === "artwork") obj.position.set(px, py, pz);
        else obj.position.set(px, offset_y, pz);
        obj.rotation.set(p.rx * M_RAD, p.ry * M_RAD, p.rz * M_RAD, p.rorder);
        obj.scale.set(p.scale, p.scale, p.scale);
        obj.updateMatrix();
    }

    function getSize(object) {
        let measure = new THREE.Vector3();
        var boundingBox = new THREE.Box3().setFromObject(object);

        let width = boundingBox.max.x - boundingBox.min.x;
        let height = boundingBox.max.y - boundingBox.min.y;
        let depth = boundingBox.max.z - boundingBox.min.z;

        return { width: width, height: height, depth: depth };
    }

    var label = document.createElement('div');

    function createLabel(object) {
        label.innerHTML = "";
        label.style.position = 'absolute';
        label.style.width = 300 + "px";
        label.style.height = 100 + "px";
        label.id = "model_label"

        const container = document.getElementById('krpanoSWFObject');
        let textPos = object.position.project(camera);

        var widthHalf = window.krpano.area.pixelwidth / 2;
        var heightHalf = window.krpano.area.pixelheight / 2;
        var style = 'translate(-50%,-50%) translate(' + (textPos.x * widthHalf + widthHalf + 200) + 'px,' + (- textPos.y * heightHalf + heightHalf + 100) + 'px)';
        label.style.transform = style;

        var animate_btn = document.createElement('button');
        animate_btn.classList.add('btn');
        animate_btn.style.borderRadius = 30 + "px";
        animate_btn.style.borderColor = "white";

        animate_btn.onclick = function () {
            window.isAnimate = !window.isAnimate;
            object.userData.changed = true;
        }

        var anim_icon = document.createElement('i');
        anim_icon.style.scale = 1.5;
        anim_icon.style.padding = 8 + "px";
        anim_icon.classList.add('fa');
        anim_icon.classList.add('fa-refresh');

        var delete_btn = document.createElement('button');
        delete_btn.classList.add('btn');
        delete_btn.style.borderRadius = 30 + "px";
        delete_btn.style.borderColor = "white";

        var delete_icon = document.createElement('i');
        delete_icon.style.scale = 1.5;
        delete_icon.style.padding = 8 + "px";
        delete_icon.classList.add('fa');
        delete_icon.classList.add('fa-trash-o');

        delete_btn.onclick = function () {
            var request_data = {
                'layout_id': layout_id,
                'sculpture_id': object.userData.model.userData.sculpture_id
            }
            $.ajax({
                url: '<?php echo route('sculpture_delete'); ?>',
                type: 'POST',
                data: request_data,
                success: function (response) {

                },
                error: function (xhr) {

                }
            });
            scene.remove(object.userData.model);
            scene.remove(object);
            scene.remove(object.userData.gizmo);
            label.innerHTML = "";
            sculpture_id_list.splice(sculpture_id_list.indexOf(object.userData.model.userData.sculpture_id), 1);
        }

        var save_btn = document.createElement('button');
        save_btn.classList.add('btn');
        save_btn.style.borderRadius = 30 + 'px';
        save_btn.style.borderColor = 'white';

        var save_icon = document.createElement('i');
        save_icon.style.scale = 1.5;
        save_icon.style.padding = 8 + "px";
        save_icon.classList.add('fa');
        save_icon.classList.add('fa-save');

        save_btn.onclick = function () {
            object.userData.changed = false;
            label.innerHTML = '';
            var request_data = {
                'layout_id': layout_id,
                'sculpture_id': object.userData.model.userData.sculpture_id,
                'model_id': object.userData.model.userData.id,
                'position_x': object.userData.model.position.x + offset_x,
                'position_y': object.userData.model.position.y + offset_y,
                'position_z': object.userData.model.position.z - offset_z,
                'rotation_x': object.userData.model.rotation.x,
                'rotation_y': object.userData.model.rotation.y,
                'rotation_z': object.userData.model.rotation.z,
            }
            $.ajax({
                url: '<?php echo route('sculpture_save'); ?>',
                type: 'POST',
                data: request_data,
                success: function (response) {

                },
                error: function (xhr) {

                }
            })
        }

        container.append(label);

        label.append(animate_btn);
        label.append(delete_btn);
        label.append(save_btn);
        animate_btn.append(anim_icon);
        delete_btn.append(delete_icon);
        save_btn.append(save_icon);

        object.position.copy(object.userData.model.position);
    }

    function loadTemp(object, position, rotation_x, rotation_y, rotation_z, temp_model_url) {
        var temp;
        const loader = new THREE.GLTFLoader();
        const dracoLoader = new THREE.DRACOLoader();
        loader.setDRACOLoader(dracoLoader);

        loader.load(temp_model_url, function (gltf) {
            temp = gltf.scene;
            temp.traverse((obj) => {
                if (obj instanceof THREE.Mesh) {
                    obj.name = "interaction-model";
                    obj.material = new THREE.MeshBasicMaterial({ color: 0x00ffff, visible: false, transparent: true, opacity: 0.2 })
                    obj.userData.model = object
                }
            });

            scene.add(temp);

            assign_object_properties(temp, "temp", {
                ath: position.phi,
                atv: position.theta,
                depth: position.r,
                rx: rotation_x * 180 / Math.PI,
                ry: rotation_y * 180 / Math.PI,
                rz: rotation_z * 180 / Math.PI,
                scale: sculptureScale,
                onup: function (obj) { createLabel(obj) }
            });

            temp.userData.model = object;
            object.userData.temp = temp;
        });
    }

    function addTemp(object, temp_model_url) {
        var temp = null;
        const loader = new THREE.GLTFLoader();
        const dracoLoader = new THREE.DRACOLoader();
        loader.setDRACOLoader(dracoLoader);

        var add_model_position = findAddModelPosition();
        var spherical_position = cartesianToSpherical(-add_model_position.x * offsetScale, offset_y, -add_model_position.z * offsetScale);

        loader.load(temp_model_url, function (gltf) {
            temp = gltf.scene;
            temp.traverse((obj) => {
                if (obj instanceof THREE.Mesh) {
                    obj.name = "interaction-model";
                    obj.material = new THREE.MeshBasicMaterial({ color: 0x00ffff, visible: false, transparent: true, opacity: 0.2 })
                    obj.userData.model = object;
                }
            });

            scene.add(temp);

            assign_object_properties(temp, "temp", {
                ath: spherical_position.phi,
                atv: spherical_position.theta,
                depth: spherical_position.r,
                rz: -180,
                scale: tourScale,
                onup: function (obj) { createLabel(obj) }
            });

            temp.userData.model = object;
            temp.userData.changed = true;
            object.userData.temp = temp;
        });
    }

    function handleImageClick(imageId) {
        var sculp_id = 1;
        while (true) {
            if (sculpture_id_list.includes(sculp_id))
                sculp_id++;
            else {
                add_model(sculp_id, imageId);
                sculpture_id_list.push(sculp_id);
                return;
            }
        }
    }

    function add_model(sculp_id, imageId) {
        const loader = new THREE.GLTFLoader();
        const dracoLoader = new THREE.DRACOLoader();
        loader.setDRACOLoader(dracoLoader);

        var model = null;

        // Check if sculptureUrls exists and has the imageId
        if (!sculptureUrls[imageId]) {
            console.error('No sculpture URL found for imageId:', imageId);
            return;
        }

        var sculpture_url = sculptureUrls[imageId].sculpture;
        var temp_url = sculptureUrls[imageId].interaction;

        var add_model_position = findAddModelPosition();
        var spherical_position = cartesianToSpherical(-add_model_position.x * offsetScale, offset_y, -add_model_position.z * offsetScale);

        loader.load(sculpture_url, async function (gltf) {
            model = gltf.scene;
            model.castShadow = true;
            model.receiveShadow = true;

            if (!tour_is_shared) {
                addTemp(model, temp_url);
                model.traverse((obj) => {
                    if (obj instanceof THREE.Mesh) {
                        obj.name = "sculpture-model";
                    }
                });
            }

            scene.add(model);

            model.userData.id = imageId;
            model.userData.sculpture_id = sculp_id;

            assign_object_properties(model, "model", {
                ath: spherical_position.phi,
                atv: spherical_position.theta,
                depth: spherical_position.r,
                rz: -180,
                scale: sculptureScale
            });
        });
    }

    function load_model(sculp_id, imageId, position_x, position_y, position_z, rotation_x, rotation_y, rotation_z) {
        const loader = new THREE.GLTFLoader();
        const dracoLoader = new THREE.DRACOLoader();
        loader.setDRACOLoader(dracoLoader);

        var model = null;

        // Check if sculptureUrls exists and has the imageId
        if (!sculptureUrls[imageId]) {
            console.error('No sculpture URL found for imageId:', imageId);
            return;
        }

        var sculpture_url = sculptureUrls[imageId].sculpture;
        var temp_url = sculptureUrls[imageId].interaction;
        var spherical_position = cartesianToSpherical(position_x, position_y, position_z);

        loader.load(sculpture_url, function (gltf) {
            model = gltf.scene;
            model.castShadow = true;
            model.receiveShadow = true;

            if (!tour_is_shared) {
                loadTemp(model, spherical_position, rotation_x, rotation_y, rotation_z, temp_url);
                model.traverse((obj) => {
                    if (obj instanceof THREE.Mesh) {
                        obj.name = "sculpture-model";
                    }
                });
            }

            scene.add(model);

            model.userData.id = imageId;
            model.userData.sculpture_id = sculp_id;

            assign_object_properties(model, "model", {
                ath: spherical_position.phi,
                atv: spherical_position.theta,
                depth: spherical_position.r,
                rx: rotation_x * 180 / Math.PI,
                ry: rotation_y * 180 / Math.PI,
                rz: rotation_z * 180 / Math.PI,
                scale: sculptureScale,
            });
        });
    }


    function loadSurfaces(surface_id, width, height, position_x, position_y, position_z, normal_x, normal_y, normal_z) {
        var spherical_position = cartesianToSpherical(position_x, position_y, position_z);
        const geometry = new THREE.PlaneGeometry(width, height);
        geometry.translate(-width / 2, height / 2, 0);
        const material = new THREE.MeshBasicMaterial({ color: 0xFFC0CB, transparent: true, opacity: 0.5, visible: false, side: THREE.DoubleSide });
        const planeMesh = new THREE.Mesh(geometry, material);
        planeMesh.position.set(position_x, position_y, position_z);

        // Create normal vector and calculate rotation
        const normal = new THREE.Vector3(normal_x, normal_y, normal_z).normalize();
        // Default plane normal (facing forward)
        const defaultNormal = new THREE.Vector3(0.001, 0, 1);

        // Calculate rotation axis and angle
        const rotationAxis = new THREE.Vector3();
        rotationAxis.crossVectors(defaultNormal, normal);
        rotationAxis.normalize();

        const rotationAngle = Math.acos(defaultNormal.dot(normal));

        // Create quaternion for the rotation
        const quaternion = new THREE.Quaternion();
        quaternion.setFromAxisAngle(rotationAxis, rotationAngle);

        // Apply the rotation
        planeMesh.setRotationFromQuaternion(quaternion);

        // Convert quaternion to Euler angles
        const euler = new THREE.Euler();
        euler.setFromQuaternion(quaternion);

        // Calculate the correct ry angle for krpano
        // This converts the rotation to match krpano's coordinate system
        const ry = 180- Math.atan2(normal.x, normal.z) * (180 / Math.PI);

        planeMesh.userData.surface_id = surface_id;
        planeMesh.userData.layout_id = layout_id;
        planeMesh.userData.spot_id = spot_id;
        planeMesh.userData.type = "surface";
        surface_meshes.push(planeMesh);
        scene.add(planeMesh);

        assign_object_properties(planeMesh, "artwork", {
            ath: spherical_position.phi,
            atv: spherical_position.theta,
            depth: spherical_position.r,
            ry: ry,
            scale: tourScale,
        });
    }

    function load_artModels(art_id, surface_id, image_url, surfacestateId, imageWidth, imageHeight, position_x, position_y, position_z, normal_x, normal_y, normal_z) {
        // Load a texture (image)
        const textureLoader = new THREE.TextureLoader();

        var spherical_position = cartesianToSpherical(position_x, position_y, position_z);

        textureLoader.load(image_url, (texture) => {
            // Flip the texture horizontally and vertically
            texture.flipY = true; // Flips vertically
            texture.center.set(0.5, 0.5); // Set rotation center point
            texture.rotation = Math.PI; // Rotate 180 degrees to flip horizontally
            // Create a geometry with the same aspect ratio
            const boxDepth = 0.04; // Adjust as needed
            const geometry = new THREE.BoxGeometry(imageWidth, imageHeight, boxDepth);
            geometry.translate(-imageWidth / 2, imageHeight / 2, 0);

            const materials = [
                new THREE.MeshBasicMaterial({ color: 0xffffff, side: THREE.DoubleSide, transparent: true, alphaTest: 0.5 }), // right
                new THREE.MeshBasicMaterial({ color: 0xffffff, side: THREE.DoubleSide, transparent: true, alphaTest: 0.5 }), // left
                new THREE.MeshBasicMaterial({ color: 0xffffff, side: THREE.DoubleSide, transparent: true, alphaTest: 0.5 }), // top
                new THREE.MeshBasicMaterial({ color: 0xffffff, side: THREE.DoubleSide, transparent: true, alphaTest: 0.5 }), // bottom
                new THREE.MeshBasicMaterial({ map: texture, side: THREE.DoubleSide, transparent: true, alphaTest: 0.5 }),    // front
                new THREE.MeshBasicMaterial({ color: 0xffffff, side: THREE.DoubleSide, transparent: true, alphaTest: 0.5 })  // back
            ];

            const box = new THREE.Mesh(geometry, materials);
            box.userData.type = "artwork";
            box.userData.art_id = art_id;
            box.userData.surfacestateId = surfacestateId;
            box.userData.surface_id = surface_id;
            box.userData.layout_id = layout_id;
            box.userData.spot_id = spot_id;
            scene.add(box);

            // Create normal vector and calculate rotation
            const normal = new THREE.Vector3(normal_x, normal_y, normal_z).normalize();
            // Default plane normal (facing forward)
            const defaultNormal = new THREE.Vector3(0.001, 0, 1);
            const rotationAxis = new THREE.Vector3().crossVectors(defaultNormal, normal).normalize();
            const rotationAngle = Math.acos(defaultNormal.dot(normal));
            const quaternion = new THREE.Quaternion().setFromAxisAngle(rotationAxis, rotationAngle);
            box.setRotationFromQuaternion(quaternion);

            // Apply the rotation
            // Convert quaternion to Euler angles for the assign_object_properties function
            const euler = new THREE.Euler();
            euler.setFromQuaternion(quaternion);

            const ry = 180- Math.atan2(normal.x, normal.z) * (180 / Math.PI);

            assign_object_properties(box, "artwork", {
                ath: spherical_position.phi,
                atv: spherical_position.theta,
                depth: spherical_position.r,
                ry: ry,
                scale: tourScale,
            });
        }
        );
    }

    function findAddModelPosition() {
        var position = new THREE.Vector3();
        camera.getWorldDirection(position);
        return position;
    }

    function cartesianToSpherical(x, y, z) {
        let r = Math.sqrt(x * x + y * y + z * z);
        let theta = Math.acos(y / r);
        let phi = Math.atan2(z, x);

        theta = theta * 180 / Math.PI;
        phi = phi * 180 / Math.PI;

        return { r: r, theta: theta, phi: phi };
    }


    document.addEventListener('DOMContentLoaded', function () {
        const images = document.querySelectorAll('.image-list-item');
        images.forEach(function (image) {
            image.addEventListener('click', function () {
                const imageId = this.getAttribute('data-image-id');
                handleImageClick(imageId);
            });
        });
    });

    document.addEventListener('livewire:init', () => {
        Livewire.on('layoutDeleted', (event) => {
            let currentLayoutId = new URL(window.location.href).searchParams.get("layout_id");
            if (currentLayoutId == event.layoutId) {
                window.location = @js(route('dashboard'));
            }
        });
    });

    // Landscape Orientation Prompt Functionality
    function checkOrientation() {
        const landscapePrompt = document.getElementById('landscapePrompt');
        if (!landscapePrompt) return;

        const isMobile = window.innerWidth <= 767;
        const isPortrait = window.innerHeight > window.innerWidth;

        if (isMobile && isPortrait) {
            landscapePrompt.classList.remove('hidden');
        } else {
            landscapePrompt.classList.add('hidden');
        }
    }

    // Check orientation on page load
    document.addEventListener('DOMContentLoaded', checkOrientation);

    // Check orientation on window resize and orientation change
    window.addEventListener('resize', checkOrientation);
    window.addEventListener('orientationchange', () => {
        // Add a small delay to ensure the orientation change is complete
        setTimeout(checkOrientation, 100);
    });

    // Button click functions
    function toggleShare() {
        @if($layout && !$tour_is_shared)
            window.open('{{ route('share.index', ['layout_id' => $layout?->id]) }}', '_blank');
        @else
            alert('Share functionality is not available in this context.');
        @endif
    }

    function toggleArtworkCollection() {
        @if(!$tour_is_shared)
            window.open('{{ route('inventory.index') }}', '_blank');
        @else
            alert('Artwork Collection is not available for shared tours.');
        @endif
    }

    function toggleSculptureList() {
        @if($project && !$tour_is_shared && $tourModel)
            const offcanvas = new bootstrap.Offcanvas(document.getElementById('offcanvasExample'));
            offcanvas.show();
        @else
            alert('Sculpture List is not available in this context.');
        @endif
    }

    function toggleTracker() {
        @auth
            @if(user()?->can('perform-admin-actions'))
                const currentUrl = new URL(window.location.href);
                const currentTracker = currentUrl.searchParams.get('tracker');
                const newTracker = currentTracker === '1' ? '0' : '1';
                currentUrl.searchParams.set('tracker', newTracker);
                window.location.href = currentUrl.toString();
            @else
                alert('You do not have permission to access the tracker.');
            @endif
        @else
            alert('You must be logged in to access the tracker.');
        @endauth
    }

</script>
@endsection
