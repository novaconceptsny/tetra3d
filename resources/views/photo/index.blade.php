@extends('layouts.redesign')

@section('content')

    <div id="project-list">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="favourites-section">
                        <h5 class="mb-4">Favourites</h5>
                        <div class="favourite-items">
                            <div class="row">
                                @if($favorites->count() > 0)
                                    @foreach($favorites as $favorite)
                                        <div class="col-md-3">
                                            <div class="bg-light rounded p-3">
                                                <h4><i class="fa fa-star"></i> {{ $favorite->photo->name }}</h4>
                                                <span>{{ $favorite->photo->project->name }}</span>
                                                <p class="text-end mb-0">
                                                    <button class="btn enter-link" onclick="navigateToPhoto({{ $favorite->photo->id }}, {{ $favorite->layout_id }})">
                                                        Enter
                                                    </button>
                                                </p>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12">
                                        <p class="text-center">No favorites found.</p>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    <div class="projects-section">
                        <div class="d-flex justify-content-between align-items-center mb-3">
                            <h5>Tishman Speyer</h5>
                            <div class="sort-dropdown">
                                <select class="form-select">
                                    <option>Recently added</option>
                                    <!-- Add other sort options -->
                                </select>
                            </div>
                        </div>
                        <div class="layout-section">
                            <div class="row">
                                @if($projects->count() > 0)
                                    @foreach($projects as $c_project)
                                        <div class="col-md-3 layout-item">
                                            <div class="card border-0 shadow-sm bg-white">
                                                <div class="rounded img-home p-2">
                                                    <img src="{{ $c_project->background_url }}" class="card-img-top img-fluid" alt="{{ $c_project->title }}">
                                                </div>
                                                <div class="card-body d-flex justify-content-between align-items-end">
                                                    <p class="card-text">
                                                        <span>{{ $c_project->name }}</span><br>
                                                        <small>Created: {{ $c_project->created_at->format('F jS, Y') }}</small>
                                                    </p>
                                                    <button type="button"
                                                            class="btn enter-link"
                                                            id="enterProjectBtn"
                                                            onclick="enterProject({{ $c_project->id }})"
                                                    >
                                                        Enter
                                                    </button>
                                                </div>
                                            </div>
                                        </div>
                                    @endforeach
                                @else
                                    <div class="col-12">
                                        <p class="text-center">No projects found.</p>
                                    </div>
                                @endif
                                <div class="col-md-3 layout-item">
                                    <div class="card bg-white card-layout">
                                        <button class="add-image-btn create-new-box" data-mode="create" data-bs-toggle="modal" data-bs-target="#projectModal">
                                            <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                            <span class="add-image-text">Create New Project</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div id="project-content" style="display: none;">
        <header class="header bg-white">
            <div class="header-logo border-top">
                <div class="container">
                    <div class="row align-items-center py-3">
                        <div class="col">
                            <div class="project-name" id="project-name"></div>
                        </div>
                        <div class="col text-end">
                            <button class="btn btn-primary" data-mode="edit"  data-bs-toggle="modal" data-bs-target="#projectModal"><i class="fas fa-pen"></i> Edit project</button>
                        </div>
                    </div>
                </div>
            </div>
        </header>
        <!-- Main Content -->
        <div class="container main-content my-5" id="projectContainer">
            <div class="row">
                <!-- Sidebar: Collections -->
                <div class="col-md-6 col-xl-2 order-md-1 mb-3">
                    <div class="sidebar bg-white rounded overflow-hidden">
                        <div class="title-box" >Collections <a href="#" class="enter-link fw-normal" data-bs-toggle="modal" data-bs-target="#collectionsModal">Enter</a></div>
                        <ul class="list-group" id="collectionsContainer">
                            <li class="list-group-item d-flex align-items-center border rounded p-2 mb-2" id="addCollectionBtn">
                                <button class="add-collection-btn" data-bs-toggle="modal" data-bs-target="#addCollectionModal">
                                    <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                    <span class="add-collection-text">Add Collection</span>
                                </button>
                            </li>
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

                        <ul class="list-group" id="surfacesContainer">
                            <li class="list-group-item d-flex justify-content-center align-items-center card surface-item">
                                <button class="add-image-btn" data-bs-toggle="modal" data-bs-target="#surfaceModal" data-action="add">
                                    <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                    <span class="add-image-text">Add Surface</span>
                                </button>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Layout Sections -->
            <div id="layoutSections">

            </div>

        </div>
    </div>

        <!-- Modal for Add Collection -->
        @if(isset($project))
            <div class="modal fade" id="addCollectionModal" tabindex="-1" aria-labelledby="addCollectionModalLabel"
                aria-hidden="true">
                <div class="modal-dialog">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="addCollectionModalLabel">Collections</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <select name="collection_id" class="form-control" id="allCollections" required>
                                <option value="">Select Collection</option>
                            </select>
                        </div>
                        <div class="modal-footer">
                            <button type="submit" class="btn btn-primary" onclick="handleAddCollection()">Update</button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

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
                        <button type="button" class="btn btn-danger" id="confirmDeleteButton" onclick="handleDelete()">Delete</button>
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
                        <button type="button" id="addSurfaces" class="btn btn-light w-100" data-bs-dismiss="modal" onclick="handleAddSurfaces()">Update</button>
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
                                         <option id="surfaceDefault"  data-width=100"
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


        <div class="modal fade" id="editPhotoModal" tabindex="-1" aria-labelledby=" editPhotoModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="editPhotoModalLabel">Edit Photo</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <div class="modal-body">
                        <div class="mb-3">
                            <input type="text" class="form-control" id="photoName" placeholder="Name" value= data-title>
                        </div>
                        <div class="image-upload-box mb-3" id="photoUploadBox" onclick="handlePhotoUpload()">
                            <input type="file" class="image-input" id="photoInput" accept="image/jpeg, image/png">
                            <span>+ Image</span>
                            <div class="overlay"></div>
                        </div>
                        <button type="button" class="btn btn-save btn-success text-white">Update</button>
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
                        <div class="collection-list" id="collectionsContainer2">
                            <div class="collection-item" id="addCollectionBtn2">
                                <button class="add-collection-btn" data-bs-toggle="modal" data-bs-target="#addCollectionModal">
                                    <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                    <span class="add-collection-text">Add Collection</span>
                                </button>
                            </div>
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
                            <div class="row list-unstyled" id="surfacesContainer2">
                                <div class="col-md-4 surface-item">
                                    <button class="add-surface-btn mb-3" data-bs-toggle="modal" data-bs-target="#surfaceModal" data-action="add">
                                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                            <circle cx="12" cy="12" r="12" fill="#28a745"/>
                                            <path d="M12 6V18M6 12H18" stroke="white" stroke-width="2"/>
                                        </svg>
                                        Add Surface
                                    </button>
                                </div>
                                <!-- @foreach($surfaces as $surface)
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
                                @endforeach -->
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
                        <div class="mb-3">
                            <input type="text" class="form-control" id="projectNameInput" placeholder="Name">
                        </div>
                        <div class="image-upload-box mb-3" id="imageUploadBox" onclick="handleImageUpload()">
                            <input type="file" class="image-input" id="imageInput" accept="image/jpeg, image/png">
                            <span>+ Image</span>
                            <div class="overlay">Click to replace image</div>
                        </div>
                        <div class="image-name" id="imageName"></div>
                        <button type="button" class="btn btn-save" id="saveProject" onclick="saveProject()">Save</button>
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
    <link href="{{ mix('css/page/tour360.css') }}" rel="stylesheet">
@endsection

@push('scripts')
<script>

    let photosData = [];
    let surfacesData = [];
    let collectionsData = [];
    let allCollections = [];
    let photoStateData = [];
    let selectedImages = [];

    let projectName = "";
    let projectId = 0;


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
    const currentPhotos = @json($photos);
    const layoutPhotos = @json($layoutPhotos);
    const currentSurfaces = @json($surfaces);
    const currentCollections = [];

    // if project_id is in the url, set projectId to the project_id using new urlsearchparams
    if (window.location.search.includes('project_id=')) {
        projectId = new URLSearchParams(window.location.search).get('project_id');
        enterProject(projectId);
    }


    function getPhotosData(data) {
        const result = [];

        if (data && data.length > 0) {
            data.forEach(photo => {
                result.push({
                    id: String(photo.id),
                    background_url: photo.background_url,
                    thumbnail_url: photo.thumbnail_url,
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

        return result;
    }
    
    function getPhotoStateData(layoutData) {
        const result = [];
        if (layoutData && Object.keys(layoutData).length > 0) {
            Object.keys(layoutData).forEach(layoutId => {
                const layout = layoutData[layoutId];
                result.push({
                    id: String(layout.layout_id),
                    name: layout.name,
                    layout_id: layout.layout_id,
                    thumbnail_urls: layout.thumbnail_urls,
                    photos: layout.photos,
                is_favorites: layout.is_favorites
                });
            });
        }
        return result;
    }

    function getSurfacesData(data) {
        const result = [];
        if (data && data.length > 0) {
            data.forEach(surface => {
                result.push({
                    id: String(surface.id),
                    name: surface.name,
                    data: surface.data,
                });
            });
        }
        return result;
    }

    function getCollectionsData(data) {
        const result = [];
        if (data && data.length > 0) {
            data.forEach(artworkCollection => {
                result.push({
                    id: String(artworkCollection.id),
                    name: artworkCollection.name,
                    artworks_count: artworkCollection.artworks_count
                });
            });
        }
        return result;
    }

    photoStateData = getPhotoStateData(layoutPhotos);
    photosData = getPhotosData(currentPhotos);
    surfacesData = getSurfacesData(currentSurfaces);
    collectionsData = getCollectionsData(currentCollections);

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
        const modal = bootstrap.Modal.getInstance(document.getElementById('addImageModal'));
        const formData = new FormData();

        // Add project_id to FormData
        console.log("projectId", projectId);
        formData.append('project_id', projectId);

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
                modal.hide();
                selectedImages = [];
                photosData = getPhotosData(data.updatedPhotos);
                renderPhotos(photosData);
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
        formData.append('project_id', projectId);
        const token = document.querySelector('meta[name="csrf-token"]');
        if (!token) {
            throw new Error('CSRF token not found');
        }

        // Send the FormData to the server
        return fetch('/photo/'+module+'/destroy/'+id, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token.content,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
    }

    function handleDelete() {

        if (itemToDelete) {
            const id = itemToDelete.getAttribute('data-id');
            const module = itemToDelete.getAttribute('data-module');

            if (module === 'photo') {
                photosData = photosData.filter(item => item.id !== id);
                renderPhotos(photosData);
            }else if (module === 'surface') {
                surfacesData = surfacesData.filter(item => item.id !== id);
                renderSurfaces(surfacesData);
            }else if (module === 'collection') {
                collectionsData = collectionsData.filter(item => item.id !== id);
                renderCollections(collectionsData);
            }

            deleteItem(id, module);
            itemToDelete.remove();
            itemToDelete = null;
            const modal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
            modal.hide();
        } else {
            console.error('No item to delete. itemToDelete is not defined.');
        }
    }

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

    function handleAddCollection() {

        // Get the selected collection ID
        const select = document.querySelector('select[name="collection_id"]');
        const collectionId = select.value;
        const collectionName = select.options[select.selectedIndex].text;

        if (!collectionId) {
            alert('Please select a collection.');
            return;
        }

        // Prepare form data
        const formData = new FormData();
        formData.append('collection_name', collectionName);
        formData.append('project_id', projectId);

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').content;

        // Send AJAX request
        fetch('/photo/collections/update', {
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
                // Optionally update the UI, close modal, etc.
                const modal = bootstrap.Modal.getInstance(document.getElementById('addCollectionModal'));
                modal.hide();
                // Update the collections list in the UI
                if (data.updatedCollections) {
                    collectionsData = getCollectionsData(data.updatedCollections);
                    renderCollections(collectionsData);
                }
            } else {
                alert('Error: ' + (data.message || 'Could not add collection.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding collection.');
        });
    }

    function handleAddSurfaces() {
        const surfaceName = document.getElementById('surfaceName').value;
        const surfaceWidth = document.getElementById('surfaceWidth').value;
        const surfaceHeight = document.getElementById('surfaceHeight').value;

        if (surfaceName && surfaceWidth && surfaceHeight) {
            const formData = new FormData();
            formData.append('name', surfaceName);
            formData.append('width', surfaceWidth);
            formData.append('height', surfaceHeight);
            formData.append('project_id', projectId);

            if (currentIndex !== null) {
                // Edit mode - add surface ID
                const surfaceItem = document.querySelectorAll('.list-group-item')[currentIndex];
                const surfaceId = surfaceItem.getAttribute('data-id');
                formData.append('surface_id', surfaceId);
            }

            // Get CSRF token
            const token = document.querySelector('meta[name="csrf-token"]').content;

            // Send AJAX request
            fetch('/photo/surface/store', {
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
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(surfaceModal);
                    modal.hide();
                    surfacesData = getSurfacesData(data.updatedSurfaces);
                    renderSurfaces(surfacesData);

                } else {
                    throw new Error(data.message || 'Failed to save surface');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving surface: ' + error.message);
            });

        } else {
            alert('Please fill in all information!');
        }
    }

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


    function renderPhotos(photosData) {
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
                        <img src="${photo.thumbnail_url}" class="card-img-top img-fluid" alt="${photo.name}">
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
                                    data-image="${photo.background_url}"
                                    data-photo-id="${photo.id}">Surface Size</a></li>
                                <li><a class="dropdown-item edit-photo" href="#"  data-bs-toggle="modal" data-bs-target="#editPhotoModal"
                                    data-title="${photo.name}"
                                    data-image="${photo.background_url}"
                                    data-photo-id="${photo.id}">Edit</a></li>
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
                        <img src="${photo.thumbnail_url}" class="card-img-top img-fluid" alt="${photo.name}">
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
                                    data-image="${photo.background_url}"
                                    data-photo-id="${photo.id}">Surface Size</a></li>
                                <li><a class="dropdown-item edit-photo" href="#"  data-bs-toggle="modal" data-bs-target="#editPhotoModal"
                                    data-title="${photo.name}"
                                    data-image="${photo.background_url}"
                                    data-photo-id="${photo.id}">Edit</a></li>
                                <li><a class="dropdown-item delete-item" href="#" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Delete</a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            `;
            photosContainer2.appendChild(colDiv);
        });
    }

    function renderAllCollections(allCollections) {
        const allCollectionsSelect = document.getElementById('allCollections');

        allCollections.forEach(collection => {
            const option = document.createElement('option');
            option.value = collection.id;
            option.textContent = collection.name;
            allCollectionsSelect.appendChild(option);
        });
        
    }

    function renderCollections(collectionsData) {
        const collectionsContainer = document.getElementById('collectionsContainer');
        const collectionsContainer2 = document.getElementById('collectionsContainer2');

        const addCollectionBtn = collectionsContainer.querySelector('#addCollectionBtn');
        collectionsContainer.innerHTML = '';
        collectionsContainer.appendChild(addCollectionBtn);;

        const addCollectionBtn2 = collectionsContainer2.querySelector('#addCollectionBtn2');
        collectionsContainer2.innerHTML = '';
        collectionsContainer2.appendChild(addCollectionBtn2);;

        collectionsData.forEach(collection => {
            const colDiv = document.createElement('div');
            colDiv.dataset.module = 'collection';
            colDiv.dataset.id = `${collection.id}`;
            colDiv.innerHTML = `
                <li class="list-group-item d-flex align-items-center border rounded p-2 mb-2" data-module="artworks" data-id="${collection.id}">
                    <i class="fas fa-image collection-icon"></i>
                    <div class="collection-info">
                        <span class="collection-name">${collection.name}</span>
                        <span class="collection-items">${collection.artworks_count} items</span>
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
            `;
           collectionsContainer.appendChild(colDiv);

            const itemDiv = document.createElement('div');
            itemDiv.classList.add('col-md-3', 'photo-item');
            itemDiv.dataset.module = 'photo';
            itemDiv.dataset.id = `${collection.id}`;
            itemDiv.innerHTML = `
                <div class="collection-item w-100">
                    <span>${collection.name}</span>
                </div>

            `;
           collectionsContainer2.appendChild(itemDiv);
        });

    }

    function renderSurfaces(surfacesData) {
        const surfacesContainer2 = document.getElementById('surfacesContainer2');
        const surfacesContainer = document.getElementById('surfacesContainer');
        const surfaceSelect = document.getElementById('surfaceId');

        const addImageCard = surfacesContainer.querySelector('.surface-item');
        surfacesContainer.innerHTML = '';
        surfacesContainer.appendChild(addImageCard);

        const addImageCard2 = surfacesContainer2.querySelector('.surface-item');
        surfacesContainer2.innerHTML = '';
        surfacesContainer2.appendChild(addImageCard2);

        surfaceSelect.innerHTML ="";

        surfacesData.forEach(surface => {
            const colDiv = document.createElement('div');
            colDiv.dataset.module = 'surface';
            colDiv.dataset.id = `${surface.id}`;
            colDiv.innerHTML = `
                <li class="list-group-item d-flex justify-content-center align-items-center card surface-item"
                    data-name="${surface.name}"
                    data-id="${surface.id}"
                    data-module="surfaces"
                    data-width="${surface.data['img_width'] ?? ''}"
                    data-height="${surface.data['img_height'] ?? ''}">
                    <span class="surface-name">${surface.name}</span>
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
            `;
            surfacesContainer.appendChild(colDiv);

            const itemDiv = document.createElement('div');
            itemDiv.classList.add('col-md-4');
            itemDiv.dataset.module = 'surface';
            itemDiv.dataset.id = `${surface.id}`;
            itemDiv.innerHTML = `

                <div class="list-group-item d-flex justify-content-center align-items-center card surface-item mb-3"
                    data-name="${surface.name}"
                    data-id="${surface.id}"
                    data-module="surfaces"
                    data-width="${surface.data['img_width'] ?? ''}"
                    data-height="${surface.data['img_height'] ?? ''}">
                    <span class="surface-name">${surface.name}</span>
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

            `;
            surfacesContainer2.appendChild(itemDiv);

            const surfaceItem = document.createElement('option');
            surfaceItem.value = surface.id;
            surfaceItem.setAttribute('data-width', surface.data['img_width'] ?? '');
            surfaceItem.setAttribute('data-height', surface.data['img_height'] ?? '');
            surfaceItem.textContent = surface.name;
            surfaceSelect.appendChild(surfaceItem);
        })
    }

    function renderLayouts(photoStateData) {

        const layoutSections = document.getElementById('layoutSections');
        layoutSections.innerHTML = '';

        if (photoStateData && photoStateData.length > 0) {
            photoStateData.forEach(layout => {
                const colDiv = document.createElement('div');
                colDiv.dataset.module = 'photo';
                colDiv.dataset.id = `${layout.id}`;
                colDiv.innerHTML = `
                    <div class="layout-section mt-5">
                        <div class="title-box">${layout.name}</div>
                        <div class="row g-3" id="layout${layout.layout_id}Container" data-layout-id="${layout.layout_id}">
                            ${layout.photos.map((photoId, index) => {
                                const photo = photosData.find(p => p.id === String(photoId));
                                if (!photo) return '';

                                return `
                                    <div class="col-md-3 layout-item">
                                        <div class="card shadow-sm bg-white image-item">
                                            <div class="overflow-hidden img-home">
                                                ${layout.thumbnail_urls && layout.thumbnail_urls[index] ?
                                                    `<img src="${layout.thumbnail_urls[index]}" class="card-img-top img-fluid" alt="${photo.name}">`
                                                    : ''}
                                            </div>
                                            <div class="card-body d-flex justify-content-between align-items-end">
                                                <p class="card-text">
                                                    <span>${photo.name}</span><br>
                                                    <small>Created: ${new Date(photo.created_at).toLocaleDateString()}</small>
                                                </p>
                                                <button type="button"
                                                        class="btn btn-link favorite-btn ${layout.is_favorites && layout.is_favorites[index] ? 'active' : ''}"
                                                        data-photo-id="${photo.id}"
                                                        data-layout-id="${layout.layout_id}"
                                                        onclick="toggleFavorite(this)">
                                                    <i class="fas fa-star"></i>
                                                </button>
                                                <button type="button"
                                                        class="btn enter-link"
                                                        onclick="navigateToPhoto(${photo.id}, ${layout.layout_id})"
                                                        data-photo-id="${photo.id}"
                                                        data-layout-id="${layout.layout_id}">
                                                    Enter
                                                </button>
                                            </div>
                                        </div>
                                    </div>
                                `;
                            }).join('')}
                            ${layout === photoStateData[photoStateData.length - 1] ? `
                                <div class="col-md-3 layout-item">
                                    <div class="card bg-white card-layout">
                                        <button class="add-image-btn" id="addLayoutBtn" onclick="handleAddPhotoState()">
                                            <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                            <span class="add-image-text">Add Layout</span>
                                        </button>
                                    </div>
                                </div>
                            ` : ''}
                        </div>
                    </div>
                `;
                layoutSections.appendChild(colDiv);
            });
        } else {
            const colDiv = document.createElement('div');
            colDiv.innerHTML = `
                <div class="layout-section">
                    <div class="title-box">No layouts available</div>
                    <div class="col-md-3 layout-item">
                        <div class="card bg-white card-layout">
                            <button class="add-image-btn" id="addLayoutBtn" onclick="handleAddPhotoState()">
                                <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                <span class="add-image-text">Add Layout</span>
                            </button>
                        </div>
                    </div>
                </div>
            `;
            layoutSections.appendChild(colDiv);
        }

    }

    document.addEventListener('DOMContentLoaded', () => {

        document.getElementById('project-name').textContent = projectName;

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

            // Fill the polygon with green color (with some transparency for better visibility)
            ctx.fillStyle = 'rgba(0, 255, 0, 0.2)'; // 0.2 = 20% opacity
            ctx.fill();

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

            // Get the PNG data URL from the canvas
            const canvas = document.getElementById('imageCanvas');
            const thumbnailDataUrl = canvas.toDataURL('image/png');

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
            formData.append('project_id', projectId);
            formData.append('data', JSON.stringify(photoData));
            formData.append('surface_id', surfaceId);
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            // Add the thumbnail image as a data URL
            formData.append('thumbnail', thumbnailDataUrl);

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

                    photosData = getPhotosData(data.updatedPhotos);
                    renderPhotos(photosData);
                    location.reload();
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




        // Add click handler for duplicate images button
        // document.getElementById('duplicateImages').addEventListener('click', handleDuplication);



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
    const photoUploadBox = document.getElementById('photoUploadBox');

    const imageInput = document.getElementById('imageInput');
    const photoInput = document.getElementById('photoInput');
    const imageName = document.getElementById('imageName');
    const photoName = document.getElementById('photoName');

    let mode = '';

    function navigateToPhoto(photoId, layoutId) {
        window.location.href = `/photos/${photoId}?layout_id=${layoutId}`;
    }
    // Xử lý khi modal được mở
    projectModal.addEventListener('show.bs.modal', (event) => {
        const button = event.relatedTarget; // Button that triggered the modal
        mode = button.getAttribute('data-mode'); // Get mode (create or edit)

        // Update title and default values based on mode
        if (mode === 'create') {
            modalTitle.textContent = 'Add new project';
            projectNameInput.value = '';
            imageUploadBox.innerHTML = '<span>+ Image</span><div class="overlay">Click to replace image</div>';
            imageUploadBox.appendChild(imageInput);
            imageName.textContent = '';
        } else if (mode === 'edit') {
            modalTitle.textContent = 'Edit project';
            projectNameInput.value = projectName;
            imageUploadBox.innerHTML = '<span>+ Image</span><div class="overlay">Click to replace image</div>';
            imageUploadBox.appendChild(imageInput);
            imageName.textContent = '';
        }
    });

    function handleImageUpload() {
        imageInput.click();
    }

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

    function handlePhotoUpload() {
        photoInput.click();
    }

    photoInput.addEventListener('change', (event) => {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.createElement('img');
                img.src = e.target.result;
                photoUploadBox.innerHTML = '';
                console.log(img, "photoUploadBox");
                photoUploadBox.appendChild(img);
                const overlay = document.createElement('div');
                overlay.className = 'overlay';
                overlay.textContent = 'Click to replace image';
                photoUploadBox.appendChild(overlay);
                photoUploadBox.appendChild(photoInput);
                photoName.textContent = file.name;

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

    // Add this code to handle edit photo modal
    const editPhotoModal = document.getElementById('editPhotoModal');
    editPhotoModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const title = button.getAttribute('data-title');
        const image = button.getAttribute('data-image');
        const photoId = button.getAttribute('data-photo-id');

        // Set the photo name in the input field
        const photoNameInput = this.querySelector('#photoName');
        if (photoNameInput) {
            photoNameInput.value = title;
        }

        // Store the photo ID on the modal for use when saving
        this.setAttribute('data-photo-id', photoId);


        if (photoUploadBox && image) {
            const img = document.createElement('img');
            img.src = image;
            photoUploadBox.innerHTML = '';
            photoUploadBox.appendChild(img);
            const overlay = document.createElement('div');
            overlay.className = 'overlay';
            overlay.textContent = 'Click to replace image';
            overlay.style.opacity = '0'; // Start with overlay hidden
            photoUploadBox.appendChild(overlay);

            // Add hover event listeners
            photoUploadBox.addEventListener('mouseenter', () => {
                overlay.style.opacity = '1';
            });

            photoUploadBox.addEventListener('mouseleave', () => {
                overlay.style.opacity = '0';
            });
        }
    });

      // Add this code to handle edit photo modal
      document.querySelector('#editPhotoModal .btn-save').addEventListener('click', function() {
        const modal = document.getElementById('editPhotoModal');
        const photoId = modal.getAttribute('data-photo-id');
        const photoName = modal.querySelector('#photoName').value;

        const formData = new FormData();
        formData.append('name', photoName);
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

        if (photoInput.files.length > 0) {
            formData.append('image', photoInput.files[0]);
        }

        // Show loading state
        const saveButton = this;
        const originalText = saveButton.textContent;
        saveButton.textContent = 'Updating...';
        saveButton.disabled = true;

        fetch(`/photo/${photoId}/edit`, {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update the UI
                const photoCard = document.querySelector(`.photo-item[data-id="${photoId}"]`);
                if (photoCard) {
                    photoCard.querySelector('.card-text').textContent = photoName;
                    if (data.photo.background_url) {
                        photoCard.querySelector('img').src = data.photo.background_url;
                    }
                }

                // Close modal
                const modalInstance = bootstrap.Modal.getInstance(modal);
                modalInstance.hide();

            } else {
                throw new Error(data.message || 'Failed to update photo');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error updating photo: ' + error.message);
        })
        .finally(() => {
            // Reset button state
            saveButton.textContent = originalText;
            saveButton.disabled = false;
        });
    });
    // Add this code to handle edit photo modal
    function toggleFavorite(button) {
        const photoId = button.getAttribute('data-photo-id');
        const layoutId = button.getAttribute('data-layout-id');
        const isFavorite = button.classList.contains('active');

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').content;
        // Send request to toggle favorite status
        fetch(`/photo/${photoId}/toggle-favorite`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: JSON.stringify({
                is_favorite: !isFavorite,
                layout_id: layoutId,
                photoId: photoId
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Toggle active class
                button.classList.toggle('active');
            } else {
                console.error('Error toggling favorite:', data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
    }

    function enterProject(id) {
        // Hide project list and show project content
        document.getElementById('project-list').style.display = 'none';
        document.getElementById('project-content').style.display = 'block';

        // change current url to have param proejct id = id
        window.history.pushState({}, '', window.location.pathname + '?project_id=' + id);
        // Fetch project data and update the content
        fetch(`/photo/projects/${id}`)
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update project name
                    document.getElementById('project-name').textContent = data.name;
                    projectName = data.name;
                    projectId = data.id;
                    photosData = getPhotosData(data.photos);
                    surfacesData = getSurfacesData(data.surfaces);
                    collectionsData = getCollectionsData(data.artworkCollections);
                    allCollections = getCollectionsData(data.allCollections);
                    console.log(allCollections, "55555555555555555");
                    photoStateData = getPhotoStateData(data.layoutPhotos);
                    renderPhotos(photosData);
                    renderCollections(collectionsData);
                    renderSurfaces(surfacesData);
                    renderLayouts(photoStateData);
                    renderAllCollections(allCollections);

                } else {
                    throw new Error(data.message || 'Failed to load project data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error loading project data: ' + error.message);
            });
    }

    function handleAddPhotoState(e) {

        const id = projectId;
        // Validate if we have photos data
        if (!photosData || photosData.length === 0) {
            alert('No photos available to save state');
            return;
        }

        // Create request data
        const requestData = {
            project_id: id,
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
                photoStateData = getPhotoStateData(data.layoutPhotos);
                renderLayouts(photoStateData);
            } else {
                throw new Error(data.message || 'Failed to save photo state');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving photo state: ' + error.message);
        });
    }

    function saveProject() {
        const projectName = document.getElementById('projectNameInput').value;
        const imageInput = document.getElementById('imageInput');
        const file = imageInput.files[0];

        if (!projectName) {
            alert('Please enter a project name');
            return;
        }

        if(!file) {
            alert('Please upload a project image');
            return;
        }

        const formData = new FormData();
        formData.append('name', projectName);
        if (file) {
            formData.append('image', file);
        }

        if(mode === 'edit') {
            formData.append('project_id', projectId);
        }

        // Get CSRF token
        const token = document.querySelector('meta[name="csrf-token"]').content;

        // Send AJAX request
        // fetch('/photos/store-project', {
        const url = mode === 'create' ? '/photos-store-project' : '/photos-update-project';
        fetch(url, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
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
                // Close modal
                const modal = bootstrap.Modal.getInstance(document.getElementById('projectModal'));
                modal.hide();

                // Refresh the page to show new project
                location.reload();
            } else {
                throw new Error(data.message || 'Failed to save project');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error saving project: ' + error.message);
        });
    }

</script>

{{--<script src="{{ mix('js/modules/photo/index.js') }}"></script>--}}
@endpush

@push('styles')
<link href="{{ mix('css/page/photo-index.css') }}" rel="stylesheet">
@endpush
