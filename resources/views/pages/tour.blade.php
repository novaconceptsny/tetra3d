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
    $layout = $layout ?? null;

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
        <x-menu-item text="Sculpture List " icon="fal fa-palette" target="_self" route="#" :visible="!$tour_is_shared"
        data-bs-toggle="offcanvas" data-bs-target="#offcanvasExample" aria-controls="offcanvasExample" />
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

@section('modellist')
<div class="offcanvas offcanvas-start" tabindex="-1" id="offcanvasExample" aria-labelledby="offcanvasExampleLabel">
    <div class="offcanvas-header">
        <h5 class="offcanvas-title" id="offcanvasExampleLabel">Sculpture List</h5>
        <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
    </div>
    <div class="offcanvas-body">
        <div>
            Choose from our exquisite collection of Sculptures to adorn your space.
        </div>
        <div class="mt-3 modellist">
        @foreach($sculptures as $sculpture)
            <div class='sculpture-list'>
                <img class="image-list-item" src="{{asset('storage/sculptures/thumbnails/') . '/' . $sculpture->image_url }}" data-bs-dismiss="offcanvas"
                    alt="Image 1" data-image-id="{{ $sculpture->id }}">
                <div class='sculpture-list-data-container'>
                    <input value='{{ $sculpture->name }}' readonly='readonly' class='sculpture-list-data-name'>
                    <input class='sculpture-list-data-artist' readonly='readonly' value='{{ $sculpture->artist }}'>
                    <input class='sculpture-list-data-dimention' readonly='readonly' value='{{ $sculpture->data }}'>
                </div>
            </div>
        @endforeach
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <script type="importmap">
        {
            "imports": {
                "three": "https://unpkg.com/three@0.161.0/build/three.module.js",
                "three/addons/": "https://unpkg.com/three@0.161.0/examples/jsm/"
            }
        }
    </script>

    <script src="{{ asset("krpano/tour.js") }}"></script>

    <script type="module">
        import * as THREE from 'three';
        import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
        import { DRACOLoader } from 'three/addons/loaders/DRACOLoader.js';

        window.THREE = THREE;
        window.GLTFLoader = GLTFLoader;
        window.DRACOLoader = DRACOLoader;
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
        var offset_x = 0;
        var offset_y = 0;
        var offset_z = 0;
        var planeMesh = null;
        var wallMesh1 = null;
        var wallMesh2 = null;
        var wallMesh3 = null;

        var label = null;
        label = document.createElement('div');
        var layout_id = {{ $layout_id }};
        var sculptures = @json($sculptures);
        var sculpture_data = @json($sculptureData);
        var spot_position = @json($spotPosition);
        var space_model = @json($tourModel);

        console.log(space_model);

        var delay_interval = setInterval(function() {
            if (window.scene !== undefined) {
                clearInterval(delay_interval);
                for (let i = 0; i < sculpture_data.length; i++) {
                    sculpture_id_list.push(sculpture_data[i].sculpture_id);
                    load_model(sculpture_data[i].sculpture_id, 
                        sculpture_data[i].model_id, 
                        sculpture_data[i].position_x - spot_position.x * 30, 
                        sculpture_data[i].position_y - spot_position.y * 30, 
                        sculpture_data[i].position_z + spot_position.z * 30, 
                        sculpture_data[i].rotation_x, 
                        sculpture_data[i].rotation_y, 
                        sculpture_data[i].rotation_z);
                }

                offset_x = spot_position.x * 30;
                offset_y = spot_position.y * 30;
                offset_z = spot_position.z * 30;

                var planeMaterial = new THREE.MeshBasicMaterial({color: 0x00ff00, transparent: true, opacity: 0.2, side: THREE.DoubleSide, colorWrite: false});
                var planeGeometry = new THREE.PlaneGeometry(1500, 1000);
                planeMesh = new THREE.Mesh(planeGeometry, planeMaterial);
                planeMesh.rotation.x = -Math.PI / 2;
                planeMesh.position.y = offset_y;
                scene.add(planeMesh);

                if (space_model == null || space_model.name == 'null')  {
                    alert("No 3D space model");
                } else {

                    var loader = new GLTFLoader();
                    var dracoLoader = new DRACOLoader();
                    loader.setDRACOLoader(dracoLoader);
    
                    var model = null;
                    var model_url = 'storage/3dmodel/' + space_model.name;
                    var asset_url = '<?php echo asset(''); ?>';
                    var full_model_url = asset_url + model_url;
            
                    loader.load(full_model_url, function (gltf) {
                        model = gltf.scene;
                        model.traverse((obj) => {
                            if(obj instanceof THREE.Mesh){
                                obj.material = new THREE.MeshBasicMaterial({color: 0x00ff00, transparent: true, opacity: 0.5})
                                }
                            }
                        )
                        model.rotation.x = -Math.PI;
                        model.rotation.y = Math.PI / 2;
                        model.scale.set(30, 30, 30);
                        model.position.set(-offset_x, offset_y, offset_z);
                        scene.add(model);
                    });
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

            update_object_properties(obj);
        }


        function update_object_properties(obj) {

            var p = obj.properties;

            var px = p.depth * Math.sin(p.atv * M_RAD) * Math.cos(p.ath * M_RAD);
            var py = p.depth * Math.cos(p.atv * M_RAD);
            var pz = p.depth * Math.sin(p.atv * M_RAD) * Math.sin(p.ath * M_RAD);

            obj.position.set(px, offset_y, pz);
            obj.rotation.set(p.rx * M_RAD, p.ry * M_RAD, p.rz * M_RAD, p.rorder);
            obj.scale.set(p.scale, p.scale, p.scale);
            obj.updateMatrix();
        }

        function getSize(object) {
            let measure = new THREE.Vector3();
            var boundingBox = new THREE.Box3().setFromObject(object);
            var size = boundingBox.getSize(measure);
            let width = size.x;
            let height = size.y;
            let depth = size.z;
            return { width: width, height: height, depth: depth };
        }

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
            }

            var anim_icon = document.createElement('i');
            anim_icon.style.scale = 1.5;
            anim_icon.style.padding = 8 + "px";
            anim_icon.classList.add('fa');
            anim_icon.classList.add('fa-refresh');

            var info_btn = document.createElement('button');
            info_btn.classList.add('btn');
            info_btn.style.borderRadius = 30 + "px";
            info_btn.style.borderColor = "white";

            var help_icon = document.createElement('i');
            help_icon.style.scale = 1.5;
            help_icon.style.padding = 8 + "px";
            help_icon.classList.add('fa');
            help_icon.classList.add('fa-info');

            var info_div = document.createElement('div');
            info_btn.onclick = function () {
                info_div.innerHTML = "";
                info_div.innerHTML = "Model Description";
                info_div.style.height = 80 + "px";
                info_div.style.width = 150 + "px";
                info_div.style.backgroundColor = "white";
                info_div.style.color = "black";
                info_div.style.fontSize = 14 + "px";
                info_div.style.borderRadius = 10 + "px";
                label.append(info_div);
            }

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
                label.innerHTML = '';
                console.log("save button click");
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
            label.append(info_btn);
            label.append(delete_btn);
            label.append(save_btn);
            info_btn.append(help_icon);
            animate_btn.append(anim_icon);
            delete_btn.append(delete_icon);
            save_btn.append(save_icon);

            object.position.copy(object.userData.model.position);
        }

        function loadTemp(object, position, rotation_x, rotation_y, rotation_z) {
            var temp;
            var invisibleMat = new THREE.MeshBasicMaterial({ color: 'blue', visible: false, transparent: true, opacity: .3 });
            var width = getSize(object).width;
            var height = getSize(object).height;
            var depth = getSize(object).depth;
            temp = new THREE.Mesh(new THREE.BoxGeometry(width, height, depth), invisibleMat);

            assign_object_properties(temp, "temp", { 
                ath: position.phi, 
                atv: position.theta, 
                depth: position.r, 
                rx: rotation_x * 180 / Math.PI,
                ry: rotation_y * 180 / Math.PI,
                rz: rotation_z * 180 / Math.PI,
                scale: 30,
                onup: function (obj) { createLabel(obj) } 
            });

            scene.add(temp);
            temp.userData.model = object;
            return temp;
        }

        function addTemp(object) {
            var temp;
            var invisibleMat = new THREE.MeshBasicMaterial({ color: 'blue', visible: false, transparent: true, opacity: .3 });
            var width = getSize(object).width;
            var height = getSize(object).height;
            var depth = getSize(object).depth;
            temp = new THREE.Mesh(new THREE.BoxGeometry(width, height, depth), invisibleMat);

            assign_object_properties(temp, "temp", { ath: +0, atv: -90, depth: 70, scale: 30, onup: function (obj) { createLabel(obj) } });

            scene.add(temp);
            temp.userData.model = object;
            return temp;
        }

        function handleImageClick(imageId) {
            var sculp_id = 1;
            while(true) {
                if (sculpture_id_list.includes(sculp_id)) 
                    sculp_id ++;
                else {
                    add_model(sculp_id, imageId);
                    sculpture_id_list.push(sculp_id);
                    return;
                }
            }
        }

        function add_model(sculp_id, imageId) {
            const loader = new GLTFLoader();
            const dracoLoader = new DRACOLoader();
            loader.setDRACOLoader(dracoLoader);
            var model = null;
            var sculpture_url;

            sculptures.forEach(function (sculpture) {
                if (sculpture.id == imageId) {
                    sculpture_url = sculpture.sculpture_url;
                }
            });

            var base_url = '<?php echo asset(''); ?>';
            var model_url = base_url + 'storage/sculptures/' + sculpture_url;

            loader.load(model_url, function (gltf) {
                model = gltf.scene;
                model.castShadow = true;
                model.receiveShadow = true;
                scene.add(model);
                var temp = addTemp(model);
                model.userData.temp = temp;
                model.userData.id = imageId;
                model.userData.sculpture_id = sculp_id;

                assign_object_properties(model, "model", { ath: +0, atv: -90, depth: 70, rz: -180, scale: 30 });
            });
        }

        function load_model(sculp_id, imageId, position_x, position_y, position_z, rotation_x, rotation_y, rotation_z) {
            const loader = new GLTFLoader();
            const dracoLoader = new DRACOLoader();
            var sculpture_url = '';
            loader.setDRACOLoader(dracoLoader);

            sculptures.forEach(function (sculpture) {
                if (sculpture.id == imageId) {
                    sculpture_url = sculpture.sculpture_url;
                }
            });

            var base_url = '<?php echo asset(''); ?>';
            var model_url = base_url + 'storage/sculptures/' + sculpture_url;
            var spherical_position = cartesianToSpherical(position_x, position_y, position_z);

            loader.load(model_url, function (gltf) {
                model = gltf.scene;
                model.castShadow = true;
                model.receiveShadow = true;
                scene.add(model);
                var temp = loadTemp(model, spherical_position, rotation_x, rotation_y, rotation_z);
                model.userData.temp = temp;
                model.userData.id = imageId;
                model.userData.sculpture_id = sculp_id;
                assign_object_properties(model, "model", { 
                    ath: spherical_position.phi, 
                    atv: spherical_position.theta, 
                    depth: spherical_position.r, 
                    rx: rotation_x * 180 / Math.PI,
                    ry: rotation_y * 180 / Math.PI,
                    rz: rotation_z * 180 / Math.PI, 
                    scale: 30,
                });
            });
        }

        function cartesianToSpherical(x, y, z) {
            let r = Math.sqrt(x*x + y*y + z*z);
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
    </script>
@endsection
