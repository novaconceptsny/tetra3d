<div class="row" style="display: flex; align-items: stretch; ">
    <div class="col-3 model-import-left" id='model-import-left' style="min-height: 500px; overflow-y: auto; overflow-x: hidden;" wire:ignore>
        <div class="row g-3 ">
            <div class="col-12">
                <h5>{{ __('Model') }}</h5>
                <input type='file' wire:model="tourModel" id='tour-model-input' accept=".glb" hidden></input>
                <button class="btn btn-primary tour-model-input" type='button' style="width: 100%;">
                    <label for='tour-model-input'>Choose 3D Model</label>
                </button></br></br>
                <div id='tour-model-name'></div>
            </div>

            <div class="col-12">
                <h5>{{ __('Intersection Surface') }}</h5>
                <input type='file' wire:model="surfaceModel" id='surface-model-input' accept=".glb" hidden></input>
                <button class="btn btn-primary surface-model-input" type='button' style="width: 100%;">
                    <label for='surface-model-input'>Choose Surface Model</label>
                </button></br></br>
                <div id='surface-model-name'></div>
            </div>

            <div class="row g-3 mt-2">
                <div class="col-12"><h5>{{ __('Spots') }}</h5></div>

                @foreach($tour->spots as $spot)
                    <div class="col-12" style="display: flex; gap: 30px;">
                        <h7>{{ $spot->friendly_name }}</h7>
                        <label class="switch">
                            <input type="checkbox" class="spot-toggle" data-spot-id="{{ $spot->id }}" checked>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="row g-2">
                        <x-backend::inputs.text
                            col="col-4" name='{{ "spotsPosition.{$spot->id}.x" }}'
                            wire:model.live="spotsPosition.{{ $spot->id }}.x" label='{{ "X (invert Y in 3ds Max)" }}'
                        />
                        <x-backend::inputs.text
                            col="col-4" name='{{ "spotsPosition.{$spot->id}.y" }}'
                            wire:model.live="spotsPosition.{{ $spot->id }}.y" label='{{ "Y (Z in 3ds Max)" }}'
                        />
                        <x-backend::inputs.text
                            col="col-4" name='{{ "spotsPosition.{$spot->id}.z" }}'
                            wire:model.live="spotsPosition.{{ $spot->id }}.z" label='{{ "Z (invert X in 3ds Max)" }}'
                        />
                    </div>
                @endforeach

            </div>

            <div class="row g-3 mt-2">
                <div class="col-12"><h5>{{ __('Surfaces') }}</h5></div>
                @foreach($tour->surfaces as $surface)
                    <div class="col-12" style="display: flex; gap: 30px;">
                        <h7>{{ $surface->friendly_name }}</h7>
                        <label class="switch">
                            <input type="checkbox" class="surface-toggle" data-surface-id="{{ $surface->id }}" checked>
                            <span class="slider round"></span>
                        </label>
                    </div>
                    <div class="col-12"><h7>Normal Vector</h7></div>
                    <div class="row g-2">
                        <x-backend::inputs.text
                            col="col-4" name='{{ "surfaceArray.{$surface->id}.normalvector.x" }}'
                            wire:model.live="surfaceArray.{{ $surface->id }}.normalvector.x" label='{{ "X (invert Y in 3ds Max)" }}'
                        />
                        <x-backend::inputs.text
                            col="col-4" name='{{ "surfaceArray.{$surface->id}.normalvector.y" }}'
                            wire:model.live="surfaceArray.{{ $surface->id }}.normalvector.y" label='{{ "Y (Z in 3ds Max)" }}'
                        />
                        <x-backend::inputs.text
                            col="col-4" name='{{ "surfaceArray.{$surface->id}.normalvector.z" }}'
                            wire:model.live="surfaceArray.{{ $surface->id }}.normalvector.z" label='{{ "Z (invert X in 3ds Max)" }}'
                        />
                    </div>
                    <div class="col-12"><h7>Start Point</h7></div>
                    <div class="row g-2">
                        <x-backend::inputs.text
                            col="col-4" name='{{ "surfaceArray.{$surface->id}.start_pos.x" }}'
                            wire:model.live="surfaceArray.{{ $surface->id }}.start_pos.x" label='{{ "X (invert Y in 3ds Max)" }}'
                        />
                        <x-backend::inputs.text
                            col="col-4" name='{{ "surfaceArray.{$surface->id}.start_pos.y" }}'
                            wire:model.live="surfaceArray.{{ $surface->id }}.start_pos.y" label='{{ "Y (Z in 3ds Max)" }}'
                        />
                        <x-backend::inputs.text
                            col="col-4" name='{{ "surfaceArray.{$surface->id}.start_pos.z" }}'
                            wire:model.live="surfaceArray.{{ $surface->id }}.start_pos.z" label='{{ "Z (invert X in 3ds Max)" }}'
                        />
                    </div>
                    <div class="col-12"><h7>Size</h7></div>
                    <div class="row g-2">
                        <x-backend::inputs.text
                            col="col-4" name='{{ "surfaceArray.{$surface->id}.width" }}'
                            wire:model.live="surfaceArray.{{ $surface->id }}.width" label='{{ "Width" }}'
                        />
                        <x-backend::inputs.text
                            col="col-4" name='{{ "surfaceArray.{$surface->id}.height" }}'
                            wire:model.live="surfaceArray.{{ $surface->id }}.height" label='{{ "Height" }}'
                        />
                    </div>
                @endforeach

            </div>

            <div class="text-end">
                <button id="tour-model-update" class="btn btn-primary mb-3" wire:click="update" type="button">
                    {{ __('Update') }}
                </button>
            </div>
        </div>
    </div>
    <div class="col-9" id="tour-model-import" style="box-sizing: border-box;" wire:ignore>
        <canvas id="tour-model-import-canvas" style="width: 100%; aspect-ratio: 4 / 3;"></canvas>
    </div>
</div>

@section('scripts')
<script type="importmap">
    {
        "imports": {
            "three": "https://unpkg.com/three@0.161.0/build/three.module.js",
            "three/addons/": "https://unpkg.com/three@0.161.0/examples/jsm/"
        }
    }
</script>

<script type="module">
    let container, stats, controls, isMouseDown;
    let camera, cameraTarget, scene, renderer, model;

    import * as THREE from 'three';
    import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
    import { DRACOLoader } from 'three/addons/loaders/DRACOLoader.js';
    import { OrbitControls } from 'three/addons/controls/OrbitControls'

    var spotsPosition = @json($spotsPosition);
    var surfaceArray = @json($surfaceArray);
    var tourModel = @json($tourModel);
    var tourModelPath = @json($tourModelPath);
    var surfaceModel = @json($surfaceModel);
    var surfaceModelPath = @json($surfaceModelPath);
    var surfaceMeshes = [];
    var spotMeshes = [];

    for (const key in spotsPosition) {
        let x_input = document.getElementById('spotsPosition_' + key + '_x');
        x_input.onchange = function() {
            updatePosition(key, 'x', this.value);
        }
        let y_input = document.getElementById('spotsPosition_' + key + '_y');
        y_input.onchange = function() {
            updatePosition(key, 'y', this.value);
        }
        let z_input = document.getElementById('spotsPosition_' + key + '_z');
        z_input.onchange = function() {
            updatePosition(key, 'z', this.value);
        }
    }

    for (const key in surfaceArray) {
        let x_input = document.getElementById('surfaceArray_' + key + '_start_pos_x');
        x_input.onchange = function() {
            updateSurfaces(key, 'start_pos_x', this.value);
        }
        let y_input = document.getElementById('surfaceArray_' + key + '_start_pos_y');
        y_input.onchange = function() {
            updateSurfaces(key, 'start_pos_y', this.value);
        }
        let z_input = document.getElementById('surfaceArray_' + key + '_start_pos_z');
        z_input.onchange = function() {
            updateSurfaces(key, 'start_pos_z', this.value);
        }
    }

    document.getElementById('tour-model-name').innerHTML = tourModel;
    document.getElementById('surface-model-name').innerHTML = surfaceModel;

    document.getElementById('tour-model-input').addEventListener('change', function(e) {
        if (e.target.files[0]) {
            var url = URL.createObjectURL(e.target.files[0]);
            GLTFLoad(url);
            document.getElementById('tour-model-name').innerHTML = e.target.files[0].name;
        }
    })

    document.getElementById('surface-model-input').addEventListener('change', function(e) {
        if (e.target.files[0]) {
            var url = URL.createObjectURL(e.target.files[0]);
            document.getElementById('surface-model-name').innerHTML = e.target.files[0].name;
        }
    })

    document.querySelectorAll('.surface-toggle').forEach(checkbox => {
        checkbox.addEventListener('change', (event) => {
            const surfaceId = event.target.getAttribute('data-surface-id');
            const planeMesh = surfaceMeshes[surfaceId];

            // Toggle visibility based on checkbox state
            if (planeMesh) {
                planeMesh.visible = event.target.checked;
            }
        });
    });

    document.querySelectorAll('.spot-toggle').forEach(checkbox => {
        checkbox.addEventListener('change', (event) => {
            const spotId = event.target.getAttribute('data-spot-id');
            const spotMesh = spotMeshes[spotId];

            // Toggle visibility based on checkbox state
            if (spotMesh) {
                spotMesh.visible = event.target.checked;
            }
        });
    });


    function updatePosition(key, axis, value) {
        scene.traverse(function(object) {
            if (object.name === key) {
                if (axis === 'x') object.position.x = value;
                if (axis === 'y') object.position.y = value;
                if (axis === 'z') object.position.z = value;
            }
        });
    }

    function updateSurfaces(key, axis, value) {
        scene.traverse(function(object) {
            if (object.name === key) {
                if (axis === 'start_pos_x') object.position.x = value;
                if (axis === 'start_pos_y') object.position.y = value;
                if (axis === 'start_pos_z') object.position.z = value;
            }
        });
    }

    function init() {
        container = document.getElementById('tour-model-import-canvas');
    
        camera = new THREE.PerspectiveCamera(75, 4 / 3, 0.1, 1000);
    
        cameraTarget = new THREE.Vector3(0, 0, 0);
    
        scene = new THREE.Scene();
        scene.background = new THREE.Color(0x72645b);
    
        scene.add(new THREE.AmbientLight(0xE5DCDF));
    
        var light = new THREE.AmbientLight(0x404040);
        scene.add(light);
        if (tourModelPath !== null)
            GLTFLoad(tourModelPath);
    
        renderer = new THREE.WebGLRenderer({ canvas: container, antialias: true });
        renderer.setPixelRatio(window.devicePixelRatio);
        var canvas_width = $('#tour-model-import').width();
        renderer.setSize(canvas_width, canvas_width * 3 / 4, false);
        renderer.shadowMap.enabled = true;
        
        controls = new OrbitControls(camera, renderer.domElement);
        controls.maxPolarAngle = Math.PI / 2;
    }

    function GLTFLoad(full_model_url) {
        var loader = new GLTFLoader();
        var dracoLoader = new DRACOLoader();
        loader.setDRACOLoader(dracoLoader);
        
        scene.traverse(function (object) {
            if (object.name === 'space-model') {
                scene.remove(object);
            }
        });
    
        loader.load(full_model_url, function (gltf) {
            model = gltf.scene;
            model.traverse((obj) => {
                if(obj instanceof THREE.Mesh){
                    obj.material = new THREE.MeshBasicMaterial({color: 0x00ff00, transparent: true, opacity: 0.5})
                    }
                }
            )
            var width = getSize(model).width;
            var height = getSize(model).height;
            var depth = getSize(model).depth;
    
            camera.position.set(width, height, length);
            camera.lookAt(new THREE.Vector3(0, height / 2, 0));
            controls.target.set(0, height / 2, 0);
            model.rotation.y =  Math.PI / 2;

            model.name = "space-model";
            scene.add(model);
        });
    }

    function animate() {
        requestAnimationFrame(animate);
        render();
    }

    function render() {
        const timer = Date.now() * 0.0005;
        renderer.render(scene, camera);
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

    function renderSpots() {
        var spotsPosition = @json($spotsPosition);
        for (const key in spotsPosition) {
            const coneGeometry = new THREE.ConeGeometry(0.1, 0.5, 32);
            const material = new THREE.MeshBasicMaterial({ color: 0xff0000 });
            const coneMesh = new THREE.Mesh(coneGeometry, material);
            coneMesh.rotation.x = - Math.PI;
            coneMesh.position.set(spotsPosition[key]['x'], spotsPosition[key]['y'], spotsPosition[key]['z'])
            coneMesh.name = key;

            spotMeshes[key] = coneMesh;
            scene.add(coneMesh);
        }
    }

    function renderSurfaces() {
        var surfaceArray = @json($surfaceArray);
        for (const key in surfaceArray) {
            const width = surfaceArray[key]['width'];
            const height = surfaceArray[key]['height'];
            let startPos = surfaceArray[key]['start_pos'];
            let normalvector = surfaceArray[key]['normalvector'];
        
            if(width != 0 && height != 0){
                // Convert values of the object from strings to numbers
                startPos = Object.fromEntries(
                    Object.entries(startPos).map(([key, value]) => [key, parseFloat(value)])
                );

                normalvector = Object.fromEntries(
                    Object.entries(normalvector).map(([key, value]) => [key, parseFloat(value)])
                );

                const geometry = new THREE.PlaneGeometry(width, height); 
                geometry.translate(width / 2, -height / 2, 0);
                const material = new THREE.MeshBasicMaterial({ color: 0xFFC0CB, });
                const planeMesh = new THREE.Mesh(geometry, material);

                planeMesh.position.set(startPos['x'], startPos['y'], startPos['z'])
                planeMesh.name = key;
                if(normalvector['x'] == 0 && normalvector['y'] == 0 && normalvector['z'] ==1){
                    planeMesh.rotation.set(0, 0, 0);
                }else if(normalvector['x'] == 0 && normalvector['y'] == 0 && normalvector['z'] ==-1){
                    planeMesh.rotation.set(0, Math.PI, 0);
                }else if(normalvector['x'] == 1 && normalvector['y'] == 0 && normalvector['z'] ==0){
                    planeMesh.rotation.set(0, Math.PI/2, 0);
                }else{
                    planeMesh.rotation.set(0, -Math.PI/2, 0);
                }
                // Store the planeMesh in the surfaceMeshes object with the surface id
                surfaceMeshes[key] = planeMesh;
                scene.add(planeMesh);
            }

        }
    }


    document.getElementById('model_forms-tab').addEventListener('click', function() {
        setTimeout(function() {
            $('[id^="spotsPosition_"]').on('keydown', function(event) {
                if (event.keyCode == 13) {
                    event.preventDefault();
                }
            })

            spotsPosition = @json($spotsPosition);
            tourModel = @json($tourModel);
            tourModelPath = @json($tourModelPath);
        
            var height = $('#tour-model-import').width() * 3 / 4;
            var element = document.getElementById('model-import-left');
            element.style.height = height + 'px';

            if (document.getElementById('tour-model-import-canvas').getAttribute('data-engine') === null){
                init();
                renderSpots();
                renderSurfaces();
                animate();
            }
        }, 1000);
    });

    $('#tour-model-update').on('click', function(e) {
        // e.preventDefault();
        var tour_input = document.getElementById('tour-model-name').innerHTML;
        var surface_input = document.getElementById('surface-model-name').innerHTML;

        if (tour_input === 'Empty') {
            alert('Sculpture Model is not exist');
        } 
        else if (surface_input === 'Empty') {
            alert('Thumbnail Image is not exist');
        } 
        else document.getElementById('sculpture_form').submit();
    })
</script>
@endsection