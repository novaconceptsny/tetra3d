@extends('layouts.redesign')

@section('content')
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
                            @foreach($projects as $project)
                                <div class="col-md-3 layout-item">
                                    <div class="card border-0 shadow-sm bg-white">
                                        <div class="rounded img-home p-2">
                                            <img src="{{ $project->background_url }}" class="card-img-top img-fluid" alt="{{ $project->title }}">
                                        </div>
                                        <div class="card-body d-flex justify-content-between align-items-end">
                                            <p class="card-text">
                                                <span>{{ $project->name }}</span><br>
                                                <small>Created: {{ $project->created_at->format('F jS, Y') }}</small>
                                            </p>
                                            <button type="button" 
                                                    class="btn enter-link" 
                                                    data-mode="edit" 
                                                    data-project-name="{{ $project->name }}"
                                                    data-project-id="{{ $project->id }}" 
                                                    data-bs-toggle="modal" 
                                                    data-bs-target="#projectModal">
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
                <div class="image-upload-box mb-3" id="imageUploadBox">
                    <input type="file" class="image-input" id="imageInput" accept="image/jpeg, image/png">
                    <span>+ Image</span>
                    <div class="overlay">Click to replace image</div>
                </div>
                <div class="image-name" id="imageName"></div>
                <button type="button" class="btn btn-save">Save</button>
            </div>
        </div>
    </div>
</div>
@endsection

@section('styles')
    <link href="{{ mix('css/page/tour360.css') }}" rel="stylesheet">
@endsection

@section('scripts')
    <script>
        const projectModal = document.getElementById('projectModal');
        const modalTitle = document.getElementById('projectModalLabel');
        const projectNameInput = document.getElementById('projectNameInput');
        const imageUploadBox = document.getElementById('imageUploadBox');
        const imageInput = document.getElementById('imageInput');
        const imageName = document.getElementById('imageName');
        let mode = '';
        let projectId = '';

        function navigateToPhoto(photoId, layoutId) {
            window.location.href = `/photos/${photoId}?layout_id=${layoutId}`;
        }
        // Xử lý khi modal được mở
        projectModal.addEventListener('show.bs.modal', (event) => {
            const button = event.relatedTarget; // Button that triggered the modal
            mode = button.getAttribute('data-mode'); // Get mode (create or edit)
            projectId = button.getAttribute('data-project-id'); // Add this line

            // Update title and default values based on mode
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

        // Xử lý upload ảnh
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

        // Replace the save button click handler with this updated version
        document.querySelector('.btn-save').addEventListener('click', async function() {
            const formData = new FormData();
            formData.append('title', projectNameInput.value);
            formData.append('image', imageInput.files[0]);

            try {
                let url = mode === 'create' ? '/tour360/store' : `/tour360/update/${projectId}`;
                const response = await fetch(url, {
                    method: 'POST',
                    body: formData,
                    headers: {
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                    }
                });

                const data = await response.json();
                
                if (data.success) {
                    // Close modal
                    const modal = bootstrap.Modal.getInstance(projectModal);
                    modal.hide();
                    
                    // Refresh the page to show updated project
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while saving the project');
            }
        });
    </script>
@endsection
