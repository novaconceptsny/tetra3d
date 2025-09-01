@extends('layouts.redesign')

@section('content')
<div class="container">
    <div id="dashboard-section" class="row">
        <div class="col-12">
            <div class="favourites-section">
                <div class="d-flex align-items-center mb-4">
                    <h5 class="mb-4">Favourites</h5>
                    @if(auth()->user() && auth()->user()->isSuperAdmin())
                        <button id="toggleFavouritesBtn" class="btn btn-link ms-2 mb-4" title="Show/Hide Favourites" style="font-size: 1.2rem;">
                            <i id="favouritesEyeIcon" class="fas fa-eye"></i>
                        </button>
                    @endif
                </div>
                <div id="favouritesSection">
                    <div class="favourite-items" id="favoritesContainer">
                        <div class="row">
                            @if($favorites->count() > 0)
                                @foreach($favorites as $favorite)
                                    <div class="col-md-3 favourite-card" data-favorite-id="{{ $favorite->id }}">
                                        <div class="bg-light rounded p-3">
                                            <div class="d-flex justify-content-between align-items-start mb-2">
                                                <h4 class="mb-0">
                                                    <i class="fas fa-star text-primary favorite-star" 
                                                       onclick="removeFavorite({{ $favorite->id }})" 
                                                       title="Remove from favorites"
                                                       style="cursor: pointer;"></i> 
                                                    {{ $favorite->name }}
                                                </h4>
                                            </div>
                                            <span>{{ $favorite->assignedTour()->name }}</span>
                                            <div class="text-end mb-0 mt-3 d-flex justify-content-end">
                                                <a href="{{ route('tours.show', [$favorite->tour_id, 'layout_id' => $favorite->id]) }}" class="btn-enter">
                                                    Enter
                                                </a>
                                            </div>
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
            </div>

            <div class="projects-section">
                @if($companies->count() > 0)
                    @foreach($companies as $company)
                        <div class="company-section mb-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>
                                    @if(user()->isAdmin() && $company->name === 'My Workspace')
                                        {{ $company->name }}_{{ str_pad($company->id, 2, '0', STR_PAD_LEFT) }}
                                    @else
                                        {{ $company->name }}
                                    @endif
                                </h5>
                                <div class="sort-dropdown">
                                    <select class="form-select">
                                        <option>Recently added</option>
                                        <!-- Add other sort options -->
                                    </select>
                                </div>
                            </div>
                            <div class="layout-section">
                                <div class="row">
                                    <!-- Create New Project Card - First in List -->
                                    <div class="col-md-3 layout-item">
                                        <div class="card bg-white card-layout">
                                            <button
                                                class="add-image-btn create-new-box"
                                                onclick="openCreateProject({{ $company->id }})"
                                            >
                                                <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                                <span class="add-image-text">Create New Project</span>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Existing Projects -->
                                    @if($company->projects->count() > 0)
                                        @foreach($company->projects as $project)
                                            <div class="col-md-3 layout-item">
                                                <div class="card border-0 shadow-sm bg-white"
                                                    data-project-id="{{ $project->id }}"
                                                    data-project-name="{{ $project->name }}"
                                                    data-project-units="{{ $project->units }}"
                                                    data-project-tours="{{ json_encode($project->tours->pluck('id')) }}"
                                                    data-project-collections="{{ json_encode($project->artworkCollections->pluck('id')) }}"
                                                    data-project-contributors="{{ json_encode($project->contributors->pluck('id')) }}"
                                                >
                                                    <div class="rounded img-home p-2">
                                                        <img src="{{ $project->background_url }}" class="card-img-top img-fluid" alt="{{ $project->title }}">
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2">
                                                            <p class="card-text mb-0">
                                                                <span>{{ $project->name }}</span>
                                                            </p>
                                                            <div class="action-icons">
                                                                <button class="btn btn-link p-0 me-2" onclick="handleEditProject({{ $project->id }})">
                                                                    <i class="fas fa-edit"></i>
                                                                </button>
                                                                <button class="btn btn-link p-0" onclick="handleDeleteProject({{ $project->id }})">
                                                                    <i class="fas fa-trash"></i>
                                                                </button>
                                                            </div>
                                                        </div>

                                                        <div class="d-flex justify-content-between align-items-center mb-3">
                                                            <div>
                                                                <small>Created: {{ $project->created_at->format('F jS, Y') }}</small><br>
                                                                <span  style="font-size: 0.875rem;">{{ $project->layouts_count ?? $project->layouts()->count() }} layouts</span>
                                                            </div>
                                                            <a href="javascript:void(0)" class="btn-enter" onclick="handleEnterProject({{ $project->id }}, {{ $project->assignedTours()->count() }})">Enter</a>
                                                        </div>
                                                        <hr class="my-2">
                                                        <div class="project-stats d-flex">
                                                            <span><i class="fas fa-cube"></i> {{ $project->assignedTours()->count() ?? 0 }} Tours</span>
                                                            <span class="contributors-count"><i class="fas fa-users"></i> {{ $project->contributors_count ?? 0 }} Contributors</span>
                                                            <span><i class="fas fa-folder"></i> {{ $project->artwork_collections_count ?? 0 }} Collections</span>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="col-12">
                        <p class="text-center">No companies found.</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <div class="create-project-section" id="createProjectSection" style="display: none;">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="modal-title">Add new project</h5>
                <button type="button" class="btn-close close-create-section"></button>
            </div>

            <div class="mb-3">
                <label for="inlineCompanyInput" class="form-label">Company</label>
                <input type="text" class="form-control" id="inlineCompanyInput" placeholder="Company" disabled>
            </div>

            <div class="mb-3">
                <label for="inlineProjectNameInput" class="form-label">Project Name</label>
                <input type="text" class="form-control" id="inlineProjectNameInput" placeholder="Project">
            </div>

            <div class="mb-3">
                <label for="inlineTourSelect" class="form-label">Tour</label>
                <div class="input-group">
                    <select id="inlineTourSelect"  name="states[]" multiple="multiple" style="width: 100%;">
                        <!-- <option  style="padding: 8px 16px;" selected disabled>Select Tour</option> -->
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="inlineCollections" class="form-label">Collections</label>
                <div class="input-group">
                    <select id="inlineCollections"  name="states[]" multiple="multiple" style="width: 100%;">
                        <!-- <option selected disabled>Select Collection</option> -->
                    </select>
                </div>
            </div>

            <div class="mb-3 contributors-section">
                <label for="inlineContributors" class="form-label contributors-label">Contributors</label>
                <div class="input-group">
                    <select id="inlineContributors" class="contributors-select" name="states[]" multiple="multiple" style="width: 100%;">
                        <!-- <option selected disabled>Select Contributor</option> -->
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="inlineUnits" class="form-label">Units</label>
                <select class="form-select" id="inlineUnits">
                    <option value="metric" selected>Metric (cm)</option>
                    <option value="imperial">Imperial (inch)</option>
                </select>
            </div>

            <div class="mb-3 col-md-4">
                <label class="form-label">Thumbnail</label>
                <div class="mb-2" id="inlineImageUploadBox">
                    <input type="file" class="filepond" id="inlineImageInput" accept="image/jpeg, image/png">
                </div>
                <div class="image-name" id="inlineImageName"></div>
            </div>

            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-primary mb-3" id="inlineSaveButton" style="width: 200px" onclick="handleCreateProject()">Create</button>
            </div>
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-danger" id="inlineCancelButton" onclick="closeCreateProject()" style="width: 200px">Cancel</button>
            </div>
        </div>
    </div>

</div>
<!-- Modal -->
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
                <div class="mb-3" id="imageUploadBox">
                    <input type="file" class="filepond" id="imageInput" accept="image/jpeg, image/png">
                </div>
                <div class="image-name" id="imageName"></div>
                <button type="button" class="btn btn-save">Save</button>
            </div>
        </div>
    </div>
</div>

<!-- Warning Modal for No Tours -->
<div class="modal fade" id="noToursModal" tabindex="-1" aria-labelledby="noToursModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="noToursModalLabel">
                    <i class="fas fa-exclamation-triangle text-warning me-2"></i>
                    Warning
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-4">
                    <i class="fas fa-info-circle text-info" style="font-size: 3rem;"></i>
                </div>
                <h6 class="mb-3">No Tours Assigned</h6>
                <p class="text-muted">This project doesn't have any tours assigned. Please add tours to the project before proceeding.</p>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                <button type="button" class="btn btn-primary" id="editProjectBtn">Edit Project</button>
            </div>
        </div>
    </div>
</div>

<!-- Remove Favorite Confirmation Modal -->
<div class="modal fade" id="removeFavoriteModal" tabindex="-1" aria-labelledby="removeFavoriteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="removeFavoriteModalLabel">
                    <i class="fas fa-star text-warning me-2"></i>
                    Remove Favorite
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body text-center">
                <div class="mb-4">
                    <i class="fas fa-question-circle text-warning" style="font-size: 3rem;"></i>
                </div>
                <h6 class="mb-3">Remove from Favorites?</h6>
                <p class="text-muted">Are you sure you want to remove this item from your favorites? This action cannot be undone.</p>
                <div id="favoriteItemInfo" class="mt-3 p-3 bg-light rounded">
                    <!-- Favorite item details will be populated here -->
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-danger" id="confirmRemoveFavoriteBtn">
                    <i class="fas fa-star me-2"></i>Remove from Favorites
                </button>
            </div>
        </div>
    </div>
</div>

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<link href="https://unpkg.com/filepond/dist/filepond.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.css" rel="stylesheet">
<link href="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.css" rel="stylesheet">
@endsection

@section('styles')
    <link href="{{ mix('css/page/tour360.css') }}" rel="stylesheet">

    <style>
        /* Custom Select2 tag style */
        .select2-selection__choice {
            background: #8187f5 !important;
            color: #fff !important;
            border: none !important;
            border-radius: 6px !important;
            font-size: 16px !important;
        }
        .select2-selection__choice__remove {
            color: #fff !important;
            margin-right: 6px;
        }

        /* Add spacing between project columns */
        .layout-item {
            margin-bottom: 50px;
        }

        .layout-item .card {
            height: 100%;
        }

        .btn-enter {
            display: inline;
            background: transparent;
            color: #203DCE;
            padding: 0.5rem;
            text-decoration: none;
            border: none;
            height: auto;
            transition: color 0.2s ease;
        }

        /* Remove the static first-child styles since we'll apply them dynamically */
        .favourite-card {
            margin-bottom: 24px; /* space below each card */
        }

        /* Favorite star styling */
        .favorite-star {
            transition: all 0.2s ease;
        }

        .favorite-star:hover {
            transform: scale(1.2);
            color: #dc3545 !important;
        }

        /* Drag and drop styles for image upload */
        .image-upload-box.drag-over {
            border: 2px dashed #007bff !important;
            background-color: rgba(0, 123, 255, 0.1) !important;
            transform: scale(1.02);
            transition: all 0.2s ease;
        }

        .image-upload-box {
            transition: all 0.2s ease;
            cursor: pointer;
        }

        .image-upload-box:hover {
            border-color: #007bff;
        }

        /* Hide contributors section */
        .contributors-section,
        .contributors-label,
        .contributors-select,
        .contributors-count {
            display: none !important;
        }

        /* FilePond custom styles */
        .filepond--root {
            font-family: inherit;
            border-radius: 8px;
            border: 2px dashed #e0e0e0;
            background-color: #f8f9fa;
            transition: all 0.2s ease;
        }

        .filepond--root:hover {
            border-color: #007bff;
            background-color: #e3f2fd;
        }

        .filepond--root.filepond--drag-over {
            border-color: #007bff;
            background-color: rgba(0, 123, 255, 0.1);
            transform: scale(1.02);
        }

        .filepond--panel-root {
            background-color: transparent;
        }

        .filepond--drop-label {
            color: #6c757d;
            font-size: 14px;
        }

        .filepond--label-action {
            color: #007bff;
            text-decoration: underline;
            cursor: pointer;
        }

        .filepond--item {
            border-radius: 6px;
            overflow: hidden;
        }

        .filepond--image-preview {
            border-radius: 6px;
        }

        /* Responsive FilePond */
        @media (max-width: 768px) {
            .filepond--root {
                font-size: 14px;
            }
            
            .filepond--drop-label {
                font-size: 12px;
            }
        }

        /* FilePond container spacing */
        #inlineImageUploadBox .filepond--root,
        #imageUploadBox .filepond--root {
            margin-bottom: 10px;
        }

        /* FilePond focus states */
        .filepond--root:focus-within {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        /* Image editing styles */
        .filepond--image-edit-editor {
            background: rgba(0, 0, 0, 0.8);
            border-radius: 8px;
            padding: 20px;
        }

        .filepond--image-edit-editor-header {
            color: white;
            font-weight: 600;
            margin-bottom: 15px;
            text-align: center;
        }

        .filepond--image-edit-editor-controls {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .filepond--image-edit-editor-control {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .filepond--image-edit-editor-control-label {
            color: white;
            font-size: 14px;
            font-weight: 500;
        }

        .filepond--image-edit-editor-control-input {
            background: rgba(255, 255, 255, 0.1);
            border: 1px solid rgba(255, 255, 255, 0.2);
            border-radius: 4px;
            color: white;
            padding: 8px 12px;
            width: 100%;
        }

        .filepond--image-edit-editor-control-input:focus {
            outline: none;
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.25);
        }

        .filepond--image-edit-editor-actions {
            display: flex;
            gap: 10px;
            justify-content: center;
            margin-top: 20px;
        }

        .filepond--image-edit-editor-button {
            background: #007bff;
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            font-size: 14px;
            font-weight: 500;
            padding: 8px 16px;
            transition: background-color 0.2s ease;
        }

        .filepond--image-edit-editor-button:hover {
            background: #0056b3;
        }

        .filepond--image-edit-editor-button.secondary {
            background: #6c757d;
        }

        .filepond--image-edit-editor-button.secondary:hover {
            background: #545b62;
        }

        /* Image edit button in FilePond item */
        .filepond--image-edit-button {
            background: rgba(0, 123, 255, 0.9);
            border: none;
            border-radius: 4px;
            color: white;
            cursor: pointer;
            font-size: 12px;
            font-weight: 500;
            padding: 4px 8px;
            position: absolute;
            right: 8px;
            top: 8px;
            transition: background-color 0.2s ease;
            z-index: 10;
        }

        .filepond--image-edit-button:hover {
            background: rgba(0, 86, 179, 0.9);
        }

        /* Ensure the edit button is visible */
        .filepond--item .filepond--image-edit-button {
            display: block !important;
            opacity: 1 !important;
            visibility: visible !important;
        }

        /* Style the edit button icon */
        .filepond--image-edit-button::before {
            content: "✏️";
            font-size: 14px;
            line-height: 1;
        }

        /* Alternative icon using FontAwesome if available */
        .filepond--image-edit-button .fa-edit,
        .filepond--image-edit-button .fa-pencil {
            font-size: 12px;
            margin-right: 2px;
        }

        /* Existing image preview styles */
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/filepond/dist/filepond.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-preview/dist/filepond-plugin-image-preview.js"></script>
    <script src="https://unpkg.com/filepond-plugin-file-validate-type/dist/filepond-plugin-file-validate-type.js"></script>
    <script src="https://unpkg.com/filepond-plugin-image-edit/dist/filepond-plugin-image-edit.js"></script>
    <script>
        const projectModal = document.getElementById('projectModal');
        const modalTitle = document.getElementById('projectModalLabel');
        const projectNameInput = document.getElementById('projectNameInput');
        const imageUploadBox = document.getElementById('imageUploadBox');
        const imageInput = document.getElementById('imageInput');
        const imageName = document.getElementById('imageName');
        let mode = '';
        let projectId = '';

        const favoriteLayouts =  @json($favorites);
        console.log(favoriteLayouts);

        // Add references to dashboard and create project sections
        const dashboardSection = document.getElementById('dashboard-section');
        const createProjectSection = document.getElementById('createProjectSection');

        // New form elements
        const inlineProjectNameInput = document.getElementById('inlineProjectNameInput');
        const inlineCompanyInput = document.getElementById('inlineCompanyInput');
        const inlineTourSelect = document.getElementById('inlineTourSelect');
        const inlineCollections = document.getElementById('inlineCollections');
        const inlineContributors = document.getElementById('inlineContributors');
        const inlineUnits = document.getElementById('inlineUnits');
        const inlineImageUploadBox = document.getElementById('inlineImageUploadBox');
        const inlineImageInput = document.getElementById('inlineImageInput');
        const inlineImageName = document.getElementById('inlineImageName');
        const inlineSaveButton = document.getElementById('inlineSaveButton');

        // Initialize FilePond
        FilePond.registerPlugin(FilePondPluginImagePreview, FilePondPluginFileValidateType, FilePondPluginImageEdit);
        
        // Debug: Check if plugins are loaded
        console.log('FilePond plugins loaded:', {
            imagePreview: typeof FilePondPluginImagePreview !== 'undefined',
            fileValidateType: typeof FilePondPluginFileValidateType !== 'undefined',
            imageEdit: typeof FilePondPluginImageEdit !== 'undefined'
        });

        // Test the image edit plugin directly
        if (typeof FilePondPluginImageEdit !== 'undefined') {
            console.log('Image Edit Plugin is available');
            console.log('Plugin methods:', Object.getOwnPropertyNames(FilePondPluginImageEdit));
        } else {
            console.error('Image Edit Plugin is NOT loaded!');
        }

        // Initialize inline image upload
        const inlinePond = FilePond.create(document.getElementById('inlineImageInput'), {
            acceptedFileTypes: ['image/jpeg', 'image/png'],
            maxFileSize: '5MB',
            imagePreviewHeight: 170,
            imageCropAspectRatio: '1:1',
            imageResizeTargetWidth: 200,
            imageResizeTargetHeight: 200,
            styleItemPanelAspectRatio: 0.5,
            styleLoadIndicatorPosition: 'center bottom',
            styleProgressIndicatorPosition: 'right bottom',
            styleButtonRemoveItemPosition: 'left bottom',
            styleButtonProcessItemPosition: 'right bottom',
            labelIdle: 'Drag & Drop your photo or <span class="filepond--label-action">Browse</span>',
            labelFileProcessing: 'Uploading',
            labelFileProcessingComplete: 'Upload complete',
            labelTapToCancel: 'tap to cancel',
            labelTapToRetry: 'tap to retry',
            labelTapToUndo: 'tap to undo',
            labelButtonRemoveItem: 'Remove',
            labelButtonAbortItemLoad: 'Abort',
            labelButtonAbortItemProcessing: 'Cancel',
            labelButtonProcessItem: 'Upload',
            labelMaxFileSize: 'File is too large',
            labelMaxFileSizeExceeded: 'File is too large',
            labelMaxFileSizeUnknown: 'File is too large',
            labelFileTypeNotAllowed: 'File of invalid type',
            fileValidateTypeLabelExpectedTypes: 'Expects {allTypes}',
            fileValidateTypeLabelExpectedTypesMap: {
                'image/jpeg': 'JPEG',
                'image/png': 'PNG'
            },
            allowMultiple: false,
            allowReplace: true,
            instantUpload: false,
            server: null,
            // Enable image editing with proper configuration
            allowImageEdit: true,
            imageEditInstantEdit: false,
            imageEditEditor: [
                {
                    name: 'crop',
                    label: 'Crop',
                    icon: 'crop',
                    options: {
                        aspectRatio: 1,
                        minCropBoxWidth: 100,
                        minCropBoxHeight: 100
                    }
                },
                {
                    name: 'rotate',
                    label: 'Rotate',
                    icon: 'rotate-right',
                    options: {
                        rotation: 0
                    }
                },
                {
                    name: 'filter',
                    label: 'Filters',
                    icon: 'magic',
                    options: {
                        brightness: 0,
                        contrast: 0,
                        saturation: 0,
                        blur: 0
                    }
                }
            ]
        });

        // Initialize modal image upload
        const modalPond = FilePond.create(document.getElementById('imageInput'), {
            acceptedFileTypes: ['image/jpeg', 'image/png'],
            maxFileSize: '5MB',
            imagePreviewHeight: 170,
            imageCropAspectRatio: '1:1',
            imageResizeTargetWidth: 200,
            imageResizeTargetHeight: 200,
            styleItemPanelAspectRatio: 0.5,
            styleLoadIndicatorPosition: 'center bottom',
            styleProgressIndicatorPosition: 'right bottom',
            styleButtonRemoveItemPosition: 'left bottom',
            styleButtonProcessItemPosition: 'right bottom',
            labelIdle: 'Drag & Drop your photo or <span class="filepond--label-action">Browse</span>',
            labelFileProcessing: 'Uploading',
            labelFileProcessingComplete: 'Upload complete',
            labelTapToCancel: 'tap to cancel',
            labelTapToRetry: 'tap to retry',
            labelTapToUndo: 'tap to undo',
            labelButtonRemoveItem: 'Remove',
            labelButtonAbortItemLoad: 'Abort',
            labelButtonAbortItemProcessing: 'Cancel',
            labelButtonProcessItem: 'Upload',
            labelMaxFileSize: 'File is too large',
            labelMaxFileSizeExceeded: 'File is too large',
            labelMaxFileSizeUnknown: 'File is too large',
            labelFileTypeNotAllowed: 'File of invalid type',
            fileValidateTypeLabelExpectedTypes: 'Expects {allTypes}',
            fileValidateTypeLabelExpectedTypesMap: {
                'image/jpeg': 'JPEG',
                'image/png': 'PNG'
            },
            allowMultiple: false,
            allowReplace: true,
            instantUpload: false,
            server: null,
            // Enable image editing with proper configuration
            allowImageEdit: true,
            imageEditInstantEdit: false,
            imageEditEditor: [
                {
                    name: 'crop',
                    label: 'Crop',
                    icon: 'crop',
                    options: {
                        aspectRatio: 1,
                        minCropBoxWidth: 100,
                        minCropBoxHeight: 100
                    }
                },
                {
                    name: 'rotate',
                    label: 'Rotate',
                    icon: 'rotate-right',
                    options: {
                        rotation: 0
                    }
                },
                {
                    name: 'filter',
                    label: 'Filters',
                    icon: 'magic',
                    options: {
                        brightness: 0,
                        contrast: 0,
                        saturation: 0,
                        blur: 0
                    }
                }
            ]
        });

        // Add FilePond event listeners for better UX
        inlinePond.on('addfile', (error, file) => {
            if (error) {
                console.error('Error adding file:', error);
                return;
            }
            console.log('File added to inline pond:', file);
            console.log('File type:', file.fileType);
            console.log('File is image:', file.fileType.includes('image'));
            
            // Check if edit button is present
            setTimeout(() => {
                const editButton = document.querySelector('.filepond--image-edit-button');
                console.log('Edit button found:', editButton);
                if (editButton) {
                    console.log('Edit button is visible:', editButton.style.display);
                    console.log('Edit button opacity:', editButton.style.opacity);
                }
            }, 100);
            
            // Update image name display
            if (inlineImageName) {
                inlineImageName.textContent = file.filename;
            }
        });

        inlinePond.on('removefile', () => {
            // Clear image name display when file is removed
            if (inlineImageName) {
                inlineImageName.textContent = '';
            }
        });

        modalPond.on('addfile', (error, file) => {
            if (error) {
                console.error('Error adding file:', error);
                return;
            }
            // Update image name display
            if (imageName) {
                imageName.textContent = file.filename;
            }
        });

        modalPond.on('removefile', () => {
            // Clear image name display when file is removed
            if (imageName) {
                imageName.textContent = '';
            }
        });

        // Image editing event listeners for inline pond
        inlinePond.on('imageedit:edit', (file) => {
            console.log('Image editing started:', file.filename);
            // You can add loading indicators or other UI feedback here
        });

        inlinePond.on('imageedit:complete', (file) => {
            console.log('Image editing completed:', file.filename);
            // Update the image name to indicate it's been edited
            if (inlineImageName) {
                inlineImageName.textContent = file.filename + ' (edited)';
            }
        });

        // Image editing event listeners for modal pond
        modalPond.on('imageedit:edit', (file) => {
            console.log('Modal image editing started:', file.filename);
        });

        modalPond.on('imageedit:complete', (file) => {
            console.log('Modal image editing completed:', file.filename);
            // Update the image name to indicate it's been edited
            if (imageName) {
                imageName.textContent = file.filename + ' (edited)';
            }
        });

        async function openCreateProject(companyId) {
            try {
                // Fetch data from the create endpoint
                const response = await fetch(`/tour360/create/${companyId}`);
                const data = await response.json();

                // Store company ID for later use
                document.getElementById('createProjectSection').dataset.companyId = companyId;

                // Populate the company select dropdown
                inlineCompanyInput.value = data.company.name;

                // Populate the tour select dropdown
                inlineTourSelect.innerHTML = '';
                data.tours.forEach(tour => {
                    inlineTourSelect.innerHTML += `<option value="${tour.id}">${tour.name}</option>`;
                });

                // Populate the contributors select dropdown
                inlineContributors.innerHTML = '';
                data.users.forEach(user => {
                    inlineContributors.innerHTML += `<option value="${user.id}">${user.first_name} ${user.last_name}</option>`;
                });

                // Populate the collections select dropdown
                inlineCollections.innerHTML = '';
                data.artworkCollections.forEach(collection => {
                    inlineCollections.innerHTML += `<option value="${collection.id}">${collection.name}</option>`;
                });

                // Show the create project section
                dashboardSection.style.display = 'none';
                createProjectSection.style.display = 'block';
            } catch (error) {
                console.error('Error fetching project data:', error);
                alert('Failed to load project creation form. Please try again.');
            }
        }

        function closeCreateProject() {
            dashboardSection.style.display = 'block';
            createProjectSection.style.display = 'none';
            
            // Reset FilePond
            inlinePond.removeFiles();
            if (inlineImageName) {
                inlineImageName.textContent = '';
            }
        }

        function handleCreateProject() {
            // Create FormData object to handle file upload
            const formData = new FormData();

            // Get all form values
            const name = document.getElementById('inlineProjectNameInput').value;
            const tours = $('#inlineTourSelect').val(); // Using jQuery for Select2
            const collections = $('#inlineCollections').val();
            const contributors = $('#inlineContributors').val();
            const unit = document.getElementById('inlineUnits').value;
            const thumbnailFile = inlinePond.getFile() ? inlinePond.getFile().file : null;
            const companyId = document.getElementById('createProjectSection').dataset.companyId;

            // Validate required fields
            if (!name) {
                alert('Please enter a project name');
                return;
            }

            // Note: If no thumbnail is selected, a random default image will be assigned

            // Append all data to FormData
            formData.append('name', name);
            formData.append('tour_ids', JSON.stringify(tours));
            formData.append('artwork_collection_ids', JSON.stringify(collections));
            formData.append('user_ids', JSON.stringify(contributors));
            formData.append('unit', unit);
            if (thumbnailFile) {
                formData.append('thumbnail', thumbnailFile);
            }
            formData.append('company_id', companyId);

            // Add CSRF token
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            // Send request to server
            fetch('/tour360/store', {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Project created successfully!');
                    window.location.reload(); // Refresh page to show new project
                } else {
                    alert(data.message || 'Failed to create project');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while creating the project');
            });
        }

        function handleDeleteProject(id) {
            if (confirm('Are you sure you want to delete this project?')) {
                fetch(`/tour360/destroy/${id}`, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        // Remove the project card from the DOM
                        const projectCard = document.querySelector(`[data-project-id="${id}"]`).closest('.layout-item');
                        projectCard.remove();
                        alert('Project deleted successfully');
                    } else {
                        alert(data.message || 'Failed to delete project');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('An error occurred while deleting the project');
                });
            }
        }

        function handleEditProject(id) {
            // First fetch the project data using the named route
            fetch(`/tour360/edit/${id}`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok');
                }
                return response.json();
            })
            .then(data => {
                // Show the create project section (we'll reuse it for editing)
                dashboardSection.style.display = 'none';
                createProjectSection.style.display = 'block';

                // Update section title for editing
                document.querySelector('.create-project-section .modal-title').textContent = 'Edit project';

                // Populate form with existing data from the server response
                document.getElementById('inlineProjectNameInput').value = data.project.name;
                document.getElementById('inlineUnits').value = data.project.unit || 'metric';

                // Clear and populate dropdowns
                console.log(data)
                if (data.tours) {
                    populateSelect('inlineTourSelect', data.tours);
                    // Set selected tours
                    const selectedTours = data.assignedTours.map(tour => tour.id);
                    $('#inlineTourSelect').val(selectedTours).trigger('change');
                }

                if (data.artworkCollections) {
                    populateSelect('inlineCollections', data.artworkCollections);
                    // Set selected collections
                    const selectedCollections = data.assignedCollections.map(collection => collection.id);
                    $('#inlineCollections').val(selectedCollections).trigger('change');
                }

                if (data.users) {
                    populateSelect('inlineContributors', data.users);
                    // Set selected contributors
                    const selectedContributors = data.assignedUsers.map(contributor => contributor.id);
                    $('#inlineContributors').val(selectedContributors).trigger('change');
                }

                // Populate the image field with existing image
                if (data.project.background_url) {
                    // For FilePond, we need to add the existing image as a file
                    // Since we can't directly set files, we'll show the image name
                    if (inlineImageName) {
                        inlineImageName.textContent = 'Current image: ' + data.project.background_url.split('/').pop();
                    }
                    // Note: FilePond doesn't support setting existing files directly
                    // The user will need to re-upload if they want to change the image
                }

                // Update the save button to handle edit
                const saveButton = document.getElementById('inlineSaveButton');
                saveButton.textContent = 'Update';
                saveButton.onclick = () => handleUpdateProject(id);
            })
            .catch(error => {
                console.error('Error:', error);
                // alert('An error occurred while loading project data. Please try again.');
            });
        }

        function handleUpdateProject(id) {
            // Create FormData object to handle file upload
            const formData = new FormData();

            // Get all form values
            const name = document.getElementById('inlineProjectNameInput').value;
            const tours = $('#inlineTourSelect').val();
            const collections = $('#inlineCollections').val();
            const contributors = $('#inlineContributors').val();
            const unit = document.getElementById('inlineUnits').value;
            const thumbnailFile = inlinePond.getFile() ? inlinePond.getFile().file : null;

            // Validate required fields
            if (!name) {
                alert('Please enter a project name');
                return;
            }

            // Append all data to FormData
            formData.append('name', name);
            formData.append('tour_ids', JSON.stringify(tours));
            formData.append('artwork_collection_ids', JSON.stringify(collections));
            formData.append('user_ids', JSON.stringify(contributors));
            formData.append('unit', unit);
            console.log(unit)
            if (thumbnailFile) {
                formData.append('thumbnail', thumbnailFile);
            }

            // Add CSRF token
            formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);

            // Send request to server
            fetch(`/tour360/update/${id}`, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert('Project updated successfully!');
                    window.location.reload(); // Refresh page to show updated project
                } else {
                    alert(data.message || 'Failed to update project');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while updating the project');
            });
        }

        function populateSelect(selectId, options) {
            const select = document.getElementById(selectId);
            select.innerHTML = '';
            options.forEach(option => {
                const opt = document.createElement('option');
                opt.value = option.id;
                if (selectId === 'inlineContributors') {
                    opt.textContent = option.first_name + ' ' + option.last_name;
                } else {
                    opt.textContent = option.name;
                }
                select.appendChild(opt);
            });
        }

        function handleEnterProject(projectId, tourCount) {
            if (tourCount === 0) {
                // Show the modal instead of alert
                const noToursModal = new bootstrap.Modal(document.getElementById('noToursModal'));
                noToursModal.show();
                
                // Set up the Edit Project button to edit the current project
                document.getElementById('editProjectBtn').onclick = function() {
                    noToursModal.hide();
                    handleEditProject(projectId);
                };
                return;
            }
            
            // If tours exist, proceed with the original Livewire dispatch
            Livewire.dispatch('slide-over.open', {
                component: 'updated-tour-switcher', 
                arguments: {'project': projectId}
            });
        }

        let currentFavoriteId = null;

        function removeFavorite(favoriteId) {
            // Store the favorite ID for later use
            currentFavoriteId = favoriteId;
            
            // Get the favorite card to extract information
            const favoriteCard = document.querySelector(`[data-favorite-id="${favoriteId}"]`);
            if (favoriteCard) {
                const favoriteName = favoriteCard.querySelector('h4').textContent.trim();
                const tourName = favoriteCard.querySelector('span').textContent.trim();
                
                // Populate the modal with favorite information
                document.getElementById('favoriteItemInfo').innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-star text-primary me-3"></i>
                        <div class="text-start">
                            <strong>${favoriteName}</strong><br>
                            <small class="text-muted">${tourName}</small>
                        </div>
                    </div>
                `;
            }
            
            // Show the modal
            const removeFavoriteModal = new bootstrap.Modal(document.getElementById('removeFavoriteModal'));
            removeFavoriteModal.show();
        }

        // Handle confirm remove favorite button click
        document.getElementById('confirmRemoveFavoriteBtn').addEventListener('click', function() {
            if (!currentFavoriteId) return;
            
            const favoriteId = currentFavoriteId;
            
            fetch(`/tour360/toggle-favorite/${favoriteId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Remove the favorite card from the DOM
                    const favoriteCard = document.querySelector(`[data-favorite-id="${favoriteId}"]`);
                    if (favoriteCard) {
                        favoriteCard.remove();
                    }
                    
                    // Check if there are any favorites left
                    const remainingFavorites = document.querySelectorAll('.favourite-card');
                    if (remainingFavorites.length === 0) {
                        const favoritesContainer = document.getElementById('favoritesContainer');
                        favoritesContainer.innerHTML = `
                            <div class="row">
                                <div class="col-12">
                                    <p class="text-center">No favorites found.</p>
                                </div>
                            </div>
                        `;
                    }
                    
                    // Hide the modal
                    const removeFavoriteModal = bootstrap.Modal.getInstance(document.getElementById('removeFavoriteModal'));
                    removeFavoriteModal.hide();
                    
                    // Reset the current favorite ID
                    currentFavoriteId = null;
                } else {
                    alert(data.message || 'Failed to remove favorite');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while removing the favorite');
            });
        });


        // Handle inline save button
        inlineSaveButton.addEventListener('click', async function() {
            const formData = new FormData();
            formData.append('name', inlineProjectNameInput.value);
            formData.append('tour', inlineTourSelect.value);
            formData.append('collections', inlineCollections.value);
            formData.append('contributors', inlineContributors.value);
            formData.append('unit', inlineUnits.value);
            const imageFile = inlinePond.getFile() ? inlinePond.getFile().file : null;
            if (imageFile) {
                formData.append('image', imageFile);
            }

            try {
                const response = await fetch('/tour360/store', {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();

                if (data.success) {
                    // Reset form
                    inlineProjectNameInput.value = '';
                    inlineTourSelect.selectedIndex = 0;
                    inlineCollections.value = '';
                    inlineContributors.value = '';
                    inlineUnits.selectedIndex = 0;
                    inlinePond.removeFiles();
                    inlineImageName.textContent = '';

                    // Refresh the page to show updated project
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while saving the project');
            }
        });

        // Add event listener for favorites updates
        document.addEventListener('livewire:initialized', () => {
            Livewire.on('favoritesUpdated', (event) => {
                const favoritesContainer = document.getElementById('favoritesContainer');
                const favorites = event.favorites;
                
                console.log(favorites);
                if (favorites.length === 0) {
                    favoritesContainer.innerHTML = `
                        <div class="row">
                            <div class="col-12">
                                <p class="text-center">No favorites found.</p>
                            </div>
                        </div>
                    `;
                    return;
                }

                const favoritesHtml = favorites.map(favorite => `
                    <div class="col-md-3 favourite-card" data-favorite-id="${favorite.id}">
                        <div class="bg-light rounded p-3">
                            <div class="d-flex justify-content-between align-items-start mb-2">
                                <h4 class="mb-0">
                                    <i class="fas fa-star text-primary favorite-star" 
                                       onclick="removeFavorite(${favorite.id})" 
                                       title="Remove from favorites"
                                       style="cursor: pointer;"></i> 
                                    ${favorite.name}
                                </h4>
                            </div>
                            <span>${favorite.tour ? favorite.tour.name : 'No Tour Assigned'}</span>
                            <div class="text-end mb-0 mt-3 d-flex justify-content-end">
                                <a href="/tours/${favorite.tour_id}?layout_id=${favorite.id}" class="btn-enter">
                                    Enter
                                </a>
                            </div>
                        </div>
                    </div>
                `).join('');

                favoritesContainer.innerHTML = `
                    <div class="row">
                        ${favoritesHtml}
                    </div>
                `;
            });
        });

        $(document).ready(function() {
            $('#inlineTourSelect').select2();
            $('#inlineCollections').select2();
            $('#inlineContributors').select2();
        });


        @if(auth()->user() && auth()->user()->isSuperAdmin())
        document.getElementById('toggleFavouritesBtn').addEventListener('click', function() {
            const section = document.getElementById('favouritesSection');
            const icon = document.getElementById('favouritesEyeIcon');
            if (section.style.display === 'none') {
                section.style.display = '';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            } else {
                section.style.display = 'none';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            }
        });
        @endif

    </script>
@endsection
