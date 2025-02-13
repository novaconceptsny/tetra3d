@extends('layouts.backend')

@section('title_right')
<x-backend::layout.breadcrumbs>
    <x-backend::layout.breadcrumb-item text="Sculptures" :route="route('backend.sculptures.index')" />
    <x-backend::layout.breadcrumb-item text="Form" :active="true" />
</x-backend::layout.breadcrumbs>
@endsection

@section('content')
@php
$sculpture = $sculpture ?? null;
$edit_mode = (bool) $sculpture;
$heading = $heading ?? ($sculpture ? __('Edit Sculpture') : __('Add New Sculpture'));
$sculpture_url = $sculpture ? $sculpture->getFirstMediaUrl('sculpture') : null;
@endphp

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
                        <div class="col-12  mb-3">
                            <h5>{{ __('Sculpture Model') }}</h5>
                            <x-backend::media-attachment name="sculpture" rules="max:20480" id="sculpture-model-upload"
                                :media="$sculpture?->getFirstMedia('sculpture')" />
                        </div>
                        <div class="col-12 mb-3">
                            <h5>{{ __('Thumbnail Image') }}</h5>
                            <x-backend::media-attachment name="thumbnail" rules="max:20480"
                                :media="$sculpture?->getFirstMedia('thumbnail')" />
                        </div>
                        <div class="col-12 mb-3">
                            <h5>{{ __('Interaction Model') }}</h5>
                            <x-backend::media-attachment name="interaction" rules="max:20480"
                                :media="$sculpture?->getFirstMedia('interaction')" />
                        </div>
                        
                        <x-backend::inputs.select col="col-12 mb-3" id="sculpture-company-select"
                            name="company_id" label="Company" required>
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                                <x-backend::inputs.select-option :value="$company->id" :text="$company->name"
                                    :selected="$sculpture?->company_id" />
                            @endforeach
                        </x-backend::inputs.select>

                        <x-backend::inputs.select col="col-12 mb-3" id="sculpture-collection-select"
                            name="artwork_collection_id" label="Collection" required>
                            @if($sculpture)
                                @foreach($artwork_collections->where('company_id', $sculpture->company_id) as $collection)
                                    <x-backend::inputs.select-option :value="$collection->id" :text="$collection->name"
                                        :selected="$sculpture?->artwork_collection_id" />
                                @endforeach
                            @else
                                <option value="">Select Company First</option>
                            @endif
                        </x-backend::inputs.select>
                        <x-backend::inputs.text col="col-12 mb-3" id="sculpture_name" name="name"
                            value="{!! $sculpture?->name !!}" label="Name" required />
                        <x-backend::inputs.text col="col-12 mb-3" id="sculpture_artist" name="artist"
                            value="{{ $sculpture?->artist }}" label="Artist" required />
                        <x-backend::inputs.text col="col-12 mb-3" id="sculpture_type" name="type"
                            value="{{ $sculpture?->type }}" label="Type" required />
                        <x-backend::inputs.text col="col-4 mb-3" readonly='readonly' name="data.length" id="data-length"
                            value="{{ $sculpture?->data->length }}" label="Length" />
                        <x-backend::inputs.text col="col-4 mb-3" readonly='readonly' name="data.width" id="data-width"
                            value="{{ $sculpture?->data->width }}" label="Width" />
                        <x-backend::inputs.text col="col-4 mb-3" readonly='readonly' name="data.height" id="data-height"
                            value="{{ $sculpture?->data->height }}" label="Height" />
                        <div class="col-12 text-end">
                            <button class="btn btn-primary" type="submit" id='sculpture_form_submit'>
                                {{ $submit_text ?? ($edit_mode ? __('Update') : __('Create')) }}
                            </button>
                        </div>

                    </div>
                </div>
                <div class="col-9 sculpture-right" id="sculpture-canvas-div">
                    <canvas id="sculpture-canvas"></canvas>
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

    .sculpture-choose-button {
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

<script>
    function previewImage(input) {
        const previewContainer = input.closest('.d-flex').querySelector('.media-library-thumb');
        const file = input.files[0];
        
        if (file) {
            // Create preview container if it doesn't exist
            if (!previewContainer) {
                const newPreview = document.createElement('div');
                newPreview.className = 'media-library-thumb m-0 me-2';
                input.closest('.d-flex').prepend(newPreview);
            }
            
            const container = previewContainer || input.closest('.d-flex').querySelector('.media-library-thumb');
            
            // Clear existing content
            container.innerHTML = '';
            
            if (file.type.startsWith('image/')) {
                const img = document.createElement('img');
                img.className = 'media-library-thumb-img';
                img.style.objectFit = 'fill';
                img.src = URL.createObjectURL(file);
                img.alt = file.name;
                container.appendChild(img);
                
                const span = document.createElement('span');
                span.className = 'fs-6';
                span.style.whiteSpace = 'nowrap';
                span.textContent = file.name;
                container.appendChild(span);
            }
        }
    }

    // Add event listeners for file inputs
    document.addEventListener('DOMContentLoaded', function() {
        const fileInputs = document.querySelectorAll('input[type="file"]');
        fileInputs.forEach(input => {
            input.addEventListener('change', function() {
                previewImage(this);
            });
        });
    });
</script>

<script type="module">
    let container, stats, controls, isMouseDown;
    let camera, cameraTarget, scene, renderer;

    import * as THREE from 'three';
    import { GLTFLoader } from 'three/addons/loaders/GLTFLoader.js';
    import { OrbitControls } from 'three/addons/controls/OrbitControls';

    var artwork_collection = @json($artwork_collections);

    function eventFuntion(event) {
        console.log(event.target.files[0]);
        const url = URL.createObjectURL(event.target.files[0]);
        GLTFLoad(url);
    }

    function addSculptureModelUploadListener() {
        const inputs = document.querySelector('#sculpture-model-upload').querySelectorAll('input');
        inputs.forEach(input => {
            input.removeEventListener('change', eventFuntion);
            input.addEventListener('change', eventFuntion);
        });
    }

    function init() {
        container = document.getElementById('sculpture-canvas');

        camera = new THREE.PerspectiveCamera(75, 4 / 3, 0.1, 1000);
        cameraTarget = new THREE.Vector3(0, 0, 0);
        scene = new THREE.Scene();
        scene.background = new THREE.Color(0xEEEEEE);
        scene.add(new THREE.AmbientLight(0xE5DCDF));

        var light = new THREE.AmbientLight(0x404040);
        scene.add(light);

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
            model.rotation.y = - Math.PI / 2;
            model.name = "space-model";
            scene.add(model);
        });
    }

    function animate() {
        addSculptureModelUploadListener()
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

    var sculpture_url = @json($sculpture_url);

    if (sculpture_url) {
        GLTFLoad(sculpture_url);
    }

    $('#sculpture_name').on('keydown', function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });

    $('#sculpture_artist').on('keydown', function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });

    $('#sculpture_type').on('keydown', function (event) {
        if (event.keyCode == 13) {
            event.preventDefault();
        }
    });

    document.getElementById('sculpture-company-select').addEventListener('change', function() {
        const companyId = this.value;
        const collectionSelect = document.getElementById('sculpture-collection-select');
        const currentSelectedValue = collectionSelect.value; // Store current selection
        
        // Clear current options
        collectionSelect.innerHTML = '';
        
        if (!companyId) {
            const option = new Option('Select Company First', '', true, true);
            option.disabled = true;
            collectionSelect.add(option);
            return;
        }
        
        // Add default "Select a Collection" option
        const defaultOption = new Option('Select a Collection', '');
        collectionSelect.add(defaultOption);
        
        // Filter collections for selected company
        const companyCollections = artwork_collection.filter(collection => 
            collection.company_id == companyId
        );
        
        // Add new options
        if (companyCollections.length > 0) {
            companyCollections.forEach(collection => {
                const option = new Option(collection.name, collection.id);
                // If this was the previously selected option, mark it as selected
                if (collection.id == currentSelectedValue) {
                    option.selected = true;
                }
                collectionSelect.add(option);
            });
        } else {
            // Add a placeholder option if no collections exist
            const option = new Option('No collections available for this company', '', true, true);
            option.disabled = true;
            collectionSelect.add(option);
        }
    });

    // Modify the window load event handler to only trigger if we're not in edit mode
    window.addEventListener('load', function() {
        const companySelect = document.getElementById('sculpture-company-select');
        const sculptureForm = document.getElementById('sculpture_form');
        const isEditMode = sculptureForm.querySelector('input[name="_method"]').value === 'PUT';
        
        if (!isEditMode && companySelect.value) {
            companySelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection