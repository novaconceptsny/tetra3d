@extends('layouts.redesign')

@section('content')
<div class="container">
    <div class="row">
        <div class="col-12">
            <h5>Collections</h5>
            <div class="add-collection-card mb-5" >
                <div class="text-center p-4">
                    <button class="btn btn-light rounded-circle">
                        <i class="fas fa-plus"></i>
                    </button>
                    <p class="mt-2">Add Collection</p>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12">
            <h5>Projects</h5>
            <div class="add-project-card" data-bs-toggle="modal" data-bs-target="#addCollectionModal">
                <div class="text-center p-4">
                    <button class="btn btn-light rounded-circle">
                        <i class="fas fa-plus"></i>
                    </button>
                    <p class="mt-2">Start a new tour project</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Collection Modal -->
<div class="modal fade" id="addCollectionModal" tabindex="-1" aria-labelledby="addCollectionModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addCollectionModalLabel">Add Images</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="imageUploadForm">
                    <p class="text-muted small">Add a name and upload a jpg or PNG image file. Max 2048 pixels on the long edge of the image.</p>

                    <div id="imageEntriesContainer">
                        <!-- Image entries will be added here dynamically -->
                    </div>

                    <div class="text-end mb-3">
                        <button type="button" class="btn btn-link" id="addMoreImages">+ Select image</button>
                    </div>

                    <div class="text-center">
                        <button type="submit" class="btn btn-primary">Save</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const imageEntriesContainer = document.getElementById('imageEntriesContainer');
    const addMoreButton = document.getElementById('addMoreImages');
    const form = document.getElementById('imageUploadForm');

    // Add first image entry when modal opens
    $('#addCollectionModal').on('shown.bs.modal', function () {
        if (imageEntriesContainer.children.length === 0) {
            addImageEntry();
        }
    });

    // Add more images button click handler
    addMoreButton.addEventListener('click', addImageEntry);

    function addImageEntry() {
        const entryDiv = document.createElement('div');
        entryDiv.className = 'mb-4 image-entry';

        const entryHtml = `
            <div class="d-flex align-items-start gap-3">
                <div class="flex-grow-1">
                    <div class="mb-2">
                        <input type="text" class="form-control" name="imageName[]" placeholder="Name...">
                    </div>
                    <div class="mb-2">
                        <input type="file" class="form-control" name="imageFile[]" accept="image/jpeg,image/png" style="display: none;">
                    </div>
                </div>
                <div class="image-preview-container" style="width: 100px;">
                    <div class="image-preview border rounded p-1" style="display: none;">
                        <img src="" alt="Preview" style="width: 100%; height: auto;">
                        <button type="button" class="btn-close remove-image" style="position: absolute; top: -5px; right: -5px; background-color: white;"></button>
                    </div>
                    <button type="button" class="btn btn-outline-secondary w-100 select-image-btn">Select Image</button>
                </div>
            </div>
        `;

        entryDiv.innerHTML = entryHtml;
        imageEntriesContainer.appendChild(entryDiv);

        // Set up event listeners for this entry
        const fileInput = entryDiv.querySelector('input[type="file"]');
        const selectButton = entryDiv.querySelector('.select-image-btn');
        const preview = entryDiv.querySelector('.image-preview');
        const previewImg = preview.querySelector('img');
        const removeButton = entryDiv.querySelector('.remove-image');

        selectButton.addEventListener('click', () => fileInput.click());

        fileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.style.display = 'block';
                    selectButton.style.display = 'none';
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        removeButton.addEventListener('click', () => {
            fileInput.value = '';
            preview.style.display = 'none';
            selectButton.style.display = 'block';
        });
    }

    // Handle form submission
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        const formData = new FormData(form);

        // Send formData to your backend
        fetch('/your-upload-endpoint', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            // Handle success
            $('#addCollectionModal').modal('hide');
            // Refresh the page or update the UI
        })
        .catch(error => {
            // Handle error
            console.error('Error:', error);
        });
    });
});
</script>
@endpush

@push('styles')
<style>
    .add-collection-card, .add-project-card {
        border: 2px dashed #dee2e6;
        border-radius: 8px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .add-collection-card:hover, .add-project-card:hover {
        border-color: #6c757d;
        background-color: #f8f9fa;
    }

    .btn-light {
        width: 48px;
        height: 48px;
    }

    .image-preview-container {
        position: relative;
    }

    .image-preview {
        position: relative;
        width: 100px;
        height: 100px;
        overflow: hidden;
    }

    .image-preview img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .select-image-btn {
        height: 100px;
    }
</style>
@endpush
