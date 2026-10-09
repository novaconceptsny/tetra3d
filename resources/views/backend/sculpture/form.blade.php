{{-- Shared form: backend (super admin, layouts.backend) and Inventory > Sculptures
     (company admins, layouts.redesign — see InventorySculptureController::formData). --}}
@extends($layout ?? 'layouts.backend')

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

<div class="{{ !empty($backUrl) ? 'container-fluid py-4 px-4' : '' }}">
<div class="card mb-3">
    <div class="card-header d-flex align-items-center gap-3">
        @if(!empty($backUrl))
            <a href="{{ $backUrl }}" class="btn btn-outline-secondary btn-sm"><i class="fas fa-arrow-left me-1"></i> Sculptures</a>
        @endif
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
                            @if($sculpture?->getFirstMedia('sculpture'))
                                <div class="uploaded-file-name mt-2 text-muted">
                                    File: {{ $sculpture->getFirstMedia('sculpture')->file_name }}
                                </div>
                            @endif
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
                            @if($sculpture?->getFirstMedia('interaction'))
                                <div class="uploaded-file-name mt-2 text-muted">
                                    File: {{ $sculpture->getFirstMedia('interaction')->file_name }}
                                </div>
                            @endif
                        </div>
                        
                        @if($lockCompany ?? false)
                            {{-- Company admins: the sculpture always belongs to their own company --}}
                            <div class="col-12 mb-3">
                                <label class="form-label">Company</label>
                                <input type="text" class="form-control" value="{{ $companies->first()?->name }}" readonly>
                            </div>
                        @else
                        <x-backend::inputs.select col="col-12 mb-3" id="sculpture-company-select"
                            name="company_id" label="Company" required>
                            <option value="">Select Company</option>
                            @foreach($companies as $company)
                                <x-backend::inputs.select-option :value="$company->id" :text="$company->name"
                                    :selected="$sculpture?->company_id" />
                            @endforeach
                        </x-backend::inputs.select>
                        @endif

                        <x-backend::inputs.select col="col-12 mb-3" id="sculpture-collection-select"
                            name="artwork_collection_id" label="Collection" required>
                            @if($lockCompany ?? false)
                                @foreach($artwork_collections as $collection)
                                    <x-backend::inputs.select-option :value="$collection->id" :text="$collection->name"
                                        field="artwork_collection_id" :selected="$sculpture?->artwork_collection_id" />
                                @endforeach
                            @elseif($sculpture)
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
                        {{-- Size in metres. Filled from the 3D model; editing one value resizes the sculpture proportionally. --}}
                        <x-backend::inputs.text col="col-4 mb-3" type="number" step="any" min="0.01" name="data.length" id="data-length"
                            value="{{ $sculpture?->data->length }}" label="Length (m)" />
                        <x-backend::inputs.text col="col-4 mb-3" type="number" step="any" min="0.01" name="data.width" id="data-width"
                            value="{{ $sculpture?->data->width }}" label="Width (m)" />
                        <x-backend::inputs.text col="col-4 mb-3" type="number" step="any" min="0.01" name="data.height" id="data-height"
                            value="{{ $sculpture?->data->height }}" label="Height (m)" />
                        <input type="hidden" name="data[scale]" id="data-scale" value="{{ $sculpture?->data->scale ?? 1 }}">
                        <input type="hidden" name="data[original_length]" id="data-original-length" value="{{ $sculpture?->data->original_length }}">
                        <input type="hidden" name="data[original_width]" id="data-original-width" value="{{ $sculpture?->data->original_width }}">
                        <input type="hidden" name="data[original_height]" id="data-original-height" value="{{ $sculpture?->data->original_height }}">
                        <div class="col-12 mb-3 d-flex align-items-center justify-content-between">
                            <small class="text-muted" id="sculpture-scale-info">
                                Change any size to resize the sculpture (proportions are kept).
                            </small>
                            <button type="button" class="btn btn-sm btn-outline-secondary" id="sculpture-size-reset">
                                Reset to model size
                            </button>
                        </div>
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
</div>
@endsection

@section('styles')
@if(($layout ?? 'layouts.backend') === 'layouts.redesign')
    {{-- the front-end layout does not include the media library styles --}}
    @mediaLibraryStyles
@endif
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
@if(($layout ?? 'layouts.backend') !== 'layouts.redesign')
{{-- layouts.redesign already defines this import map --}}
<script type="importmap">
    {
        "imports": {
            "three": "https://unpkg.com/three@0.161.0/build/three.module.js",
            "three/addons/": "https://unpkg.com/three@0.161.0/examples/jsm/"
        }
    }
</script>
@endif

<script>
    function previewImage(input) {
        const previewContainer = input.closest('.d-flex').querySelector('.media-library-thumb');
        const file = input.files[0];
        const fileNameDisplay = input.closest('.mb-3').querySelector('.uploaded-file-name');
        
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
            
            // Display file name
            if (!fileNameDisplay) {
                const nameElement = document.createElement('div');
                nameElement.className = 'uploaded-file-name mt-2 text-muted';
                nameElement.textContent = `File: ${file.name}`;
                input.closest('.mb-3').appendChild(nameElement);
            } else {
                fileNameDisplay.textContent = `File: ${file.name}`;
            }
            
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
    import { DRACOLoader } from 'three/addons/loaders/DRACOLoader.js';
    import { MeshoptDecoder } from 'three/addons/libs/meshopt_decoder.module.js';
    import { OrbitControls } from 'three/addons/controls/OrbitControls.js';

    var artwork_collection = @json($artwork_collections);

    // Many exported/optimised GLB files are Draco- or Meshopt-compressed.
    // Without these decoders GLTFLoader fails and nothing is shown.
    const dracoLoader = new DRACOLoader();
    dracoLoader.setDecoderPath('https://unpkg.com/three@0.161.0/examples/jsm/libs/draco/gltf/');

    function loadSculptureFile(file) {
        if (!file || !/\.(glb|gltf)$/i.test(file.name)) {
            return;
        }
        GLTFLoad(URL.createObjectURL(file), 1); // a new model starts at its own size
    }

    // Listen on the wrapper (capture phase) instead of on the <input> itself:
    // - the media-library uploader is a Livewire component, so its <input> can be re-rendered/replaced;
    // - when a file is dragged onto the drop zone, the uploader uploads it directly and the input never fires "change".
    // The wrapper div is outside the Livewire component, so these listeners always stay attached.
    function addSculptureModelUploadListener() {
        const uploadBox = document.getElementById('sculpture-model-upload');
        if (!uploadBox) {
            return;
        }

        uploadBox.addEventListener('change', function (event) {
            const input = event.target;
            if (input && input.type === 'file' && input.files && input.files.length) {
                loadSculptureFile(input.files[0]);
            }
        }, true);

        uploadBox.addEventListener('drop', function (event) {
            const files = event.dataTransfer && event.dataTransfer.files;
            if (files && files.length) {
                loadSculptureFile(files[0]);
            }
        }, true);
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

    // ---- Sculpture size / scale --------------------------------------------------------
    // originalSize = size of the GLB as exported (metres, at scale 1).
    // currentScale = uniform factor chosen by the admin; saved as data.scale and applied in the tour.
    // data.length/width/height always hold the resulting (scaled) size.
    let currentModel = null;
    let originalSize = null;
    let currentScale = 1;
    const MIN_SCALE = 0.01;
    const MAX_SCALE = 100;
    const sizeFields = { length: 'data-length', width: 'data-width', height: 'data-height' };

    function roundSize(value) {
        return Math.round(value * 100) / 100;
    }

    function applyScale(skipField) {
        if (!currentModel || !originalSize) {
            return;
        }

        currentModel.scale.setScalar(currentScale);

        Object.keys(sizeFields).forEach(function (key) {
            if (key !== skipField) {
                document.getElementById(sizeFields[key]).value = roundSize(originalSize[key] * currentScale);
            }
        });

        document.getElementById('data-scale').value = currentScale;
        document.getElementById('data-original-length').value = originalSize.length;
        document.getElementById('data-original-width').value = originalSize.width;
        document.getElementById('data-original-height').value = originalSize.height;

        const info = document.getElementById('sculpture-scale-info');
        if (info) {
            info.textContent = Math.abs(currentScale - 1) < 0.0001
                ? 'Original model size. Change any size to resize the sculpture (proportions are kept).'
                : 'Resized to ' + Math.round(currentScale * 1000) / 10 + '% of the original model ('
                    + roundSize(originalSize.length) + ' x ' + roundSize(originalSize.width) + ' x ' + roundSize(originalSize.height) + ' m).';
        }

        // Keep the whole sculpture in view
        const height = originalSize.height * currentScale;
        const maxDim = Math.max(originalSize.length, originalSize.width, originalSize.height) * currentScale;
        camera.position.set(0, height / 2, Math.max(5, maxDim * 2));
        controls.target.set(0, height / 2, 0);
        controls.update();
    }

    function onSizeFieldInput(key) {
        const value = parseFloat(document.getElementById(sizeFields[key]).value);
        if (!originalSize || !(value > 0) || !(originalSize[key] > 0)) {
            return;
        }
        currentScale = Math.min(MAX_SCALE, Math.max(MIN_SCALE, value / originalSize[key]));
        applyScale(key); // don't rewrite the field the user is typing in
    }

    Object.keys(sizeFields).forEach(function (key) {
        const field = document.getElementById(sizeFields[key]);
        field.addEventListener('input', function () { onSizeFieldInput(key); });
        field.addEventListener('change', function () { applyScale(); }); // tidy up the typed value
    });

    document.getElementById('sculpture-size-reset').addEventListener('click', function () {
        currentScale = 1;
        applyScale();
    });

    function GLTFLoad(full_model_url, initialScale) {
        var loader = new GLTFLoader();
        loader.setDRACOLoader(dracoLoader);
        loader.setMeshoptDecoder(MeshoptDecoder);

        scene.traverse(function (object) {
            if (object.name === 'space-model') {
                scene.remove(object);
            }
        });

        loader.load(full_model_url, function (gltf) {
            const model = gltf.scene;
            // Measure at scale 1, before rotating (length = z, width = x, height = y)
            const size = getSize(model);
            originalSize = { length: size.depth, width: size.width, height: size.height };

            model.rotation.y = - Math.PI / 2;
            model.name = "space-model";
            scene.add(model);

            currentModel = model;
            currentScale = (initialScale > 0) ? initialScale : 1;
            applyScale();
        }, undefined, function (error) {
            console.error('Sculpture model could not be loaded:', error);
            alert('The 3D model could not be previewed, so its size could not be filled in.\n\n' + (error && error.message ? error.message : error));
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

    init();
    addSculptureModelUploadListener();
    animate();

    var sculpture_url = @json($sculpture_url);

    if (sculpture_url) {
        GLTFLoad(sculpture_url, parseFloat(@json($sculpture?->data->scale ?? 1)) || 1);
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

    // Company select only exists for super admins (company admins have a fixed company)
    document.getElementById('sculpture-company-select')?.addEventListener('change', function() {
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
        if (!companySelect) {
            return;
        }
        const sculptureForm = document.getElementById('sculpture_form');
        const isEditMode = sculptureForm.querySelector('input[name="_method"]').value === 'PUT';
        
        if (!isEditMode && companySelect.value) {
            companySelect.dispatchEvent(new Event('change'));
        }
    });
</script>
@endsection