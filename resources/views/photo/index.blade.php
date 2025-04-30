@extends('layouts.redesign')

@section('content')


        <header class="header bg-white">
            <div class="header-logo border-top">
                <div class="container">
                    <div class="row align-items-center py-3">
                        <div class="col">
                            <div class="project-name">{{ $project->name }}</div>
                        </div>
                        <div class="col text-end">
                            <button class="btn btn-primary" data-mode="edit" data-project-name="{{ $project->name }}" data-project-id="{{ $project->id }}" data-bs-toggle="modal" data-bs-target="#projectModal"><i class="fas fa-pen"></i> Edit project</button>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <input type="hidden" value="{{ $project->id }}" name="project-id" id="project-id">
        <!-- Main Content -->
        <div class="container main-content my-5">
            <div class="row">
                <!-- Sidebar: Collections -->
                <div class="col-md-6 col-xl-2 order-md-1 mb-3">
                    <div class="sidebar bg-white rounded overflow-hidden">
                        <div class="title-box" >Collections <a href="#" class="enter-link fw-normal" data-bs-toggle="modal" data-bs-target="#collectionsModal">Enter</a></div>
                        <ul class="list-group">
                            <li class="list-group-item d-flex align-items-center border rounded p-2 mb-2">
                                <button class="add-collection-btn" data-bs-toggle="modal" data-bs-target="#addCollectionModal">
                                    <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                    <span class="add-collection-text">Add Collection</span>
                                </button>
                            </li>
                            @foreach($artworkCollections as $artworkCollection)
                                @if($project->artworkCollections->contains($artworkCollection->id))
                                    <li class="list-group-item d-flex align-items-center border rounded p-2 mb-2" data-module="artworks" data-id="{{ $artworkCollection->id }}">
                                        <i class="fas fa-image collection-icon"></i>
                                        <div class="collection-info">
                                            <span class="collection-name">{{ $artworkCollection->name }}</span>
                                            <span class="collection-items">{{ $artworkCollection->artworks_count }} items</span>
                                        </div>
                                        <div class="dropdown position-absolute top-0 end-0">
                                            <button class="btn btn-link" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v ms-auto"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item delete-item" href="#" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Delete</a></li>
                                            </ul>
                                        </div>
                                    </li>
                                @endif
                            @endforeach
                        </ul>
                    </div>
                </div>

                <!-- Main Content: Photos and Surfaces -->
                <div class="col-md-12 col-xl-8 order-md-3 order-xl-2 mb-3">
                    <!-- Photos Section -->
                    <div class="photos-section bg-white rounded overflow-hidden">
                        <div  class="title-box">Photos
                            <button class="btn enter-link" data-bs-toggle="modal" data-bs-target="#showImageModal" id="toggleButton">Enter</button>
                            <!-- <button class="btn enter-link" id="duplicateImages">Duplicate images</button> -->
                        </div>
                        <div class="row g-3" id="photosContainer">
                            <div class="col-md-3 d-flex photo-item">
                                <div class="card shadow-sm photo-card add-image-card w-100 justify-content-center">
                                    <button class="add-image-btn" data-bs-toggle="modal" data-bs-target="#addImageModal">
                                        <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                        <span class="add-image-text">Add Image</span>
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Surfaces Section -->
                <div class="col-md-6 col-xl-2 order-md-2">
                    <div class="surfaces-section bg-white rounded overflow-hidden">
                        <div class="title-box">Surfaces <button class="btn enter-link" id="toggleButtonSurfaces" data-bs-toggle="modal" data-bs-target="#surfaceModalShow">Enter</button></div>

                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-center align-items-center card surface-item">
                                <button class="add-image-btn" data-bs-toggle="modal" data-bs-target="#surfaceModal" data-action="add">
                                    <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                    <span class="add-image-text">Add Surface</span>
                                </button>
                            </li>
                            @foreach($surfaces as $surface)
                                <li class="list-group-item d-flex justify-content-center align-items-center card surface-item"
                                    data-name="{{ $surface->name }}"
                                    data-id="{{ $surface->id }}"
                                    data-module="surfaces"
                                    data-width="{{ $surface->data['img_width'] ?? '' }}"
                                    data-height="{{ $surface->data['img_height'] ?? '' }}">
                                    <span class="surface-name">{{ $surface->name }}</span>
                                    <div class="dropdown position-absolute top-0 end-0">
                                        <button class="btn btn-link" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                            <i class="fas fa-ellipsis-v ms-auto"></i>
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item edit-item" href="#" data-bs-toggle="modal" data-bs-target="#surfaceModal" data-action="edit">Edit</a></li>
                                            <li><a class="dropdown-item delete-item" href="#" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Delete</a></li>
                                        </ul>
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Layout Sections -->
            @foreach($layoutPhotos as $layout)
                <div class="layout-section mt-5">
                    <div class="title-box">{{ $layout['name'] }}</div>
                    <div class="row g-3" id="layout{{ $loop->iteration }}Container" data-layout-id="{{ $layout['id'] }}">
                        @foreach($layout['photos'] as $photoId)
                            @php
                                $photo = $photos->firstWhere('id', $photoId);
                            @endphp
                            @if($photo)
                                <div class="col-md-3 layout-item">
                                    <div class="card shadow-sm bg-white image-item">
                                        <div class="overflow-hidden img-home">
                                            <img src="{{ $photo->background_url }}" class="card-img-top img-fluid" alt="{{ $photo->name }}">
                                        </div>
                                        <div class="card-body d-flex justify-content-between align-items-end">
                                            <p class="card-text">
                                                <span>{{ $photo->name }}</span><br>
                                                <small>Created: {{ $photo->created_at->format('Y-m-d') }}</small>
                                            </p>
                                            <button type="button"
                                                    class="btn enter-link"
                                                    onclick="navigateToPhoto({{ $photo->id }}, {{ $layout['id'] }})"
                                                    data-photo-id="{{ $photo->id }}"
                                                    data-layout-id="{{ $layout['id'] }}">
                                                Enter
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        @endforeach
                        @if($loop->last)
                            <div class="col-md-3 layout-item">
                                <div class="card bg-white card-layout">
                                    <button class="add-image-btn">
                                        <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                        <span class="add-image-text">Add Layout</span>
                                    </button>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            @endforeach

            <!-- If no layouts exist -->
            @if(empty($layoutPhotos))
                <div class="layout-section">
                    <div class="title-box">No layouts available</div>
                    <div class="col-md-3 layout-item">
                        <div class="card bg-white card-layout">
                            <button class="add-image-btn">
                                <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                <span class="add-image-text">Add Layout</span>
                            </button>
                        </div>
                    </div>
                </div>
            @endif
        </div>

        <!-- Modal for Add Collection -->
        <div class="modal fade" id="addCollectionModal" tabindex="-1" aria-labelledby="addCollectionModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addCollectionModalLabel">Collections</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form action="{{ route('projects.collections.update', $project->id) }}" method="POST">
                        @csrf
                        <div class="modal-body">
                            <select name="collection_id" class="form-control" required>
                                <option value="">Select Collection</option>
                                @foreach($artworkCollections as $artworkCollection)
                                    <option value="{{ $artworkCollection->id }}">{{ $artworkCollection->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary">Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal for Add Image -->
        <div class="modal fade" id="addImageModal" tabindex="2" aria-labelledby="addImageModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addImageModalLabel">Add images</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Add images and upload a .jpeg or PNG image file. Max 2048 pixels on the long edge of the image.</p>
                        <div id="imagePreviewContainer">
                            <!-- image -->
                        </div>
                        <div class="d-flex justify-content-end mt-3">
                            <button class="btn btn-light select-image-btn"
                                    onclick="document.getElementById('imageInput').click()">
                                <i class="fas fa-plus"></i> Select image
                            </button>
                            <input type="file" id="imageInput" accept="image/jpeg, image/png" multiple style="display: none;"
                                onchange="previewImages(event)">
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light w-100" onclick="saveImages()">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal xác nhận xóa -->
        <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Delete</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        Are you sure you want to delete this item?
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                        <button type="button" class="btn btn-danger" id="confirmDeleteButton">Delete</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Surfaces -->
        <div class="modal fade" id="surfaceModal" tabindex="-1" aria-labelledby="editSurfaceModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editSurfaceModalLabel">EDIT SURFACE</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body mt-4">
                        <div class="mb-3 mb-4">
                            <input type="text" class="form-control" id="surfaceName" value="" placeholder="Name.....">
                        </div>
                        <div class="input-group mb-3">
                            <input type="number" class="form-control" id="surfaceWidth" value="" aria-label="Width" placeholder="Width">
                            <span class="input-group-text">cm</span>
                            <span class="input-group-text">×</span>
                            <input type="number" class="form-control" id="surfaceHeight" value="" aria-label="Height" placeholder="Height">
                            <span class="input-group-text">cm</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" id="saveSurface" class="btn btn-light w-100" data-bs-dismiss="modal">Save</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div class="modal fade" id="imageModal" tabindex="-1" aria-labelledby="imageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="imageModalLabel">IMAGE TITLE</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <form action="" method="post" id="updateImage" onsubmit="return false;">
                            @csrf
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <select name="surfaceId" id="surfaceId" class="form-select">
                                         <option  data-width=100"
                                                data-height="100">
                                            Default
                                        </option>
                                    @foreach($surfaces as $surface)
                                        <option value="{{ $surface->id }}"
                                                data-width="{{ $surface->data['img_width'] ?? '' }}"
                                                data-height="{{ $surface->data['img_height'] ?? '' }}">
                                            {{ $surface->name }}
                                        </option>
                                    @endforeach
                                </select>
{{--                                <input type="text" class="form-control" id="titleImage" placeholder="Image title (editable)">--}}
                                    <img src="" alt="" class="modal-image d-none">
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="button" id="saveLayout" class="btn btn-light">Save</button>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">Width</span>
                                    <input type="number" class="form-control" id="rectWidth" value="">
                                    <span class="input-group-text">
                                        <select class="form-select" id="widthUnit" onchange="convertUnits('width')" >
                                          <option selected value="cm">cm</option>
                                          <option value="inch">inch</option>
                                        </select>
                                    </span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">Height</span>
                                    <input type="number" class="form-control" id="rectHeight" value="">
                                    <span class="input-group-text">
                                        <select class="form-select" id="heightUnit" onchange="convertUnits('height')">
                                          <option selected value="cm">cm</option>
                                          <option value="inch">inch</option>
                                        </select>
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="text-center position-relative">
                            <canvas id="imageCanvas" class="img-fluid"></canvas>
                        </div>
                         </form>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal for show image-->
        <div class="modal fade" id="showImageModal" tabindex="-1" aria-labelledby="showImageModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="showImageModalLabel">Photos</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="photos-section photos-section-popup bg-white rounded overflow-hidden">
                            <div class="row g-3" id="photosContainer2">
                                <div class="col-md-3 d-flex photo-item">
                                    <div class="card shadow-sm photo-card add-image-card w-100 justify-content-center">
                                        <button class="add-image-btn" data-bs-toggle="modal" data-bs-target="#addImageModal">
                                            <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                            <span class="add-image-text">Add Image</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-light w-100" aria-label="Close">Close</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Collections -->
        <div class="modal fade" id="collectionsModal" tabindex="-1" aria-labelledby="collectionsModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="collectionsModalLabel">Collections</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Danh sách collections -->
                        <div class="collection-list">
                            <div class="collection-item">
                                <button class="add-collection-btn" data-bs-toggle="modal" data-bs-target="#addCollectionModal">
                                    <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                    <span class="add-collection-text">Add Collection</span>
                                </button>
                            </div>
                            @foreach($artworkCollections as $artworkCollection)
                                @if($project->artworkCollections->contains($artworkCollection->id))
                                    <div class="collection-item">
                                        <span>{{ $artworkCollection->name }}</span>
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Surfaces -->
        <div class="modal fade" id="surfaceModalShow" tabindex="-1" aria-labelledby="surfacesModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="surfacesModalLabel">Surfaces</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">

                        <!-- Danh sách surfaces -->
                        <div class="surface-list">
                            <div class="row list-unstyled">
                                <div class="col-md-4">
                                    <button class="add-surface-btn mb-3" data-bs-toggle="modal" data-bs-target="#surfaceModal" data-action="add">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="12" fill="#28a745"/>
                                            <path d="M12 6V18M6 12H18" stroke="white" stroke-width="2"/>
                                        </svg>
                                        Add Surface
                                    </button>
                                </div>
                                @foreach($surfaces as $surface)
                                    <div class="col-md-4 ">
                                        <div class="list-group-item d-flex justify-content-center align-items-center card surface-item mb-3"
                                             data-name="{{ $surface->name }}"
                                             data-id="{{ $surface->id }}"
                                             data-module="surfaces"
                                             data-width="{{ $surface->data['img_width'] ?? '' }}"
                                             data-height="{{ $surface->data['img_height'] ?? '' }}">
                                            <span class="surface-name">{{ $surface->name }}</span>
                                            <div class="dropdown position-absolute top-0 end-0">
                                                <button class="btn btn-link" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                    <i class="fas fa-ellipsis-v ms-auto"></i>
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li><a class="dropdown-item edit-item" href="#" data-bs-toggle="modal" data-bs-target="#surfaceModal" data-action="edit">Edit</a></li>
                                                    <li><a class="dropdown-item delete-item" href="#" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Delete</a></li>
                                                </ul>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>

                        </div>
                    </div>
                    <div class="modal-footer">

                    </div>
                </div>
            </div>
        </div>

{{--    Modal edit project--}}
        <div class="modal fade" id="projectModal" tabindex="-1" aria-labelledby="projectModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="projectModalLabel">Add new project</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <p>Add a project name and upload a JPEG or PNG image file for the project thumbnail. Max 2048 pixels on the long edge of the image.</p>
                        <form action="{{ route('project.update', ['id' => $project->id ]) }}" method="post">
                            @csrf
                            <div class="mb-3">
                                <input type="text" class="form-control" id="projectNameInput" name="name" placeholder="Name" value="{{ $project->name }}">
                            </div>
                            <div class="image-upload-box mb-3" id="imageUploadBox">
                                <input type="file" class="image-input" id="imageInput" accept="image/jpeg, image/png">
                                <span>+ Image</span>
                                <div class="overlay">Click to replace image</div>
                            </div>
                            <div class="image-name" id="imageName"></div>
                            <button type="submit" class="btn btn-save">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
@endpush
@section('styles')
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:ital,opsz,wght@0,14..32,100..900;1,14..32,100..900&display=swap"
          rel="stylesheet">
@endsection
@push('scripts')
<script>
    const projectId = $('#project-id').val();
    let photosData = [];
    let selectedImages = [];

    const canvas = document.getElementById('imageCanvas');
    const ctx = canvas.getContext('2d');
    const widthInput = document.getElementById('rectWidth');
    const heightInput = document.getElementById('rectHeight');
    let corners = [];
    let backgroundImage = null;
    let currentImageElement = null; // Store reference to current image element

    // Set fixed canvas dimensions
    const CANVAS_WIDTH = 800;
    const CANVAS_HEIGHT = 600;

    // Initialize photosData with existing photos
    const existing_photos = @json($photos);
    if (existing_photos && existing_photos.length > 0) {
        existing_photos.forEach(photo => {
            photosData.push({
                id: String(photo.id),
                src: photo.background_url,
                name: photo.name,
                corners: photo.data['corners'] || calculateDefaultCorners(),
                width: photo.data['img_width'],
                height: photo.data['img_height'],
                boundingBoxTop: photo.data['bounding_box_top'],
                boundingBoxLeft: photo.data['bounding_box_left'],
                boundingBoxWidth : photo.data['bounding_box_width'],
                boundingBoxHeight : photo.data['bounding_box_height'],
            });
        });
    }

    function navigateToPhoto(photoId, layoutId) {
        window.location.href = `/photos/${photoId}?layout_id=${layoutId}`;
    }

    function calculateDefaultCorners() {

        const defaultWidth = 100;
        const defaultHeight = 100;

        const CANVAS_WIDTH = 800;
        const CANVAS_HEIGHT = 600;
        // Calculate starting position to center the rectangle
        const startX = (CANVAS_WIDTH - defaultWidth) / 2;
        const startY = (CANVAS_HEIGHT - defaultHeight) / 2;

        return [
            { x: startX, y: startY, label: '1' },                                    // top-left
            { x: startX + defaultWidth, y: startY, label: '2' },                    // top-right
            { x: startX + defaultWidth, y: startY + defaultHeight, label: '3' },    // bottom-right
            { x: startX, y: startY + defaultHeight, label: '4' }                    // bottom-left
        ];
    }

    function generateUniqueId() {
        return 'photo_' + Date.now() + '_' + Math.random().toString(36).substr(2, 9);
    }

    function previewImages(event) {
        const files = event.target.files;
        const previewContainer = document.getElementById('imagePreviewContainer');

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            if (file.type === 'image/jpeg' || file.type === 'image/png') {
                const reader = new FileReader();
                reader.onload = function (e) {
                    const img = new Image();
                    img.onload = function() {
                        const imageData = {
                            id: generateUniqueId(),
                            src: e.target.result,
                            name: file.name.replace(/\.[^/.]+$/, ""), // Remove file extension
                            corners: calculateDefaultCorners(),
                            width: img.width,
                            height: img.height,
                            boundingBoxTop: 0,
                            boundingBoxLeft: 0,
                            boundingBoxWidth: 0,
                            boundingBoxHeight: 0
                        };
                        selectedImages.push(imageData);

                        const previewDiv = document.createElement('div');
                        previewDiv.classList.add('image-preview');
                        previewDiv.innerHTML = `
                            <div class="preview-header">
                                <input type="text"
                                       class="form-control image-name-input"
                                       value="${imageData.name}"
                                       onchange="updateImageName(this, ${selectedImages.length - 1})">
                                <div class="remove-btn" onclick="removeImage(this, ${selectedImages.length - 1})">
                                    <i class="fas fa-times"></i>
                                </div>
                            </div>
                            <div class="preview-image">
                                <img src="${imageData.src}" alt="${imageData.name}" class="img-fluid">
                            </div>
                        `;
                        previewContainer.appendChild(previewDiv);
                    };
                    img.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }
    }

    function updateImageName(input, index) {
        selectedImages[index].name = input.value;
    }

    function removeImage(element, index) {
        photosData.splice(index, 1);
        element.closest('.image-preview').remove();
    }

    function saveImages() {
        const formData = new FormData();

        // Add project_id to FormData
        formData.append('project_id', document.getElementById('project-id').value);

        // Add each image to FormData
        selectedImages.forEach((imageData, index) => {
            // Convert base64 to blob
            const base64Data = imageData.src.split(',')[1];
            const byteCharacters = atob(base64Data);
            const byteArrays = [];

            for (let offset = 0; offset < byteCharacters.length; offset += 512) {
                const slice = byteCharacters.slice(offset, offset + 512);
                const byteNumbers = new Array(slice.length);

                for (let i = 0; i < slice.length; i++) {
                    byteNumbers[i] = slice.charCodeAt(i);
                }

                const byteArray = new Uint8Array(byteNumbers);
                byteArrays.push(byteArray);
            }

            const blob = new Blob(byteArrays, { type: 'image/jpeg' });
            const file = new File([blob], imageData.name + '.jpg', { type: 'image/jpeg' });

            // Add file and metadata to FormData
            formData.append(`images[${index}]`, file);
            formData.append(`names[${index}]`, imageData.name);
            formData.append(`widths[${index}]`, imageData.width || '0');
            formData.append(`heights[${index}]`, imageData.height || '0');
            formData.append(`boundingBoxTop[${index}]`, imageData.boundingBoxTop || '0');
            formData.append(`boundingBoxLeft[${index}]`, imageData.boundingBoxLeft || '0');
            formData.append(`boundingBoxWidth[${index}]`, imageData.boundingBoxWidth || '0');
            formData.append(`boundingBoxHeight[${index}]`, imageData.boundingBoxHeight || '0');
            formData.append(`corners[${index}]`, JSON.stringify(imageData.corners));

        });

        // Add CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').content;

        // Send the FormData to the server
        fetch('/photo/store', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('Error saving images: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving images');
        });
    }

    let itemToDelete = '';
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('delete-item')) {
            e.preventDefault();

            let currentElement = e.target;

            while (currentElement) {
                if (currentElement.classList.contains('list-group-item')) {
                    itemToDelete = currentElement;
                    break;
                }
                if (currentElement.classList.contains('col-md-3')) {
                    itemToDelete = currentElement;
                    break;
                }
                currentElement = currentElement.parentElement;
            }
        }
    });

    function deleteItem(id, module){
        const formData = new FormData();
        formData.append('id', id);
        const token = document.querySelector('meta[name="csrf-token"]');
        if (!token) {
            throw new Error('CSRF token not found');
        }

        // Send the FormData to the server
        return fetch('/'+module+'/destroy/'+id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token.content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
    }

    document.getElementById('confirmDeleteButton').addEventListener('click', function() {
        if (itemToDelete) {
            const id = itemToDelete.getAttribute('data-id');
            const module = itemToDelete.getAttribute('data-module');

            deleteItem(id, module);

            itemToDelete.remove();
            itemToDelete = null;
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
            modal.hide();
            location.reload();
        } else {
            console.error('No item to delete. itemToDelete is not defined.');
        }
    });

    const surfaceModal = document.getElementById('surfaceModal');
    let currentIndex = null; // Lưu chỉ số của surface đang chỉnh sửa

    // Xử lý khi modal được mở
    surfaceModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget; // Nút đã kích hoạt modal
        const action = button.getAttribute('data-action'); // "add" hoặc "edit"

        const modalTitle = surfaceModal.querySelector('.modal-title');
        const surfaceNameInput = surfaceModal.querySelector('#surfaceName');
        const surfaceWidthInput = surfaceModal.querySelector('#surfaceWidth');
        const surfaceHeightInput = surfaceModal.querySelector('#surfaceHeight');

        if (action === 'add') {
            // Thêm mới surface
            modalTitle.textContent = 'ADD SURFACE';
            surfaceNameInput.value = '';
            surfaceWidthInput.value = '';
            surfaceHeightInput.value = '';
            currentIndex = null;
        } else if (action === 'edit') {
            // Sửa surface
            modalTitle.textContent = 'EDIT SURFACE';
            const surfaceItem = button.closest('.list-group-item');
            const name = surfaceItem.getAttribute('data-name');
            const width = surfaceItem.getAttribute('data-width');
            const height = surfaceItem.getAttribute('data-height');
            surfaceNameInput.value = name;
            surfaceWidthInput.value = width;
            surfaceHeightInput.value = height;
            currentIndex = Array.from(document.querySelectorAll('.list-group-item')).indexOf(surfaceItem);
        }
    });

    // Xử lý khi nhấn nút Save
    document.getElementById('saveSurface').addEventListener('click', function () {
        const surfaceName = document.getElementById('surfaceName').value;
        const surfaceWidth = document.getElementById('surfaceWidth').value;
        const surfaceHeight = document.getElementById('surfaceHeight').value;

        if (surfaceName && surfaceWidth && surfaceHeight) {
            if (currentIndex === null) {
                // Thêm mới surface
                const surfaceList = document.querySelector('.surfaces-section .list-group');
                const newSurface = document.createElement('li');
                newSurface.className = 'list-group-item d-flex justify-content-between align-items-center card surface-item';
                newSurface.setAttribute('data-name', surfaceName);
                newSurface.setAttribute('data-width', surfaceWidth);
                newSurface.setAttribute('data-height', surfaceHeight);
                newSurface.innerHTML = `
                        <span class="surface-name">${surfaceName}</span>
                        <div class="dropdown position-absolute top-0 end-0">
                            <button class="btn btn-link" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v ms-auto"></i>
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                <li><a class="dropdown-item edit-item" href="#" data-bs-toggle="modal" data-bs-target="#surfaceModal" data-action="edit">Edit</a></li>
                                <li><a class="dropdown-item delete-item" href="#" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Delete</a></li>
                            </ul>
                        </div>
                    `;
                surfaceList.appendChild(newSurface);
            } else {
                // Sửa surface
                const surfaceItem = document.querySelectorAll('.list-group-item')[currentIndex];
                surfaceItem.setAttribute('data-name', surfaceName);
                surfaceItem.setAttribute('data-width', surfaceWidth);
                surfaceItem.setAttribute('data-height', surfaceHeight);
                surfaceItem.querySelector('span').textContent = surfaceName;
            }

            // Close modal
            const modal = bootstrap.Modal.getInstance(surfaceModal);
            modal.hide();
        } else {
            alert('Please fill in all information!');
        }
    });

    // document.getElementById('toggleButton').addEventListener('click', function() {
    //     const photosSection = document.querySelector('.photos-section');
    //
    //     photosSection.classList.toggle('overflow-hidden');
    //     photosSection.classList.toggle('expanded');
    // });

    // document.getElementById('toggleButtonSurfaces').addEventListener('click', function() {
    //     const photosSection = document.querySelector('.surfaces-section');
    //
    //     photosSection.classList.toggle('overflow-hidden');
    //     photosSection.classList.toggle('expanded');
    // });

    const surfaceModalImage = document.getElementById('imageModal');
    let currentIndexImage = null;

    surfaceModalImage.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        if (!button) return;

        const title = button.getAttribute('data-title');
        const image = button.getAttribute('data-image');
        const photoId = button.getAttribute('data-photo-id');

        this.setAttribute('data-photo-id', photoId);

        const modalTitle = this.querySelector('#imageModalLabel');
        const titleImage = this.querySelector('#titleImage');
        if (modalTitle) modalTitle.textContent = title;
        if (titleImage) titleImage.value = title;

        const imageItem = button.closest('.layout-item');

        currentIndexImage = Array.from(document.querySelectorAll('.layout-item')).indexOf(imageItem);
        const modalImage = this.querySelector('.modal-image');
        modalImage.src = image;

        const surfaceSelect = document.getElementById('surfaceId');
        const selectedOption = surfaceSelect.options[surfaceSelect.selectedIndex];
        const width = selectedOption.getAttribute('data-width');
        const height = selectedOption.getAttribute('data-height');

        if (width && height) {
            document.getElementById('rectWidth').value = width;
            document.getElementById('rectHeight').value = height;
        }
    });

    document.getElementById('surfaceId').addEventListener('change', function() {
        const selectedOption = this.options[this.selectedIndex];
        const width = selectedOption.getAttribute('data-width');
        const height = selectedOption.getAttribute('data-height');

        if (width && height) {
            document.getElementById('rectWidth').value = width;
            document.getElementById('rectHeight').value = height;
        }

    });


    document.addEventListener('DOMContentLoaded', () => {
        // Display existing photos
        const photosContainer = document.getElementById('photosContainer');

        // First, clear any existing content except the "Add Image" button
        const addImageCard = photosContainer.querySelector('.photo-item');
        photosContainer.innerHTML = '';
        photosContainer.appendChild(addImageCard);

        // Add each photo from photosData
        photosData.forEach(photo => {
            const colDiv = document.createElement('div');
            colDiv.classList.add('col-md-3', 'photo-item');
            colDiv.dataset.module = 'photo';
            colDiv.dataset.id = `${photo.id}`;
            colDiv.innerHTML = `
                <div class="card shadow-sm photo-card">
                    <div class="overflow-hidden img-home">
                        <img src="${photo.src}" class="card-img-top img-fluid" alt="${photo.name}">
                    </div>
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <p class="card-text">${photo.name}</p>
                        <div class="dropdown">
                            <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v ms-auto"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#imageModal"
                                    data-title="${photo.name}"
                                    data-image="${photo.src}"
                                    data-photo-id="${photo.id}">Surface Size</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addCollectionModal">Edit</a></li>
                                <li><a class="dropdown-item delete-item" href="#" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Delete</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            `;
            photosContainer.appendChild(colDiv);
        });

        const photosContainer2 = document.getElementById('photosContainer2');

        // First, clear any existing content except the "Add Image" button
        const addImageCard2 = photosContainer2.querySelector('.photo-item');
        photosContainer2.innerHTML = '';
        photosContainer2.appendChild(addImageCard2);

        // Add each photo from photosData
        photosData.forEach(photo => {
            const colDiv = document.createElement('div');
            colDiv.classList.add('col-md-3', 'photo-item');
            colDiv.dataset.module = 'photo';
            colDiv.dataset.id = `${photo.id}`;
            colDiv.innerHTML = `
                <div class="card shadow-sm photo-card">
                    <div class="overflow-hidden img-home">
                        <img src="${photo.src}" class="card-img-top img-fluid" alt="${photo.name}">
                    </div>
                    <div class="card-body d-flex justify-content-between align-items-center">
                        <p class="card-text">${photo.name}</p>
                        <div class="dropdown">
                            <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                <i class="fas fa-ellipsis-v ms-auto"></i>
                            </button>
                            <ul class="dropdown-menu">
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#imageModal"
                                    data-title="${photo.name}"
                                    data-image="${photo.src}"
                                    data-photo-id="${photo.id}">Surface Size</a></li>
                                <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addCollectionModal">Edit</a></li>
                                <li><a class="dropdown-item delete-item" href="#" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Delete</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            `;
            photosContainer2.appendChild(colDiv);
        });


        canvas.width = CANVAS_WIDTH;
        canvas.height = CANVAS_HEIGHT;

        function fitImageToCanvas(img) {
            const imgRatio = img.width / img.height;
            const canvasRatio = CANVAS_WIDTH / CANVAS_HEIGHT;
            let drawWidth, drawHeight, x, y;

            if (imgRatio > canvasRatio) {
                // Image is wider than canvas ratio
                drawWidth = CANVAS_WIDTH;
                drawHeight = CANVAS_WIDTH / imgRatio;
                x = 0;
                y = (CANVAS_HEIGHT - drawHeight) / 2;
            } else {
                // Image is taller than canvas ratio
                drawHeight = CANVAS_HEIGHT;
                drawWidth = CANVAS_HEIGHT * imgRatio;
                x = (CANVAS_WIDTH - drawWidth) / 2;
                y = 0;
            }

            return { x, y, width: drawWidth, height: drawHeight };
        }

        function drawCanvas(endPoints) {
            let points = [];
            if (!backgroundImage) return;

            // Clear canvas
            ctx.clearRect(0, 0, CANVAS_WIDTH, CANVAS_HEIGHT);

            // Draw background image fitted to canvas
            const fitDimensions = fitImageToCanvas(backgroundImage);
            ctx.drawImage(
                backgroundImage,
                fitDimensions.x,
                fitDimensions.y,
                fitDimensions.width,
                fitDimensions.height
            );


            // Define corners with their labels
            if(endPoints.length === 0){
                points = [
                    { x: rect.x, y: rect.y, label: '1' },                           // top-left
                    { x: rect.x + rect.width, y: rect.y, label: '2' },             // top-right
                    { x: rect.x + rect.width, y: rect.y + rect.height, label: '3' }, // bottom-right
                    { x: rect.x, y: rect.y + rect.height, label: '4' }             // bottom-left
                ];
            }else{
                points = [...endPoints];
            }


            ctx.beginPath();
            ctx.strokeStyle = 'red';
            ctx.moveTo(points[0].x, points[0].y);
            for (let i = 1; i < points.length; i++) {
            ctx.lineTo(points[i].x, points[i].y);
            }
            ctx.closePath();
            ctx.stroke();

           // Draw corner dots with labels
            const dotRadius = 8;
            ctx.fillStyle = '#00ff00';

            points.forEach(corner => {
                // Draw dot
                ctx.beginPath();
                ctx.arc(corner.x, corner.y, dotRadius, 0, Math.PI * 2);
                ctx.fill();

                // Draw label
                ctx.fillStyle = 'black';
                ctx.font = 'bold 12px Arial';
                ctx.textAlign = 'center';
                ctx.textBaseline = 'middle';
                ctx.fillText(corner.label, corner.x, corner.y);

                // Reset fill style for next dot
                ctx.fillStyle = '#00ff00';
            });

            // Calculate width and height based on corner positions
            const width = Math.sqrt(
                Math.pow(points[1].x - points[0].x, 2) +
                Math.pow(points[1].y - points[0].y, 2)
            );

            const height = Math.sqrt(
                Math.pow(points[3].x - points[0].x, 2) +
                Math.pow(points[3].y - points[0].y, 2)
            );

            // Update input fields with rounded values
            widthInput.value = Math.round(width);
            heightInput.value = Math.round(height);
            corners = points;
        }

        // Handle modal open
        const imageModal = document.getElementById('imageModal');
        imageModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (!button) return;

            // Store reference to the card that contains the image
            currentImageElement = button.closest('.photo-card');

            const image = button.getAttribute('data-image');


            const photoId = button.getAttribute('data-photo-id');

            corners = photosData.find(p => p.id === photoId).corners;

            if (!image) {
                console.error('No image data found');
                return;
            }

            // Create new image object
            backgroundImage = new Image();
            backgroundImage.onerror = function() {
                console.error('Failed to load image');
            };


            backgroundImage.onload = function() {
                // Initialize rectangle in center of canvas
                rect = {
                    x: (CANVAS_WIDTH - 100) / 2,
                    y: (CANVAS_HEIGHT - 100) / 2,
                    width: 100,
                    height: 100
                };
                drawCanvas(corners);
            };

            try {
                backgroundImage.src = image;
                const fitDimensions = fitImageToCanvas(backgroundImage);
                photosData.map(photo => {
                    if(photo.id === photoId){
                        photo.boundingBoxTop = fitDimensions.y;
                        photo.boundingBoxLeft = fitDimensions.x;
                        photo.boundingBoxWidth = fitDimensions.width;
                        photo.boundingBoxHeight = fitDimensions.height;
                    }
                });
            } catch (error) {
                console.error('Error setting image source:', error);
            }

            // Update modal title and input
            const title = button.getAttribute('data-title') || 'Untitled';
            const modalTitle = imageModal.querySelector('#imageModalLabel');
            const titleInput = imageModal.querySelector('#titleImage');
            if (modalTitle) modalTitle.textContent = title;
            if (titleInput) titleInput.value = title;
        });

        let isDragging = false;
        let selectedCorner = null;

        function getMousePos(canvas, evt) {
            const rect = canvas.getBoundingClientRect();
            const scaleX = canvas.width / rect.width;
            const scaleY = canvas.height / rect.height;
            return {
                x: (evt.clientX - rect.left) * scaleX,
                y: (evt.clientY - rect.top) * scaleY
            };
        }

        function isOverCornerDot(mouseX, mouseY, cornerX, cornerY) {
            const dotRadius = 8;
            const distance = Math.sqrt((mouseX - cornerX) ** 2 + (mouseY - cornerY) ** 2);
            return distance <= dotRadius * 2; // Increased hit area for better touch
        }

        // Mouse event handlers
        canvas.addEventListener('mousedown', (e) => {
            const pos = getMousePos(canvas, e);

            for (const corner of corners) {
                if (isOverCornerDot(pos.x, pos.y, corner.x, corner.y)) {
                    isDragging = true;
                    selectedCorner = corner.label;
                    canvas.style.cursor = 'grabbing';
                    break;
                }
            }
        });

        canvas.addEventListener('mousemove', (e) => {
            const pos = getMousePos(canvas, e);

            let isOverCorner = false;
            for (const corner of corners) {
                if (isOverCornerDot(pos.x, pos.y, corner.x, corner.y)) {
                    canvas.style.cursor = 'grab';
                    isOverCorner = true;
                    break;
                }
            }
            if (!isOverCorner && !isDragging) {
                canvas.style.cursor = 'default';
            }

            if (!isDragging) return;

            // Free-form corner dragging with fixed opposite corners
            if (selectedCorner) {
                const minSize = 10; // Minimum rectangle size
                corners[selectedCorner-1].x = pos.x;
                corners[selectedCorner-1].y = pos.y;

                drawCanvas(corners);
            }
        });

        canvas.addEventListener('mouseup', () => {
            isDragging = false;
            selectedCorner = null;
            canvas.style.cursor = 'default';
        });

        canvas.addEventListener('mouseleave', () => {
            isDragging = false;
            selectedCorner = null;
            canvas.style.cursor = 'default';
        });

        // Add save button handler
        document.getElementById('saveLayout').addEventListener('click', function(e) {
            e.preventDefault();

            // Get required elements
            const widthInput = document.getElementById('rectWidth');
            const heightInput = document.getElementById('rectHeight');
            const surfaceSelect = document.getElementById('surfaceId');
            const imageModal = document.getElementById('imageModal');

            // Validate required elements exist
            if (!widthInput || !heightInput || !surfaceSelect || !imageModal) {
                console.error('Required form elements not found');
                alert('Error: Required form elements not found');
                return;
            }

            // Validate width and height values
            if (!widthInput.value || !heightInput.value) {
                console.error('Width or height values are required');
                alert('Please enter both width and height values');
                return;
            }

            // Get the photo ID from the modal
            const photoId = imageModal.getAttribute('data-photo-id');
            if (!photoId) {
                console.error('Photo ID not found');
                alert('Error: Photo ID not found');
                return;
            }

            // Get surface ID
            const surfaceId = surfaceSelect.value;

            // Prepare the photo data
            const photoData = {
                corners: corners || [],
                boundingBoxTop: backgroundImage ? parseFloat(fitImageToCanvas(backgroundImage).y) : 0,
                boundingBoxLeft: backgroundImage ? parseFloat(fitImageToCanvas(backgroundImage).x) : 0,
                boundingBoxWidth: backgroundImage ? parseFloat(fitImageToCanvas(backgroundImage).width) : 0,
                boundingBoxHeight: backgroundImage ? parseFloat(fitImageToCanvas(backgroundImage).height) : 0,
                width: parseInt(widthInput.value) || 100,
                height: parseInt(heightInput.value) || 100
            };

            // Create form data
            const formData = new FormData();
            formData.append('data', JSON.stringify(photoData));
            formData.append('surface_id', surfaceId);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            // Show loading state
            const saveButton = document.getElementById('saveLayout');
            const originalText = saveButton.textContent;
            saveButton.textContent = 'Saving...';
            saveButton.disabled = true;

            // Send AJAX request
            fetch(`/photo/${photoId}`, {
                method: 'POST',
                body: formData
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                if (data.success) {
                    // Update the UI if needed
                    if (currentImageElement) {
                        currentImageElement.dataset.corners = JSON.stringify(corners);
                    }

                    // Close modal
                    const modal = bootstrap.Modal.getInstance(imageModal);
                    if (modal) {
                        modal.hide();
                    }

                    // Optional: Show success message
                } else {
                    throw new Error(data.message || 'Failed to update photo');
                }
            })
            .catch(error => {
                console.error('Error updating photo:', error);
                alert('Error saving changes: ' + error.message);
            })
            .finally(() => {
                // Reset button state
                saveButton.textContent = originalText;
                saveButton.disabled = false;
            });
        });


        function handleAddPhotoState(e) {
            e.preventDefault();

            // Get project ID from hidden input
            const projectId = document.getElementById('project-id').value;

            // Validate if we have photos data
            if (!photosData || photosData.length === 0) {
                alert('No photos available to save state');
                return;
            }

            // Create request data
            const requestData = {
                project_id: projectId,
                photos: photosData
            };

            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').content;

            // Send request to save photo state
            fetch('/photo-state/store', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: JSON.stringify(requestData)
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Reload the page to show updated layout
                    location.reload();
                } else {
                    throw new Error(data.message || 'Failed to save photo state');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving photo state: ' + error.message);
            });
        }

        // Add click handler for duplicate images button
        // document.getElementById('duplicateImages').addEventListener('click', handleDuplication);

        // Add click handler for all "Add Layout" buttons
        document.addEventListener('click', function(e) {
            if (e.target.closest('.add-image-btn') && e.target.closest('.card-layout')) {
                e.preventDefault();
                handleAddPhotoState(e);
            }
        });


    });

    document.addEventListener('show.bs.modal', function (event) {
        const modals = document.querySelectorAll('.modal.show');
        modals.forEach(function (modal) {
            if (modal !== event.target) {
                bootstrap.Modal.getInstance(modal).hide();
            }
        });
    });


    const projectModal = document.getElementById('projectModal');
    const modalTitle = document.getElementById('projectModalLabel');
    const projectNameInput = document.getElementById('projectNameInput');
    const imageUploadBox = document.getElementById('imageUploadBox');
    const imageInput = document.getElementById('imageInput');
    const imageName = document.getElementById('imageName');

    // Xử lý khi modal được mở
    projectModal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget; // Nút đã kích hoạt modal
        const mode = button.getAttribute('data-mode'); // Lấy mode (create hoặc edit)

        // Cập nhật tiêu đề và giá trị mặc định dựa trên mode
        if (mode === 'create') {
            modalTitle.textContent = 'Add new project';
            projectNameInput.value = '';
            imageUploadBox.innerHTML = '<span>+ Image</span><div class="overlay">Click to replace image</div>';
            imageUploadBox.appendChild(imageInput);
            imageName.textContent = '';
        } else if (mode === 'edit') {
            modalTitle.textContent = 'Edit project';
            const projectName = button.getAttribute('data-project-name');
            projectNameInput.value = projectName;
            imageUploadBox.innerHTML = '<span>+ Image</span><div class="overlay">Click to replace image</div>';
            imageUploadBox.appendChild(imageInput);
            imageName.textContent = '';
        }
    });

    imageUploadBox.addEventListener('click', () => {
        imageInput.click();
    });

    imageInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.createElement('img');
                img.src = e.target.result;
                imageUploadBox.innerHTML = '';
                imageUploadBox.appendChild(img);
                const overlay = document.createElement('div');
                overlay.className = 'overlay';
                overlay.textContent = 'Click to replace image';
                imageUploadBox.appendChild(overlay);
                imageUploadBox.appendChild(imageInput);
                imageName.textContent = file.name;
            };
            reader.readAsDataURL(file);
        }
    });

    function convertUnits(source) {
        const widthInput = document.getElementById('rectWidth');
        const heightInput = document.getElementById('rectHeight');
        const widthUnitSelect = document.getElementById('widthUnit');
        const heightUnitSelect = document.getElementById('heightUnit');

        let widthValue = parseFloat(widthInput.value);
        let heightValue = parseFloat(heightInput.value);

        if (isNaN(widthValue) || isNaN(heightValue)) return;

        let newUnit;
        if (source === 'width') {
            newUnit = widthUnitSelect.value;
            heightUnitSelect.value = newUnit;
        } else {
            newUnit = heightUnitSelect.value;
            widthUnitSelect.value = newUnit;
        }
        if (newUnit === 'inch') {
            widthInput.value = (widthValue / 2.54).toFixed(2);
            heightInput.value = (heightValue / 2.54).toFixed(2);
        } else {
            widthInput.value = (widthValue * 2.54).toFixed(2);
            heightInput.value = (heightValue * 2.54).toFixed(2);
        }
    }

    function updateValues(source) {
        const widthInput = document.getElementById('rectWidth');
        const heightInput = document.getElementById('rectHeight');
        const currentUnit = document.getElementById('widthUnit').value;

        let widthValue = parseFloat(widthInput.value);
        let heightValue = parseFloat(heightInput.value);

        if (isNaN(widthValue) || isNaN(heightValue)) return;

        let widthInCm = currentUnit === 'inch' ? widthValue * 2.54 : widthValue;
        let heightInCm = currentUnit === 'inch' ? heightValue * 2.54 : heightValue;

        if (source === 'width') {
            widthInCm = currentUnit === 'inch' ? widthValue * 2.54 : widthValue;
            heightInput.value = currentUnit === 'inch' ? (heightInCm / 2.54).toFixed(2) : heightInCm.toFixed(2);
        } else {
            heightInCm = currentUnit === 'inch' ? heightValue * 2.54 : heightValue;
            widthInput.value = currentUnit === 'inch' ? (widthInCm / 2.54).toFixed(2) : widthInCm.toFixed(2);
        }
    }

    document.getElementById('rectWidth').addEventListener('input', function() {
        updateValues('width');
    });

    document.getElementById('rectHeight').addEventListener('input', function() {
        updateValues('height');
    });
</script>

{{--<script src="{{ mix('js/modules/photo/index.js') }}"></script>--}}
@endpush

@push('styles')
<link href="{{ mix('css/page/photo-index.css') }}" rel="stylesheet">
@endpush
