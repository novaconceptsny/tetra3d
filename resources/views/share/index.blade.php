@extends('layouts.redesign')

@section('content')
<div class="container-fluid">
    <h5 class="mb-4">Share layouts</h5>
    <div class="row">
        <!-- Left Panel: Share Layout Form -->
        <div class="col-md-3">
            <div class="card p-3">
                <form id="shareLayoutForm" method="POST" action="{{ route('share.store') }}">
                    @csrf
                    <!-- Select Layout Button -->
                    <button type="button" class="btn btn-outline-secondary mb-3 w-100" id="selectLayoutBtn">
                        Select layout
                    </button>
                    <!-- Hidden field for layout_id -->
                    <input type="hidden" name="layout_id" id="selectedLayoutId">
                    <!-- Thumbnail preview area -->
                    <div class="mb-3" id="thumbnailContainer" style="height: 150px; background: #f5f5f5; display: flex; align-items: center; justify-content: center;">
                        <span id="thumbnailPlaceholder">Share layout thumbnail</span>
                        <img id="selectedThumbnail" src="" alt="Selected Thumbnail" style="display:none; max-height: 100%; max-width: 100%;"/>
                    </div>
                    <!-- Title input (readonly, filled by selection) -->
                    <input type="text" class="form-control mb-2" placeholder="Title" id="selectedLayoutTitle" name="title" readonly>
                    <textarea class="form-control mb-2" placeholder="Description" name="description"></textarea>
                    <button type="submit" class="btn btn-primary w-100" id="saveBtn">Save</button>
                </form>
            </div>
        </div>
        <!-- Right Panel: Shared Layouts -->
        <div class="col-md-9">
            <div class="row g-3">
                @forelse($sharedLayouts as $sharedLayout)
                <div class="col-md-6">
                    <div class="card h-100 shadow-sm {{ !$sharedLayout->active ? 'opacity-50' : '' }}">
                        @if($sharedLayout->layout && $sharedLayout->layout->assignedTour()->getFirstMediaUrl('thumbnail'))
                            <img src="{{ $sharedLayout->layout->assignedTour()->getFirstMediaUrl('thumbnail') }}" class="card-img-top" alt="{{ $sharedLayout->title }}">
                        @else
                            <div class="card-img-top bg-light d-flex align-items-center justify-content-center" style="height: 200px;">
                                <span class="text-muted">No thumbnail</span>
                            </div>
                        @endif
                        <div class="card-body">
                            <h5 class="card-title">{{ $sharedLayout->title }}</h5>
                            <p class="card-text">{{ $sharedLayout->description ?: 'No description available.' }}</p>
                            @if(!$sharedLayout->active)
                                <span class="badge bg-danger">Disabled</span>
                            @endif
                            <div class="mt-2 d-flex gap-2">
                                <a href="#" class="text-muted" title="Preview"><i class="bi bi-eye"></i></a>
                                <a href="#" class="text-muted" title="Share"><i class="bi bi-share"></i></a>
                                <a href="#" class="text-muted edit-shared-layout" data-id="{{ $sharedLayout->id }}" data-title="{{ $sharedLayout->title }}" data-description="{{ $sharedLayout->description }}" title="Edit"><i class="bi bi-pencil"></i></a>
                                @if($sharedLayout->active)
                                    <a href="#" class="text-muted toggle-shared-layout" data-id="{{ $sharedLayout->id }}" title="Disable"><i class="bi bi-x-circle"></i></a>
                                @else
                                    <a href="#" class="text-muted toggle-shared-layout" data-id="{{ $sharedLayout->id }}" title="Enable"><i class="bi bi-check-circle"></i></a>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <div class="col-12">
                    <div class="text-center text-muted py-5">
                        <i class="bi bi-inbox fs-1"></i>
                        <p class="mt-3">No shared layouts yet. Create your first one!</p>
                    </div>
                </div>
                @endforelse
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
        <select class="form-select" id="layoutSelect">
          <option value="" data-thumbnail="" data-id="">-- Select a layout --</option>
          @foreach($layouts as $layout)
            <option value="{{ $layout->name }}" data-thumbnail="{{ $layout->assignedTour()->getFirstMediaUrl('thumbnail') }}" data-id="{{ $layout->id }}">
              {{ $layout->name }}
            </option>
          @endforeach
        </select>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-primary" id="selectLayoutOkBtn">OK</button>
      </div>
    </div>
  </div>
</div>

<!-- Modal for editing shared layout -->
<div class="modal fade" id="editSharedLayoutModal" tabindex="-1" aria-labelledby="editSharedLayoutModalLabel" aria-hidden="true">
  <div class="modal-dialog">
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
        </form>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
        <button type="button" class="btn btn-primary" id="saveEditBtn">Save Changes</button>
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
        }
        @media (min-width: 576px) {
            .modal-dialog {
                min-height: calc(100% - 3.5rem);
            }
        }
    </style>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var modalElement = document.getElementById('selectLayoutModal');
    var modal = new bootstrap.Modal(modalElement);
    var selectLayoutBtn = document.getElementById('selectLayoutBtn');
    var selectLayoutOkBtn = document.getElementById('selectLayoutOkBtn');
    var layoutSelect = document.getElementById('layoutSelect');
    var form = document.getElementById('shareLayoutForm');

    // Handle opening the modal
    selectLayoutBtn.addEventListener('click', function() {
        // Reset the select to first option when opening modal
        layoutSelect.selectedIndex = 0;
        modal.show();
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

            // Set thumbnail
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
        
        if (!layoutId || !title) {
            alert('Please select a layout first.');
            return;
        }

        // Disable save button to prevent double submission
        var saveBtn = document.getElementById('saveBtn');
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';

        // Submit form via AJAX
        fetch(form.action, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Reload the page to show the new shared layout
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while saving.');
        })
        .finally(() => {
            // Re-enable save button
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save';
        });
    });

    // Handle disable shared layout
    document.addEventListener('click', function(e) {
        if (e.target.closest('.toggle-shared-layout')) {
            e.preventDefault();
            
            var link = e.target.closest('.toggle-shared-layout');
            var sharedLayoutId = link.getAttribute('data-id');
            var isActive = link.getAttribute('title') === 'Disable'; // Check if it's the disable link
            
            if (confirm('Are you sure you want to ' + (isActive ? 'disable' : 'enable') + ' this shared layout?')) {
                // Disable the link to prevent double clicks
                link.style.pointerEvents = 'none';
                link.style.opacity = '0.5';
                
                fetch(`/share/${sharedLayoutId}/toggle`, {
                    method: 'POST',
                    headers: {
                        'X-Requested-With': 'XMLHttpRequest',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert(data.message);
                        // Reload the page to show updated status
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
                    // Re-enable the link
                    link.style.pointerEvents = '';
                    link.style.opacity = '';
                });
            }
        }
    });

    // Handle edit shared layout
    document.addEventListener('click', function(e) {
        if (e.target.closest('.edit-shared-layout')) {
            e.preventDefault();
            
            var link = e.target.closest('.edit-shared-layout');
            var sharedLayoutId = link.getAttribute('data-id');
            var title = link.getAttribute('data-title');
            var description = link.getAttribute('data-description');
            
            // Populate the edit modal with current data
            document.getElementById('editTitle').value = title;
            document.getElementById('editDescription').value = description;
            
            // Store the shared layout ID for the save operation
            document.getElementById('editSharedLayoutForm').setAttribute('data-shared-layout-id', sharedLayoutId);
            
            // Show the edit modal
            var editModal = new bootstrap.Modal(document.getElementById('editSharedLayoutModal'));
            editModal.show();
        }
    });

    // Handle save edit button
    document.getElementById('saveEditBtn').addEventListener('click', function() {
        var form = document.getElementById('editSharedLayoutForm');
        var sharedLayoutId = form.getAttribute('data-shared-layout-id');
        var title = document.getElementById('editTitle').value;
        var description = document.getElementById('editDescription').value;
        
        if (!title.trim()) {
            alert('Title is required.');
            return;
        }
        
        // Disable save button to prevent double submission
        var saveBtn = document.getElementById('saveEditBtn');
        saveBtn.disabled = true;
        saveBtn.textContent = 'Saving...';
        
        // Submit form via AJAX
        fetch(`/share/${sharedLayoutId}/edit`, {
            method: 'POST',
            body: new FormData(form),
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert(data.message);
                // Reload the page to show updated data
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Something went wrong'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('An error occurred while updating the shared layout.');
        })
        .finally(() => {
            // Re-enable save button
            saveBtn.disabled = false;
            saveBtn.textContent = 'Save Changes';
        });
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
        // Focus on the select element when modal opens
        layoutSelect.focus();
    });
});
</script>
@endsection
