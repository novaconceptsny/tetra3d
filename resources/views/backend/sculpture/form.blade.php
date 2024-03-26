@extends('layouts.backend')

@section('title_right')
    <x-backend::layout.breadcrumbs>
        <x-backend::layout.breadcrumb-item text="Sculptures" :route="route('backend.sculptures.index')" />
        <x-backend::layout.breadcrumb-item text="Form" :active="true" />
    </x-backend::layout.breadcrumbs>
@endsection

@section('content')
    @php($sculpture = $sculpture ?? null)
    @php($edit_mode = (bool)$sculpture)
    @php($heading = $heading ?? ( $sculpture ? __('Edit Sculpture') : __('Add New Sculpture') ))

    <div class="card mb-3">
        <div class="card-header">
            <h5 class="mb-0">{{ $heading }}</h5>
        </div>
        <div class="card-body">
            <form action="{{ $route }}" method="POST" enctype="multipart/form-data" id='sculpture_form'>
                @csrf
                @method($method)

                <div class="row">
                    <div class="col-3 sculpture-left">    
                        <div class="row">
                            <div class="col-12  mb-2">
                                <h5>{{ __('Import Sculpture') }}</h5>
                                <input type='file' id='sculpture-model-input' accept=".glb" hidden name='sculpture' required></input>
                                <button class="btn btn-primary mb-2 sculpture-choose-button" type='button'>
                                    <label for='sculpture-model-input'>Choose Sculpture Model</label>
                                </button>
                                <div id='sculpture-model-name'>Empty</div>
                            </div>
                            <div class="col-12 mb-3">
                                <h5>{{ __('Import Thumbnail') }}</h5>
                                <input type='file' id='sculpture-thumbnail-input' accept=".png" hidden name='thumbnail'></input>
                                <input id='sculpture-thumbnail-canvas-input' hidden name='thumbnail-canvas'></input>
                                <button class="btn btn-primary mb-2 sculpture-choose-button" type='button'>
                                    <label for='sculpture-thumbnail-input'>Choose Thumbnail Image</label>
                                </button>
                                <div id='sculpture-thumbnail-name'>Empty</div>
                            </div>
                            <div class="col-12 mb-3">
                                <h5>{{ __('Import Interaction Model') }}</h5>
                                <input type='file' id='sculpture-interaction-input' accept=".glb" hidden name='interaction'></input>
                                <button class="btn btn-primary mb-2 sculpture-choose-button" type='button'>
                                    <label for='sculpture-interaction-input'>Choose Interaction Model</label>
                                </button>
                                <div id='sculpture-interaction-name'>Empty</div>
                            </div>
                            <x-backend::inputs.select col="col-12 mb-3" id="sculpture-collection-select" name="artwork_collection_id" label="Collection" required>
                                @foreach($artwork_collections as $collection)
                                    <x-backend::inputs.select-option
                                        :value="$collection->id" :text="$collection->name"
                                        :selected="$sculpture?->artwork_collection_id"
                                    />
                                @endforeach
                            </x-backend::inputs.select>
                            <x-backend::inputs.text col="col-12 mb-3" id="sculpture_name" name="name" value="{!! $sculpture?->name !!}" label="Title" required/>
                            <x-backend::inputs.text col="col-12 mb-3" id="sculpture_artist" name="artist" value="{{ $sculpture?->artist }}" required/>
                            <x-backend::inputs.text col="col-4 mb-3" readonly='readonly' name="data.length" id="data-length" value="{{ $sculpture?->data->length }}" label="Length"/>
                            <x-backend::inputs.text col="col-4 mb-3" readonly='readonly' name="data.width" id="data-width" value="{{ $sculpture?->data->width }}" label="Width"/>
                            <x-backend::inputs.text col="col-4 mb-3" readonly='readonly' name="data.height" id="data-height" value="{{ $sculpture?->data->height }}" label="Height"/>
                            <div class="col-12 text-end">
                                <button class="btn btn-primary" type="submit" id='sculpture_form_submit'>
                                    {{ $submit_text ?? ( $edit_mode ? __('Update') : __('Create') ) }}
                                </button>
                            </div>

                        </div>
                    </div>
                    <div class="col-9 sculpture-right" id="sculpture-canvas-div">
                        <canvas id="sculpture-canvas"></canvas>
                        <button class="btn btn-primary get-sculpture-thumbnail" id='get-sculpture-thumbnail' type='button'>
                            <i class="fas fa-camera"></i>
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('backend/css/media-library.css') }}">
    <style>
        .sculpture-left {
            min-height: 500px;
            overflow-y: auto;
            overflow-x: hidden;
        }

        #sculpture-canvas {
            width: 100%;
            aspect-ratio: 4 / 3;
        }

        .sculpture-choose-button{
            width: 100%;
        }

        .get-sculpture-thumbnail {
            width: 50px;
            height: 50px;
            font-size: 20px;
            position: absolute;
            top: 30px;
            right: 40px;
        }
        .sculpture-right {
            position: relative;
        }
    </style>
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
        
<script type="module">
    let container, stats, controls, isMouseDown;
    let camera, cameraTarget, scene, renderer;

    import * as THREE from 'three';
    import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
    import { OrbitControls } from 'three/addons/controls/OrbitControls';

    var artwork_collection = @json($artwork_collections);

    document.getElementById('sculpture-model-input').addEventListener('change', function(e) {
        if (e.target.files[0]) {
            var url = URL.createObjectURL(e.target.files[0]);
            GLTFLoad(url);

            document.getElementById('sculpture-model-name').innerHTML = e.target.files[0].name;
        }
    });

    document.getElementById('sculpture-thumbnail-input').addEventListener('change', function(e) {
        if (e.target.files[0]) {
            var url = URL.createObjectURL(e.target.files[0]);
            document.getElementById('sculpture-thumbnail-name').innerHTML = e.target.files[0].name;
        }
    });
    
    document.getElementById('sculpture-interaction-input').addEventListener('change', function(e) {
        if (e.target.files[0]) {
            var url = URL.createObjectURL(e.target.files[0]);
            document.getElementById('sculpture-interaction-name').innerHTML = e.target.files[0].name;
        }
    });

    document.getElementById('get-sculpture-thumbnail').addEventListener('click', function(e) {
        var thumbnailURL = renderer.domElement.toDataURL("image/png");
        document.getElementById('sculpture-thumbnail-name').innerHTML = 'Thumbnail_Image_From_Canvas';
        document.getElementById('sculpture-thumbnail-canvas-input').value = thumbnailURL;
    });
    
    function init() {
        container = document.getElementById('sculpture-canvas');
    
        camera = new THREE.PerspectiveCamera(75, 4 / 3, 0.1, 1000);
    
        cameraTarget = new THREE.Vector3(0, 0, 0);
    
        scene = new THREE.Scene();
        scene.background = new THREE.Color(0x72645b);
    
        scene.add(new THREE.AmbientLight(0xE5DCDF));
    
        var light = new THREE.AmbientLight(0x404040);
        scene.add(light);
        // if (tourModelPath !== null)
        //     GLTFLoad(tourModelPath);
    
        renderer = new THREE.WebGLRenderer({ canvas: container, antialias: true, preserveDrawingBuffer: true });
        renderer.setPixelRatio(window.devicePixelRatio);
        var canvas_width = $('#sculpture-canvas-div').width();
        renderer.setSize(canvas_width, canvas_width * 3 / 4, false);
        renderer.shadowMap.enabled = true;
        
        controls = new OrbitControls(camera, renderer.domElement);
    }

    function GLTFLoad(full_model_url) {
        var loader = new GLTFLoader();
        var model = null;

        scene.traverse(function (object) {
            if (object.name === 'space-model') {
                scene.remove(object);
            }
        });
    
        loader.load(full_model_url, function (gltf) {
            model = gltf.scene;
            var width = getSize(model).width;
            var height = getSize(model).height;
            var depth = getSize(model).depth;
    
            camera.position.set(0, height / 2, 5);
            controls.target.set(0, height / 2, 0);
            model.rotation.y =  - Math.PI / 2;
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

        document.getElementById('data-length').value = depth;
        document.getElementById('data-height').value = height;
        document.getElementById('data-width').value = width;

        return { width: width, height: height, depth: depth };
    }

    init();
    animate();

    var sculpture = @json($sculpture);

    if (sculpture) {
        document.getElementById('sculpture-model-name').innerHTML = sculpture.sculpture_url;
        document.getElementById('sculpture-thumbnail-name').innerHTML = sculpture.image_url;
        document.getElementById('sculpture-interaction-name').innerHTML = sculpture.type;
        var base_url = '<?php echo asset(''); ?>'

        GLTFLoad(base_url + 'storage/sculptures/' + sculpture.sculpture_url);
    }

    $('#sculpture_name').on('keydown', function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });

    $('#sculpture_artist').on('keydown', function(event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });

    $('#sculpture_form_submit').on('click', function(e) {
        e.preventDefault();
        var sculpture_input = document.getElementById('sculpture-model-name').innerHTML;
        var thumbnail_input = document.getElementById('sculpture-thumbnail-name').innerHTML;
        var interaction_input = document.getElementById('sculpture-interaction-name').innerHTML;
        var collection_input = document.getElementById('sculpture-collection-select').value;

        if (sculpture_input === 'Empty') {
            alert('Sculpture Model is not exist');
        } 
        else if (thumbnail_input === 'Empty') {
            alert('Thumbnail Image is not exist');
        } 
        else if (interaction_input === 'Empty') {
            alert('Interaction model is not exist');
        }
        else if (collection_input === '') {
            alert('Please choose Collection');
        }
        else document.getElementById('sculpture_form').submit();
    })
</script>
@endsection

