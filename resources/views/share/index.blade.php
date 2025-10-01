@extends('layouts.redesign')

@section('content')
<div class="share-page-container">
    <div class="share-page-content">
        <div class="share-page-header">
            <h5>Share layouts</h5>
        </div>
        <div class="row">
            <!-- Left Panel: Share Layout Form -->
            <div class="col-md-4">
                <div class="share-form-panel">
                    <div class="card p-3">
                        <form id="shareLayoutForm" method="POST" action="{{ route('share.store') }}">
                            @csrf
                            <!-- Select Layout Button -->
                            <button type="button" class="btn btn-outline-secondary mb-3 w-100" id="selectLayoutBtn">
                                Select layout
                            </button>
                            <!-- Hidden field for layout_id -->
                            <input type="hidden" name="layout_id" id="selectedLayoutId">
                            <!-- Hidden field for thumbnail_url -->
                            <input type="hidden" name="thumbnail_url" id="selectedThumbnailUrl">
                            <!-- Hidden file input for image upload -->
                            <input type="file" id="thumbnailFileInput" accept="image/*" style="display: none;">
                            <!-- Thumbnail preview area -->
                            <div class="mb-3" id="thumbnailContainer" style="height: 140px; background: #f5f5f5; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative;">
                                <div id="thumbnailContent" style="text-align: center;">
                                    <span id="thumbnailPlaceholder">Click to add image</span>
                                    <img id="selectedThumbnail" src="" alt="Selected Thumbnail" style="display:none; max-height: 100px; max-width: 100px; object-fit: cover; border-radius: 4px;"/>
                                </div>
                                <!-- Overlay for hover effect -->
                                <div id="thumbnailOverlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.1); display: none; align-items: center; justify-content: center;">
                                    <span style="color: #666; font-size: 14px;">Click to change image</span>
                                </div>
                                <!-- Loading overlay with circle progress bar -->
                                <div id="thumbnailLoadingOverlay" class="loading-overlay hidden">
                                    <div class="circle-progress">
                                        <svg>
                                            <circle class="background" cx="30" cy="30" r="25"></circle>
                                            <circle class="progress" cx="30" cy="30" r="25" id="thumbnailProgressCircle"></circle>
                                        </svg>
                                        <div class="progress-text" id="thumbnailProgressText">0%</div>
                                    </div>
                                </div>
                            </div>
                            <!-- Title input (readonly, filled by selection) -->
                            <input type="text" class="form-control mb-2" placeholder="Title" id="selectedLayoutTitle" name="title" >
                            <textarea class="form-control mb-2" placeholder="Description" name="description"></textarea>
                            <button type="submit" class="btn btn-primary w-100" id="saveBtn">Save</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        <div class="row" style="margin-top: 50px;">

            <!-- Right Panel: Shared Layouts -->
            <div class="shared-layouts-panel">
                <div class="shared-layouts-grid">
                    @forelse($sharedLayouts as $sharedLayout)
                    <div class="card h-100 shadow-sm {{ !$sharedLayout->active ? 'opacity-50' : '' }}">
                        @if($sharedLayout->thumbnail_url)
                            <img src="{{ asset($sharedLayout->thumbnail_url) }}" class="card-img-top" alt="{{ $sharedLayout->title }}">
                        @elseif($sharedLayout->layout && $sharedLayout->layout->assignedTour() && $sharedLayout->layout->assignedTour()->getFirstMediaUrl('thumbnail'))
                            <img src="{{ $sharedLayout->layout->assignedTour()->getFirstMediaUrl('thumbnail') }}" class="card-img-top" alt="{{ $sharedLayout->title }}">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <span class="text-muted">No thumbnail</span>
                            </div>
                        @endif
                        <div class="card-body">
                            <!-- First row: Title left, icons right -->
                            <div class="d-flex justify-content-between align-items-center">
                                <h5 class="card-title mb-0">{{ $sharedLayout->title }}</h5>
                                <div class="card-actions ms-2">
                                    <!-- Preview button: always active -->
                                    <a href="{{ route('tours.show', [$sharedLayout->layout->assignedTour()->id, 'layout_id' => $sharedLayout->layout->id, 'shared' => true]) }}" class="text-muted" title="Preview" target="_blank">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    @if($sharedLayout->layout)
                                        <!-- Share button: always active -->
                                        <a href="#" class="text-muted share-shared-layout"
                                           data-layout-id="{{ $sharedLayout->layout->id }}"
                                           data-shared-layout-id="{{ $sharedLayout->id }}"
                                           title="Share">
                                            <i class="bi bi-share"></i>
                                        </a>
                                    @else
                                        <span class="text-muted" title="Layout not available"><i class="bi bi-share"></i></span>
                                    @endif
                                    <!-- Edit button: always active -->
                                    <a href="#"
                                        class="text-muted edit-shared-layout"
                                        data-id="{{ $sharedLayout->id }}"
                                        data-title="{{ $sharedLayout->title }}"
                                        data-description="{{ $sharedLayout->description }}"
                                        data-thumbnail="{{ $sharedLayout->thumbnail_url ? asset($sharedLayout->thumbnail_url) : $sharedLayout->layout->assignedTour()->getFirstMediaUrl('thumbnail') }}"
                                        data-layout-name="{{ $sharedLayout->layout->name ?? 'N/A' }}"
                                        data-tour-name="{{ $sharedLayout->layout->assignedTour()->name ?? 'N/A' }}"
                                        data-project-name="{{ $sharedLayout->layout->project?->name ?? 'N/A' }}"
                                        title="Edit">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <!-- Toggle button: Enable/Disable -->
                                    @if($sharedLayout->active)
                                        <a href="#" class="text-muted toggle-shared-layout" data-id="{{ $sharedLayout->id }}" title="Disable">
                                            <i class="bi bi-x-circle"></i>
                                        </a>
                                    @else
                                        <a href="#" class="text-muted toggle-shared-layout" data-id="{{ $sharedLayout->id }}" title="Enable">
                                            <i class="bi bi-check-circle"></i>
                                        </a>
                                    @endif
                                    <!-- Delete button: always visible -->
                                    <a href="#" class="text-muted delete-shared-layout" data-id="{{ $sharedLayout->id }}" title="Delete">
                                        <i class="bi bi-trash"></i>
                                    </a>
                                </div>
                            </div>

                            <!-- Second row: Company name and Disabled badge right-aligned -->
                            <div class="d-flex align-items-center mb-2">
                                @if($sharedLayout->layout && $sharedLayout->layout->project)
                                    <div class="text-muted small">{{ $sharedLayout->layout->project->company->name }}</div>
                                @endif
                                @if(!$sharedLayout->active)
                                    <span class="badge bg-danger ms-auto">Disabled</span>
                                @endif
                            </div>

                            <!-- Description -->
                            <p class="card-text">{{ $sharedLayout->description ?: 'No description available.' }}</p>

                            <!-- Divider line -->
                            <hr class="my-2" />

                            <!-- Layout/Tour/Project info -->
                            <div class="mt-2 small text-muted">
                                <div>Layout: {{ $sharedLayout->layout->name ?? 'N/A' }}</div>
                                <div>Tour: {{ $sharedLayout->layout->assignedTour()->name ?? 'N/A' }}</div>
                                <div>Project: {{ $sharedLayout->layout->project?->name ?? 'N/A' }}</div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <p>No shared layouts yet. Create your first one!</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal for layout selection -->
<div class="modal fade" id="selectLayoutModal" tabindex="-1" aria-labelledby="selectLayoutModalLabel" aria-hidden="true">
  <div class="modal-dialog">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="selectLayoutModalLabel">Select Layout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Project Selector -->
        <div class="mb-3">
          <label for="projectSelect" class="form-label">Select Project</label>
          <select class="form-select" id="projectSelect">
            <option value="">-- Select a project --</option>
            @foreach($projects as $project)
              <option value="{{ $project->id }}">{{ $project->name }}</option>
            @endforeach
          </select>
        </div>

        <!-- Layout Selector (initially disabled) -->
        <div class="mb-3">
          <label for="layoutSelect" class="form-label">Select Layout</label>
          <select class="form-select" id="layoutSelect" disabled>
            <option value="" data-thumbnail="" data-id="">-- Select a project first --</option>
          </select>
        </div>

        <!-- Layout Information Display (initially hidden) -->
        <div id="layoutInfoDisplay" class="mb-3" style="display: none;">
          <div class="card">
            <div class="card-body">
              <h6 class="card-title">Layout Information</h6>
              <div class="small text-muted">
                <div id="layoutInfoLayout">Layout: <span id="layoutInfoLayoutName">-</span></div>
                <div id="layoutInfoTour">Tour: <span id="layoutInfoTourName">-</span></div>
                <div id="layoutInfoProject">Project: <span id="layoutInfoProjectName">-</span></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="selectLayoutOkBtn" disabled style="background-color: #099F9A !important; border-color: #099F9A !important;">OK</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for editing shared layout -->
<div class="modal fade" id="editSharedLayoutModal" tabindex="-1" aria-labelledby="editSharedLayoutModalLabel" aria-hidden="true">
  <div class="modal-dialog" style="margin-top: 0px;">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="editSharedLayoutModalLabel">Edit Shared Layout</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="editSharedLayoutForm">
          <div class="mb-3">
            <label for="editTitle" class="form-label">Title</label>
            <input type="text" class="form-control" id="editTitle" name="title" required>
          </div>
          <div class="mb-3">
            <label for="editDescription" class="form-label">Description</label>
            <textarea class="form-control" id="editDescription" name="description" rows="3"></textarea>
          </div>
          <!-- Thumbnail upload area for edit -->
          <div class="mb-3">
            <label class="form-label">Thumbnail</label>
            <div class="mb-3" id="editThumbnailContainer" style="height: 140px; background: #f5f5f5; border: 2px dashed #ccc; display: flex; align-items: center; justify-content: center; cursor: pointer; position: relative;">
                <div id="editThumbnailContent" style="text-align: center;">
                    <span id="editThumbnailPlaceholder">Click to change image</span>
                    <img id="editSelectedThumbnail" src="" alt="Selected Thumbnail" style="display:none; max-height: 100px; max-width: 100px; object-fit: cover; border-radius: 4px;"/>
                </div>
                <!-- Overlay for hover effect -->
                <div id="editThumbnailOverlay" style="position: absolute; top: 0; left: 0; right: 0; bottom: 0; background: rgba(0,0,0,0.1); display: none; align-items: center; justify-content: center;">
                    <span style="color: #666; font-size: 14px;">Click to change image</span>
                </div>
                <!-- Loading overlay with circle progress bar -->
                <div id="editThumbnailLoadingOverlay" class="loading-overlay hidden">
                    <div class="circle-progress">
                        <svg>
                            <circle class="background" cx="30" cy="30" r="25"></circle>
                            <circle class="progress" cx="30" cy="30" r="25" id="editThumbnailProgressCircle"></circle>
                        </svg>
                        <div class="progress-text" id="editThumbnailProgressText">0%</div>
                    </div>
                </div>
            </div>
            <!-- Hidden file input for edit image upload -->
            <input type="file" id="editThumbnailFileInput" accept="image/*" style="display: none;">
          </div>
          <!-- Hidden field for thumbnail_url -->
          <input type="hidden" name="thumbnail_url" id="editThumbnailUrl">
          
          <!-- Layout Information Display for Edit -->
          <div class="mb-3">
            <div class="card">
              <div class="card-body">
                <h6 class="card-title">Layout Information</h6>
                <div class="small text-muted">
                  <div>Layout: <span id="editLayoutInfoLayoutName">-</span></div>
                  <div>Tour: <span id="editLayoutInfoTourName">-</span></div>
                  <div>Project: <span id="editLayoutInfoProjectName">-</span></div>
                </div>
              </div>
            </div>
          </div>
        </form>
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-primary" id="saveEditBtn" onclick="handleEditSharedLayout()" style="background-color: #099F9A !important; border-color: #099F9A !important;">Save Changes</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for enable/disable confirmation -->
<div class="modal fade" id="confirmToggleModal" tabindex="-1" aria-labelledby="confirmToggleModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmToggleModalLabel">Confirm Action</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="confirmToggleModalBody">
        <!-- Message will be set by JS -->
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-primary" id="confirmToggleOkBtn" style="background-color: #099F9A !important; border-color: #099F9A !important;">OK</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for delete confirmation -->
<div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="confirmDeleteModalLabel">Confirm Delete</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body" id="confirmDeleteModalBody">
        Are you sure you want to delete this shared layout? This action cannot be undone.
      </div>
      <div class="modal-footer">
      <button type="button" class="btn btn-primary" id="confirmDeleteOkBtn" style="background-color: #099F9A !important; border-color: #099F9A !important;">Yes</button>
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
      </div>
    </div>
  </div>
</div>
@endsection

@section('styles')
    <link href="/css/page/share.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css">
    <style>
        .modal-dialog {
            display: flex;
            align-items: center;
            min-height: calc(100% - 1rem);
            margin-top: -50px; /* Move modal up by 50px */
        }
        @media (min-width: 576px) {
            .modal-dialog {
                min-height: calc(100% - 3.5rem);
                margin-top: -50px; /* Move modal up by 50px on larger screens */
            }
        }

        /* Additional modal content height constraints */
        .modal-content {
            max-height: 80vh; /* Limit modal content height */
            overflow-y: auto; /* Add scroll if content is too tall */
        }

        .modal-body {
            max-height: 70vh; /* Limit modal body height */
            overflow-y: auto; /* Add scroll if body content is too tall */
        }

        /* Thumbnail container styling */
        #thumbnailContainer {
            transition: all 0.3s ease;
            border-radius: 8px;
        }

        #thumbnailContainer:hover {
            border-color: #007bff;
            background-color: #f8f9fa;
        }

        #thumbnailContainer:active {
            transform: scale(0.98);
        }

        #thumbnailPlaceholder {
            color: #6c757d;
            font-size: 14px;
            user-select: none;
        }

        #selectedThumbnail {
            border-radius: 6px;
            transition: opacity 0.3s ease;
        }

        #thumbnailOverlay {
            border-radius: 8px;
            transition: opacity 0.3s ease;
        }

        #thumbnailOverlay span {
            background: rgba(0,0,0,0.8);
            color: #ffffff;
            font-weight: 500;
            text-shadow: 0 1px 2px rgba(0,0,0,0.5);
        }

        /* Improved thumbnail image sizing */
        #selectedThumbnail {
            max-height: 120px !important;
            max-width: 120px !important;
            width: auto;
            height: auto;
            object-fit: contain;
            border-radius: 6px;
            transition: opacity 0.3s ease;
        }

        /* Card image styling for shared layouts */
        .card-img-top {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        /* Edit thumbnail styling */
        #editSelectedThumbnail {
            max-height: 120px !important;
            max-width: 120px !important;
            width: auto;
            height: auto;
            object-fit: contain;
            border-radius: 6px;
            transition: opacity 0.3s ease;
        }

        /* Circle Progress Bar Styles */
        .circle-progress {
            position: relative;
            width: 60px;
            height: 60px;
            margin: 0 auto;
        }

        .circle-progress svg {
            width: 100%;
            height: 100%;
            transform: rotate(-90deg);
        }

        .circle-progress circle {
            fill: none;
            stroke-width: 4;
        }

        .circle-progress .background {
            stroke: #e0e0e0;
        }

        .circle-progress .progress {
            stroke: #007bff;
            stroke-linecap: round;
            stroke-dasharray: 157; /* 2 * π * 25 (radius) */
            stroke-dashoffset: 157;
            transition: stroke-dashoffset 0.3s ease;
        }

        .circle-progress .progress-text {
            display: none;
        }

        .loading-overlay {
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 8px;
            z-index: 10;
        }

        .loading-overlay.hidden {
            display: none;
        }

        /* Custom highlight color for Save button */
        #saveBtn {
            background-color: #099F9A !important;
            border-color: #099F9A !important;
        }

        #saveBtn:hover {
            background-color: #088a85 !important;
            border-color: #088a85 !important;
        }

        #saveBtn:focus {
            background-color: #099F9A !important;
            border-color: #099F9A !important;
            box-shadow: 0 0 0 0.2rem rgba(9, 159, 154, 0.25) !important;
        }
    </style>
@endsection

@section('scripts')
<script>
// Pass layouts data to JavaScript
var layoutsData = @json($layouts->groupBy('project_id'));

// Pass prefill data to JavaScript
var prefillData = @json($prefillData);

// Circle Progress Bar Functions
function showProgressBar(overlayId, progressCircleId, progressTextId) {
    const overlay = document.getElementById(overlayId);
    const progressCircle = document.getElementById(progressCircleId);
    const progressText = document.getElementById(progressTextId);
    
    if (overlay && progressCircle && progressText) {
        overlay.classList.remove('hidden');
        animateProgress(progressCircle, progressText, 0, 100, 2000); // 2 seconds animation
    }
}

function hideProgressBar(overlayId) {
    const overlay = document.getElementById(overlayId);
    if (overlay) {
        overlay.classList.add('hidden');
    }
}

function animateProgress(circle, textElement, start, end, duration) {
    const startTime = performance.now();
    const circumference = 2 * Math.PI * 25; // radius = 25
    const range = end - start;
    
    function updateProgress(currentTime) {
        const elapsed = currentTime - startTime;
        const progress = Math.min(elapsed / duration, 1);
        const currentValue = start + (range * progress);
        
        // Update circle progress
        const offset = circumference - (currentValue / 100) * circumference;
        circle.style.strokeDashoffset = offset;
        
        if (progress < 1) {
            requestAnimationFrame(updateProgress);
        }
    }
    
    requestAnimationFrame(updateProgress);
}

// Simulate loading for prefill data
function simulateDataLoading() {
    if (prefillData && prefillData.layout_id) {
        showProgressBar('thumbnailLoadingOverlay', 'thumbnailProgressCircle', 'thumbnailProgressText');
        
        // Simulate loading time for prefill data
        setTimeout(() => {
            hideProgressBar('thumbnailLoadingOverlay');
        }, 2000);
    }
}

// Global functions that need to be accessible from onclick attributes
function showEditModal(element) {
    var link = element.closest('.edit-shared-layout');
    var sharedLayoutId = link.getAttribute('data-id');
    var title = link.getAttribute('data-title');
    var description = link.getAttribute('data-description');
    var thumbnailUrl = link.getAttribute('data-thumbnail'); // Get thumbnail_url
    var layoutName = link.getAttribute('data-layout-name');
    var tourName = link.getAttribute('data-tour-name');
    var projectName = link.getAttribute('data-project-name');

    // Populate the edit modal with current data
    document.getElementById('editTitle').value = title;
    document.getElementById('editDescription').value = description;

    // Populate layout information
    document.getElementById('editLayoutInfoLayoutName').textContent = layoutName;
    document.getElementById('editLayoutInfoTourName').textContent = tourName;
    document.getElementById('editLayoutInfoProjectName').textContent = projectName;

    // Set thumbnail preview in edit modal
    var editThumbnailContainer = document.getElementById('editThumbnailContainer');
    var editThumbnailPlaceholder = document.getElementById('editThumbnailPlaceholder');
    var editSelectedThumbnail = document.getElementById('editSelectedThumbnail');
    var editThumbnailUrl = document.getElementById('editThumbnailUrl');

    if (thumbnailUrl && thumbnailUrl.trim() !== '') {
        editSelectedThumbnail.src = thumbnailUrl;
        editSelectedThumbnail.style.display = 'block';
        editThumbnailPlaceholder.style.display = 'none';
        editThumbnailUrl.value = thumbnailUrl; // Set the hidden field
    } else {
        editSelectedThumbnail.src = '';
        editSelectedThumbnail.style.display = 'none';
        editThumbnailPlaceholder.style.display = 'block';
        editThumbnailUrl.value = ''; // Clear the hidden field
    }

    console.log('Shared layout ID:', sharedLayoutId);
    // Store the shared layout ID for the save operation
    document.getElementById('editSharedLayoutForm').setAttribute('data-shared-layout-id', sharedLayoutId);

    // Show the edit modal
    var editModal = new bootstrap.Modal(document.getElementById('editSharedLayoutModal'));
    editModal.show();
}

function handleEditSharedLayout() {
    var form = document.getElementById('editSharedLayoutForm');
    var sharedLayoutId = form.getAttribute('data-shared-layout-id');
    var title = document.getElementById('editTitle').value;
    var description = document.getElementById('editDescription').value;

    if (!title.trim()) {
        alert('Title is required.');
        return;
    }

    // Show progress bar
    showProgressBar('editThumbnailLoadingOverlay', 'editThumbnailProgressCircle', 'editThumbnailProgressText');

    // Disable save button to prevent double submission
    var saveBtn = document.getElementById('saveEditBtn');
    saveBtn.disabled = true;
    saveBtn.textContent = 'Saving...';

    // Prepare FormData for thumbnail upload
    var formData = new FormData(form);

    // Handle thumbnail upload for edit
    var editThumbnailFileInput = document.getElementById('editThumbnailFileInput');
    var editThumbnailContainer = document.getElementById('editThumbnailContainer');
    var editThumbnailPlaceholder = document.getElementById('editThumbnailPlaceholder');
    var editSelectedThumbnail = document.getElementById('editSelectedThumbnail');
    var editThumbnailOverlay = document.getElementById('editThumbnailOverlay');
    var editThumbnailUrl = document.getElementById('editThumbnailUrl');

    if (editThumbnailFileInput.files.length > 0) {
        var file = editThumbnailFileInput.files[0];
        if (!file.type.startsWith('image/')) {
            alert('Please select a valid image file for the thumbnail.');
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Changes';
            return;
        }
        if (file.size > 5 * 1024 * 1024) {
            alert('Thumbnail image file size must be less than 5MB.');
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Changes';
            return;
        }
        formData.append('thumbnail', file);
    } else {
        // If no new thumbnail, ensure the existing one is kept or cleared
        if (editThumbnailUrl.value && editThumbnailUrl.value.trim() !== '') {
            // Keep existing thumbnail
            formData.append('thumbnail_url', editThumbnailUrl.value);
        } else {
            // Clear thumbnail if no new file and no existing one
            formData.delete('thumbnail');
            formData.delete('thumbnail_url');
            editSelectedThumbnail.src = '';
            editSelectedThumbnail.style.display = 'none';
            editThumbnailPlaceholder.style.display = 'block';
        }
    }

    // Submit form via AJAX
    fetch(`/share/${sharedLayoutId}/edit`, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
           // alert(data.message);
            // Reload the page to show updated data
            window.location.reload();
        } else {
            alert('Error: ' + (data.message || 'Something went wrong'));
            hideProgressBar('editThumbnailLoadingOverlay');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('An error occurred while updating the shared layout.');
        hideProgressBar('editThumbnailLoadingOverlay');
    })
    .finally(() => {
        // Re-enable save button
        saveBtn.disabled = false;
        saveBtn.textContent = 'Save Changes';
    });
}

document.addEventListener('DOMContentLoaded', function() {
    var modalElement = document.getElementById('selectLayoutModal');
    var modal = new bootstrap.Modal(modalElement);
    var selectLayoutBtn = document.getElementById('selectLayoutBtn');
    var selectLayoutOkBtn = document.getElementById('selectLayoutOkBtn');
    var projectSelect = document.getElementById('projectSelect');
    var layoutSelect = document.getElementById('layoutSelect');
    var layoutInfoDisplay = document.getElementById('layoutInfoDisplay');
    var layoutInfoLayoutName = document.getElementById('layoutInfoLayoutName');
    var layoutInfoTourName = document.getElementById('layoutInfoTourName');
    var layoutInfoProjectName = document.getElementById('layoutInfoProjectName');
    var form = document.getElementById('shareLayoutForm');

    // Thumbnail upload functionality
    var thumbnailContainer = document.getElementById('thumbnailContainer');
    var thumbnailFileInput = document.getElementById('thumbnailFileInput');
    var thumbnailOverlay = document.getElementById('thumbnailOverlay');
    var selectedThumbnail = document.getElementById('selectedThumbnail');
    var thumbnailPlaceholder = document.getElementById('thumbnailPlaceholder');
    var selectedThumbnailUrl = document.getElementById('selectedThumbnailUrl');

    // Handle thumbnail container click
    thumbnailContainer.addEventListener('click', function() {
        thumbnailFileInput.click();
    });

    // Handle file selection
    thumbnailFileInput.addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            // Validate file type
            if (!file.type.startsWith('image/')) {
                alert('Please select a valid image file.');
                return;
            }

            // Validate file size (max 5MB)
            if (file.size > 5 * 1024 * 1024) {
                alert('Image file size must be less than 5MB.');
                return;
            }

            // Create a preview
            var reader = new FileReader();
            reader.onload = function(e) {
                selectedThumbnail.src = e.target.result;
                selectedThumbnail.style.display = 'block';
                thumbnailPlaceholder.style.display = 'none';
                selectedThumbnailUrl.value = e.target.result; // Store the data URL
            };
            reader.readAsDataURL(file);
        }
    });

    // Handle thumbnail container hover effects
    thumbnailContainer.addEventListener('mouseenter', function() {
        if (selectedThumbnail.style.display !== 'none') {
            thumbnailOverlay.style.display = 'flex';
        }
    });

    thumbnailContainer.addEventListener('mouseleave', function() {
        thumbnailOverlay.style.display = 'none';
    });

    // Handle opening the modal
    selectLayoutBtn.addEventListener('click', function() {
        // Reset the selects when opening modal
        projectSelect.selectedIndex = 0;
        layoutSelect.selectedIndex = 0;
        layoutSelect.disabled = true;
        selectLayoutOkBtn.disabled = true;
        layoutInfoDisplay.style.display = 'none'; // Hide layout info display
        modal.show();
    });

    // Handle project selection
    projectSelect.addEventListener('change', function() {
        var selectedProjectId = this.value;
        var layoutSelect = document.getElementById('layoutSelect');
        var layoutInfoDisplay = document.getElementById('layoutInfoDisplay');
        var layoutInfoLayoutName = document.getElementById('layoutInfoLayoutName');
        var layoutInfoTourName = document.getElementById('layoutInfoTourName');
        var layoutInfoProjectName = document.getElementById('layoutInfoProjectName');

        // Clear and disable layout select
        layoutSelect.innerHTML = '<option value="" data-thumbnail="" data-id="">-- Select a layout --</option>';
        layoutSelect.disabled = true;
        selectLayoutOkBtn.disabled = true;
        layoutInfoDisplay.style.display = 'none'; // Hide layout info display

        if (selectedProjectId && layoutsData[selectedProjectId]) {
            // Enable layout select and populate with layouts for selected project
            layoutSelect.disabled = false;

            layoutsData[selectedProjectId].forEach(function(layout) {
                var option = document.createElement('option');
                option.value = layout.name;
                option.setAttribute('data-thumbnail', layout.thumbnail_url || '');
                option.setAttribute('data-id', layout.id);
                option.textContent = layout.name;
                layoutSelect.appendChild(option);
            });
        }
    });

    // Handle layout selection
    layoutSelect.addEventListener('change', function() {
        var selectedOption = this.options[this.selectedIndex];
        selectLayoutOkBtn.disabled = !selectedOption.value;

        if (selectedOption.value) {
            // Get the layout data from layoutsData
            var selectedProjectId = projectSelect.value;
            var layoutId = selectedOption.getAttribute('data-id');
            var layoutData = null;
            
            // Find the layout data
            if (layoutsData[selectedProjectId]) {
                layoutData = layoutsData[selectedProjectId].find(function(layout) {
                    return layout.id == layoutId;
                });
            }
            
            // Update layout info display
            layoutInfoLayoutName.textContent = selectedOption.value;
            layoutInfoTourName.textContent = layoutData ? layoutData.tour_name : 'N/A';
            layoutInfoProjectName.textContent = layoutData ? layoutData.project_name : 'N/A';
            layoutInfoDisplay.style.display = 'block';
        } else {
            layoutInfoDisplay.style.display = 'none';
        }
    });

    // Handle OK button click
    selectLayoutOkBtn.addEventListener('click', function() {
        var selectedOption = layoutSelect.options[layoutSelect.selectedIndex];
        var layoutName = selectedOption.value;
        var thumbnailUrl = selectedOption.getAttribute('data-thumbnail');
        var layoutId = selectedOption.getAttribute('data-id');

        console.log('Selected layout:', layoutName);
        console.log('Thumbnail URL:', thumbnailUrl);
        console.log('Layout ID:', layoutId);

        // Only proceed if a layout is actually selected
        if (layoutName && layoutName.trim() !== '') {
            // Set layout_id
            document.getElementById('selectedLayoutId').value = layoutId;

            // Set title
            document.getElementById('selectedLayoutTitle').value = layoutName;

            // Set thumbnail - only if no custom thumbnail has been uploaded
            if (!selectedThumbnailUrl.value || selectedThumbnailUrl.value === '') {
                selectedThumbnailUrl.value = thumbnailUrl || '';

                var img = document.getElementById('selectedThumbnail');
                var placeholder = document.getElementById('thumbnailPlaceholder');

                if (thumbnailUrl && thumbnailUrl.trim() !== '') {
                    img.src = thumbnailUrl;
                    img.style.display = 'block';
                    placeholder.style.display = 'none';
                    console.log('Thumbnail set successfully');
                } else {
                    img.style.display = 'none';
                    placeholder.style.display = 'block';
                    console.log('No thumbnail available, showing placeholder');
                }
            }

            // Close modal
            modal.hide();
        } else {
            // Show some feedback if no layout is selected
            console.log('No layout selected');
        }
    });

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();

        var layoutId = document.getElementById('selectedLayoutId').value;
        var title = document.getElementById('selectedLayoutTitle').value;
        var thumbnailUrl = document.getElementById('selectedThumbnailUrl').value;

        if (!layoutId || !title) {
            alert('Please select a layout first.');
            return;
        }

        // Show progress bar
        showProgressBar('thumbnailLoadingOverlay', 'thumbnailProgressCircle', 'thumbnailProgressText');

        // Disable save button to prevent double submission
        var saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';

        // Prepare FormData for file upload
        var formData = new FormData(form);

        // Handle file upload
        var thumbnailFileInput = document.getElementById('thumbnailFileInput');
        if (thumbnailFileInput.files.length > 0) {
            var file = thumbnailFileInput.files[0];
            if (!file.type.startsWith('image/')) {
                alert('Please select a valid image file for the thumbnail.');
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save';
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Thumbnail image file size must be less than 5MB.');
                saveBtn.disabled = false;
                saveBtn.textContent = 'Save';
                return;
            }
            formData.append('thumbnail', file);
            // Remove the base64 data if file is uploaded
            formData.delete('thumbnail_url');
        }

        // Submit form via AJAX
        fetch(form.action, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
              //  alert(data.message);
                // Reload the page to show the new shared layout
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
                hideProgressBar('thumbnailLoadingOverlay');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving.');
            hideProgressBar('thumbnailLoadingOverlay');
        })
        .finally(() => {
            // Re-enable save button
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save';
        });
    });

    let toggleLayoutId = null;
    let toggleIsActive = null;
    let deleteLayoutId = null;

    document.addEventListener('click', function(e) {
        if (e.target.closest('.toggle-shared-layout')) {
            e.preventDefault();

            var link = e.target.closest('.toggle-shared-layout');
            toggleLayoutId = link.getAttribute('data-id');
            toggleIsActive = link.getAttribute('title') === 'Disable';

            // Set modal message
            var modalBody = document.getElementById('confirmToggleModalBody');
            modalBody.textContent = `Are you sure you want to ${toggleIsActive ? 'disable' : 'enable'} this shared layout?`;

            // Show modal
            var confirmModal = new bootstrap.Modal(document.getElementById('confirmToggleModal'));
            confirmModal.show();
        }
    });

    // Handle delete shared layout
    document.addEventListener('click', function(e) {
        if (e.target.closest('.delete-shared-layout')) {
            e.preventDefault();
            var link = e.target.closest('.delete-shared-layout');
            deleteLayoutId = link.getAttribute('data-id');

            // Show delete confirmation modal
            var deleteModal = new bootstrap.Modal(document.getElementById('confirmDeleteModal'));
            deleteModal.show();
        }
    });

    // Handle OK button in modal
    document.getElementById('confirmToggleOkBtn').addEventListener('click', function() {
        if (!toggleLayoutId) return;

        // Optionally, disable the button here

        fetch(`/share/${toggleLayoutId}/toggle`, {
            method: 'POST',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
               // alert(data.message);
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while toggling the shared layout.');
        })
        .finally(() => {
            // Optionally, re-enable the button here
            // Hide the modal
            var confirmModal = bootstrap.Modal.getInstance(document.getElementById('confirmToggleModal'));
            confirmModal.hide();
            toggleLayoutId = null;
            toggleIsActive = null;
        });
    });

    // Handle delete confirmation OK button
    document.getElementById('confirmDeleteOkBtn').addEventListener('click', function() {
        if (!deleteLayoutId) return;

        fetch(`/share/${deleteLayoutId}/delete`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while deleting.');
        })
        .finally(() => {
            // Hide the modal
            var deleteModal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
            deleteModal.hide();
            deleteLayoutId = null;
        });
    });

    // Handle share shared layout
    document.addEventListener('click', function(e) {
        if (e.target.closest('.share-shared-layout')) {
            e.preventDefault();

            var link = e.target.closest('.share-shared-layout');
            var layoutId = link.getAttribute('data-layout-id');
            var sharedLayoutId = link.getAttribute('data-shared-layout-id');

            if (!layoutId) {
                alert('Layout not available for sharing.');
                return;
            }

            // Pass both layout and shared_layout_id
            Livewire.dispatch('modal.open', {
                component: 'modals.share-tour',
                arguments: {
                    'layout': layoutId,
                    'sharedLayout': sharedLayoutId
                }
            });
        }
    });

    // Handle edit shared layout
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-shared-layout')) {
            e.preventDefault();
            showEditModal(e.target);
        }
    });

    // Handle thumbnail upload for edit modal
    var editThumbnailContainer = document.getElementById('editThumbnailContainer');
    var editThumbnailFileInput = document.getElementById('editThumbnailFileInput');
    var editThumbnailOverlay = document.getElementById('editThumbnailOverlay');
    var editSelectedThumbnail = document.getElementById('editSelectedThumbnail');
    var editThumbnailPlaceholder = document.getElementById('editThumbnailPlaceholder');

    editThumbnailContainer.addEventListener('click', function() {
        editThumbnailFileInput.click();
    });

    editThumbnailFileInput.addEventListener('change', function(e) {
        var file = e.target.files[0];
        if (file) {
            if (!file.type.startsWith('image/')) {
                alert('Please select a valid image file for the thumbnail.');
                return;
            }
            if (file.size > 5 * 1024 * 1024) {
                alert('Thumbnail image file size must be less than 5MB.');
                return;
            }

            var reader = new FileReader();
            reader.onload = function(e) {
                editSelectedThumbnail.src = e.target.result;
                editSelectedThumbnail.style.display = 'block';
                editThumbnailPlaceholder.style.display = 'none';
                editThumbnailUrl.value = e.target.result; // Store the data URL
            };
            reader.readAsDataURL(file);
        }
    });

    editThumbnailContainer.addEventListener('mouseenter', function() {
        if (editSelectedThumbnail.style.display !== 'none') {
            editThumbnailOverlay.style.display = 'flex';
        }
    });

    editThumbnailContainer.addEventListener('mouseleave', function() {
        editThumbnailOverlay.style.display = 'none';
    });

    // Handle modal hidden event for cleanup
    modalElement.addEventListener('hidden.bs.modal', function() {
        // Clean up any remaining backdrop
        var backdrop = document.querySelector('.modal-backdrop');
        if (backdrop) {
            backdrop.remove();
        }
        // Clean up body classes
        document.body.classList.remove('modal-open');
        document.body.style.paddingRight = '';
    });

    // Handle modal shown event
    modalElement.addEventListener('shown.bs.modal', function() {
        // Focus on the project select element when modal opens
        projectSelect.focus();
    });

    // Auto-open modal and pre-fill form if prefillData is available
    if (prefillData && prefillData.layout_id) {
        // Show progress bar for prefill data loading
        simulateDataLoading();
        
        // Set the form fields with prefill data
        document.getElementById('selectedLayoutId').value = prefillData.layout_id;
        document.getElementById('selectedLayoutTitle').value = prefillData.layout_name;
        
        // Set thumbnail if available
        if (prefillData.thumbnail_url && prefillData.thumbnail_url.trim() !== '') {
            document.getElementById('selectedThumbnailUrl').value = prefillData.thumbnail_url;
            var img = document.getElementById('selectedThumbnail');
            var placeholder = document.getElementById('thumbnailPlaceholder');
            img.src = prefillData.thumbnail_url;
            img.style.display = 'block';
            placeholder.style.display = 'none';
        }

    }
});
</script>
@endsection
