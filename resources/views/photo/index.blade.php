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
                            <button class="btn btn-primary"><i class="fas fa-pen"></i> Edit project</button>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <div class="container-fluid main-content my-5">
            <div class="row">
                <!-- Sidebar: Collections -->
                <div class="col-md-6 col-xl-2 order-md-1 mb-3">
                    <div class="sidebar bg-white rounded">
                        <div style="font-size: 24px; font-weight: bold;">Collections <a href="#" class="enter-link">Enter</a></div>
                        <ul class="list-group">
                            <li class="list-group-item d-flex align-items-center border rounded p-2 mb-2">
                                <button class="add-collection-btn" data-bs-toggle="modal" data-bs-target="#addCollectionModal">
                                    <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                    <span class="add-collection-text">Add Collection</span>
                                </button>
                            </li>
                            @foreach($artworkCollections as $artworkCollection)
                                @if($project->artworkCollections->contains($artworkCollection->id))
                                    <li class="list-group-item d-flex align-items-center border rounded p-2 mb-2">
                                        <i class="fas fa-image collection-icon"></i>
                                        <div class="collection-info">
                                            <span class="collection-name">{{ $artworkCollection->name }}</span>
                                            <span class="collection-items">{{ $artworkCollection->photos_count ?? 0 }} items</span>
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
                <div class="col-md-12 col-xl-7 order-md-3 order-xl-2 mb-3">
                    <!-- Photos Section -->
                    <div class="photos-section bg-white rounded overflow-hidden">
                        <div     style="font-size: 24px; font-weight: bold;">Photos
                            <button class="btn enter-link" id="toggleButton">Enter</button>
                            <button class="btn enter-link" id="duplicateImages">Duplicate images</button>
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
                            @foreach($photos as $photo)
                            <div class="col-md-3 photo-item">
                                <div class="card shadow-sm photo-card">
                                    <div class="overflow-hidden img-home">
                                        <img src="{{ $photo->getUrl() }}" class="card-img-top img-fluid" alt="{{ $photo->name }}">
                                    </div>
                                    <div class="card-body d-flex justify-content-between align-items-center">
                                        <p class="card-text">{{ $photo->name }}</p>
                                        <div class="dropdown">
                                            <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="fas fa-ellipsis-v ms-auto"></i>
                                            </button>
                                            <ul class="dropdown-menu">
                                                <li><a class="dropdown-item delete-item" href="#" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Delete</a></li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Surfaces Section -->
                <div class="col-md-6 col-xl-3 order-md-2">
                    <div class="surfaces-section bg-white rounded overflow-hidden">
                        <div style="font-size: 24px; font-weight: bold;">Surfaces <button class="btn enter-link" id="toggleButtonSurfaces">Enter</button></div>

                        <ul class="list-group">
                            <li class="list-group-item d-flex justify-content-center align-items-center card surface-item">
                                <button class="add-image-btn" data-bs-toggle="modal" data-bs-target="#surfaceModal" data-action="add">
                                    <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                    <span class="add-image-text">Add Surface</span>
                                </button>
                            </li>
                            <li class="list-group-item d-flex justify-content-center align-items-center card surface-item" data-name="North Wall" data-width="600" data-height="300">
                                <span class="surface-name">North Wall</span>
                                <div class="dropdown position-absolute top-0 end-0">
                                    <button class="btn btn-link" type="button" id="dropdownMenuButton1" data-bs-toggle="dropdown" aria-expanded="false">
                                        <i class="fas fa-ellipsis-v ms-auto"></i>
                                    </button>
                                    <ul class="dropdown-menu" aria-labelledby="dropdownMenuButton1">
                                        <li><a class="dropdown-item edit-item" href="#" data-bs-toggle="modal" data-bs-target="#surfaceModal" data-action="edit">Edit</a></li>
                                        <li><a class="dropdown-item delete-item" href="#" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Delete</a></li>
                                    </ul>
                                </div>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Layout Section -->
            <div class="layout-section">
                <div style="font-size: 24px; font-weight: bold;">Layout 1</div>
                <div class="row g-3" id="layout1Container">
                    <div class="col-md-3 layout-item">
                        <div class="card bg-white card-layout">
                            <button class="add-image-btn">
                                <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                <span class="add-image-text">Add Layout</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Layout 2 Section -->
            <div class="layout-section">
                <div style="font-size: 24px; font-weight: bold;">Layout 2</div>
                <div class="row g-3" id="layout2Container">
                    <div class="col-md-3 layout-item">
                        <div class="card bg-white card-layout">
                            <button class="add-image-btn">
                                <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                <span class="add-image-text">Add Layout</span>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
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
        <div class="modal fade" id="addImageModal" tabindex="-1" aria-labelledby="addImageModalLabel" aria-hidden="true">
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
                        <div class="row mb-3">
                            <div class="col-md-8">
                                <input type="text" class="form-control" id="titleImage" placeholder="Image title (editable)">
                            </div>
                            <div class="col-md-4 text-end">
                                <button type="button" id="saveLayout" class="btn btn-light">Save</button>
                            </div>
                        </div>
                        
                        <div class="row mb-3">
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">Width</span>
                                    <input type="number" class="form-control" id="rectWidth" value="100">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="input-group">
                                    <span class="input-group-text">Height</span>
                                    <input type="number" class="form-control" id="rectHeight" value="100">
                                    <span class="input-group-text">cm</span>
                                </div>
                            </div>
                        </div>

                        <div class="text-center position-relative">
                            <canvas id="imageCanvas" class="img-fluid"></canvas>
                        </div>
                    </div>
                </div>
            </div>
        </div>



@endsection
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.6/dist/umd/popper.min.js"></script>
@endpush
@push('scripts')
<script>

        let selectedImages = [];

        function previewImages(event) {
            const files = event.target.files;
            const previewContainer = document.getElementById('imagePreviewContainer');

            for (let i = 0; i < files.length; i++) {
                const file = files[i];
                if (file.type === 'image/jpeg' || file.type === 'image/png') {
                    const reader = new FileReader();
                    reader.onload = function (e) {
                        const imageData = {
                            src: e.target.result,
                            name: file.name.replace(/\.[^/.]+$/, "") // Remove file extension
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
                    reader.readAsDataURL(file);
                }
            }
        }

        function updateImageName(input, index) {
            selectedImages[index].name = input.value;
        }

        function removeImage(element, index) {
            selectedImages.splice(index, 1);
            element.closest('.image-preview').remove();
        }

        function saveImages() {
            const photosContainer = document.getElementById('photosContainer');

            selectedImages.forEach(image => {
                const colDiv = document.createElement('div');
                colDiv.classList.add('col-md-3', 'photo-item');
                colDiv.innerHTML = `
                    <div class="card shadow-sm photo-card">
                        <div class="overflow-hidden img-home">
                            <img src="${image.src}" class="card-img-top img-fluid" alt="${image.name}">
                        </div>
                        <div class="card-body d-flex justify-content-between align-items-center">
                            <p class="card-text">${image.name}</p>
                            <div class="dropdown">
                                <button class="btn" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="fas fa-ellipsis-v ms-auto"></i>
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#imageModal" data-title="${image.name}" data-image="${image.src}">Surface Size</a></li>
                                    <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#addCollectionModal">Edit</a></li>
                                    <li><a class="dropdown-item delete-item" href="#" data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">Delete</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                `;
                photosContainer.appendChild(colDiv);
            });

            // Reset modal
            selectedImages = [];
            document.getElementById('imagePreviewContainer').innerHTML = '';
            document.getElementById('imageInput').value = '';

            // Close modal
            const modal = bootstrap.Modal.getInstance(document.getElementById('addImageModal'));
            modal.hide();
        }

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

        document.getElementById('confirmDeleteButton').addEventListener('click', function() {
            if (itemToDelete) {
                itemToDelete.remove();
                itemToDelete = null;
                const modal = bootstrap.Modal.getInstance(document.getElementById('confirmDeleteModal'));
                modal.hide();
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

        const surfaceModalImage = document.getElementById('imageModal');
        let currentIndexImage = null;

        surfaceModalImage.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;

            const title = button.getAttribute('data-title');
            const image = button.getAttribute('data-image');

            const modalTitle = this.querySelector('#imageModalLabel');
            const titleImage = this.querySelector('#titleImage');
            // const modalTextTitle = this.querySelector('#textareaModal');
            modalTitle.textContent = title;
            titleImage.value = title;
            // modalTextTitle.value = title;

            const imageItem = button.closest('.layout-item');

            currentIndexImage = Array.from(document.querySelectorAll('.layout-item')).indexOf(imageItem);
            const modalImage = this.querySelector('.modal-image');
            modalImage.src = image;
        });



        document.addEventListener('DOMContentLoaded', () => {
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
                let points;
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
                    points = endPoints;
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
            document.getElementById('saveLayout').addEventListener('click', function() {
                if (!widthInput || !heightInput) {
                    console.error('Width or height input not found');
                    return;
                }

                const titleInput = document.getElementById('titleImage');
                const newTitle = titleInput.value;

                // Update the surface data
                const surfaceData = {
                    width: parseInt(widthInput.value) || 100,
                    height: parseInt(heightInput.value) || 100,
                    rect: { ...rect },
                    title: newTitle
                };

                console.log('Saving surface data:', surfaceData);

                // Update the image title in the card if we have a reference to it
                if (currentImageElement) {
                    // Update the card title
                    const cardTitle = currentImageElement.querySelector('.card-text');
                    if (cardTitle) cardTitle.textContent = newTitle;

                    // Update the Surface Size link data-title attribute
                    const surfaceSizeLink = currentImageElement.querySelector('[data-bs-target="#imageModal"]');
                    if (surfaceSizeLink) surfaceSizeLink.setAttribute('data-title', newTitle);
                }
                
                // Close modal
                const modal = bootstrap.Modal.getInstance(imageModal);
                if (modal) {
                    modal.hide();
                }
            });

            // Handle duplicate images button click
            document.getElementById('duplicateImages').addEventListener('click', function() {
                // Get all current photos (excluding the "Add Image" card)
                const photoContainer = document.getElementById('photosContainer');
                const photos = photoContainer.querySelectorAll('.photo-item:not(:first-child)');

                console.log(photos, 'photos');
                
                if (!photos.length) {
                    alert('No images to duplicate');
                    return;
                }

                // Create layout content
                let layoutHTML = '';

                // Add each photo to the layout
                photos.forEach(photo => {
                    const img = photo.querySelector('img');
                    const title = photo.querySelector('.card-text').textContent;
                    
                    if (img && title) {
                        const now = new Date();
                        layoutHTML += `
                            <div class="col-md-3 layout-item">
                                <div class="card shadow-sm bg-white">
                                    <div class="overflow-hidden img-home">
                                        <img src="${img.src}" class="card-img-top img-fluid" alt="${title}">
                                    </div>
                                    <div class="card-body d-flex justify-content-between align-items-end">
                                        <p class="card-text">
                                            <span>${title}</span><br>
                                            <small>Created: ${now.toLocaleDateString()}</small>
                                        </p>
                                        <button type="button" 
                                                class="btn enter-link" 
                                                data-bs-toggle="modal" 
                                                data-bs-target="#imageModal" 
                                                data-title="${title}" 
                                                data-image="${img.src}">
                                            Enter
                                        </button>
                                    </div>
                                </div>
                            </div>
                        `;
                    }
                });

                // Add the "Add Layout" button to the end
                layoutHTML += `
                    <div class="col-md-3 layout-item">
                        <div class="card bg-white card-layout">
                            <button class="add-image-btn">
                                <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                <span class="add-image-text">Add Layout</span>
                            </button>
                        </div>
                    </div>
                `;

                // Find the layout1Container and update its content
                const layoutSection = document.getElementById('layout1Container');
                if (layoutSection) {
                    layoutSection.innerHTML = layoutHTML;
                } else {
                    console.error('Layout 1 container not found');
                }
            });
        });

</script>


@endpush

@push('styles')
<style>

    .container-fluid {
        padding-left:  12px !important;
        padding-right:  12px !important;
    }

    /* Header */
    .project-name {
        font-size: 30px;
        font-weight: bold;
        margin: 0;
    }
    .nav-link {
        color: #000;
        font-size: 22px;
    }
    .nav-link.active {
        border-bottom: 2px solid #000;
    }
    /* Sidebar */
    .sidebar {
        padding: 20px;
    }
    .sidebar h2 {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 15px;
        display: flex;
        justify-content: space-between;
    }
    .enter-link {
        float: right;
        font-size: 14px;
        color: #007bff;
        text-decoration: none;
        display: flex;
        justify-content: space-between;
    }
    .add-btn {
        width: 100%;
        margin-bottom: 15px;
        background-color: #f1f3f5;
        border: 1px solid #ddd;
        color: #000;
        display: flex;
        justify-content: space-between;
    }
    .list-group-item {
        border: none;
        padding: 10px 0;
        font-size: 14px;
        border: unset !important;
    }
    .list-group-item.border {
        border: 1px solid #dee2e6 !important;
    }
    .badge {
        font-size: 12px;
    }
    /* Photos Section */
    .photos-section {
        padding: 20px;
    }
    .photos-section h2 {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .photos-section .img-home{
        height: 164px;
    }
    .card {
        border: none;
        padding: 15px;
        margin-bottom: 15px;
        background-color: #F9FAFB;
    }
    .card-img-top {
        width: 100%;
        object-fit: cover;
    }
    .card-body {
        padding: 10px 0;
    }
    .card-text {
        font-size: 16px;
        margin-bottom: 5px;
    }
    /* Layout Section */
    .layout-section {
        margin-bottom: 2rem;
        padding: 1rem;
        background-color: #fff;
        border-radius: 0.5rem;
    }
    .layout-section h2 {
        font-size: 24px;
        font-weight: bold;
        margin-bottom: 40px;
    }
    .layout-section .card-text small {
        color: #6c757d;
        font-size: 12px;
    }
    /* Surfaces Section */
    .surfaces-section {
        padding: 20px;
        border-left: 1px solid #ddd;
    }

    .surfaces-section h2 {
        font-size: 18px;
        font-weight: bold;
        margin-bottom: 15px;
    }

    .list-group-item {
        border: none;
        padding: 12px 0;
        font-size: 14px;
        display: flex;
        align-items: center;
    }

    .surface-name {
        font-size: 14px;
        font-weight: bold;
        color: #000;
    }

    .list-group-item i.fa-ellipsis-v {
        font-size: 14px;
        color: #6c757d;
    }
    /* Modal Styling */
    .modal-content {
        border-radius: 10px !important;
    }
    .modal-header {
        border-bottom: none;
    }
    .modal-title {
        font-size: 18px;
        font-weight: bold;
    }
    .modal-footer {
        border-top: none;
        justify-content: flex-end;
    }
    .btn-primary {
        background-color: #007bff;
        border: none;
        border-radius: 5px;
        padding: 8px 20px;
    }
    /* Tags Styling */
    .tags-container {
        display: flex;
        flex-wrap: wrap;
        gap: 10px;
        margin-bottom: 20px;
    }
    .tag {
        background-color: #e9ecef;
        border-radius: 15px;
        padding: 5px 10px;
        font-size: 14px;
        display: flex;
        align-items: center;
        gap: 5px;
    }
    .tag i {
        cursor: pointer;
        color: #6c757d;
    }
    .tag-input {
        flex: 1;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 5px 10px;
        font-size: 14px;
        outline: none;
    }
    /* Modal for Add Image */
    .modal-body p {
        font-size: 14px;
        color: #6c757d;
        margin-bottom: 20px;
    }
    #imagePreviewContainer {
        display: flex;
        flex-wrap: wrap;
        gap: 15px;
        margin-bottom: 20px;
    }
    .image-preview {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 15px;
    }
    .preview-header {
        display: flex;
        align-items: center;
        padding: 10px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }
    .image-name-input {
        flex-grow: 1;
        margin-right: 10px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 5px 10px;
    }
    .preview-image {
        padding: 10px;
    }
    .preview-image img {
        width: 100%;
        height: auto;
        border-radius: 4px;
    }
    .remove-btn {
        cursor: pointer;
        padding: 5px 10px;
        color: #dc3545;
    }
    .remove-btn:hover {
        color: #c82333;
    }
    .select-image-btn {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 8px 15px;
    }
    .modal-footer .btn-light {
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 8px 20px;
    }
    /* Add Image Button Styling */
    .add-image-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: none;
        border: none;
        padding: 10px;
        cursor: pointer;
    }
    .icon-circle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background-color: #e6f0fa;
        border-radius: 50%;
        margin-bottom: 5px;
    }
    .icon-circle i {
        font-size: 16px;
        color: #000;
    }
    .add-image-text {
        font-size: 14px;
        font-weight: bold;
        color: #000;
    }
    .add-image-btn:hover .icon-circle {
        background-color: #d0e4f5;
    }
    /* Add Image Button Styling */
    .add-image-card {
        background-color: #F9FAFB;
    }
    .add-image-btn {
        display: flex;
        flex-direction: column;
        align-items: center;
        background: none;
        border: none;
        padding: 10px;
        cursor: pointer;
    }
    .icon-circle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 40px;
        height: 40px;
        background-color: #e6f0fa;
        border-radius: 50%;
        margin-bottom: 5px;
    }
    .icon-circle i {
        font-size: 16px;
        color: #000;
    }
    .add-image-text {
        font-size: 14px;
        font-weight: bold;
        color: #000;
    }
    .add-image-btn:hover .icon-circle {
        background-color: #d0e4f5;
    }
    .card-layout{
        border: 1px dotted #ccc;
    }
    /* Add Collection Button Styling */
    .add-collection-btn {
        display: flex;
        align-items: center;
        background-color: #fff;
        border: none;
        border-radius: 5px;
        padding: 10px;
        width: 100%;
        cursor: pointer;
        transition: background-color 0.2s;
    }
    .add-collection-btn:hover {
        background-color: #f8f9fa;
    }
    .icon-circle {
        display: flex;
        align-items: center;
        justify-content: center;
        width: 30px;
        height: 30px;
        background-color: #e6f0fa;
        border-radius: 50%;
        margin-right: 10px;
    }
    .icon-circle i {
        font-size: 14px;
        color: #000;
    }
    .add-collection-text {
        font-size: 14px;
        font-weight: bold;
        color: #000;
    }
    /* Collection List Styling */
    .list-group-item {
        border: none;
        padding: 10px 0;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
    }
    .collection-icon {
        font-size: 20px;
        margin-right: 10px;
        color: #000;
    }
    .collection-thumbnail {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 5px;
        margin-right: 10px;
    }
    .collection-info {
        display: flex;
        flex-direction: column;
    }
    .collection-name {
        font-size: 14px;
        font-weight: bold;
        color: #000;
    }
    .collection-items {
        font-size: 12px;
        color: #6c757d;
    }
    .list-group-item i.fa-ellipsis-v {
        font-size: 14px;
        color: #6c757d;
        position: absolute;
        top: 9px;
        right: 9px;
    }


    #surfaceModal .modal-content {
        border-radius: 10px;
        padding: 20px;
    }
    #surfaceModal .modal-header {
        border-bottom: none;
        padding-bottom: 0;
    }
    #surfaceModal .modal-title {
        font-size: 16px;
        font-weight: bold;
        color: #333;
    }
    #surfaceModal .modal-body {
        padding-top: 0;
    }
    #surfaceModal .form-label {
        font-size: 14px;
        font-weight: bold;
        color: #333;
    }
    #surfaceModal .form-control {
        border: none !important;
        border-bottom: 1px solid #333 !important;
        border-radius: 0 !important;
        padding: 5px 0 !important;
        font-size: 14px !important;
    }
    #surfaceModal .form-control:focus {
        box-shadow: none !important;
        border-bottom: 1px solid #333 !important;
    }
    #surfaceModal .input-group {
        display: flex;
        align-items: center;
        gap: 10px;
    }
    #surfaceModal .input-group-text {
        background: none;
        border: none;
        font-size: 14px;
        padding: 0;
    }
    #surfaceModal .modal-footer {
        border-top: none;
        justify-content: center;
    }
    #surfaceModal .btn-save {
        width: 100%;
        background-color: #f0f0f0 !important;
        border: 1px solid #ddd;
        border-radius: 5px;
        padding: 10px;
        font-size: 14px;
        font-weight: bold;
        color: #333 !important;
    }
    #surfaceModal .btn-save:hover {
        background-color: #e0e0e0 !important;
    }

    #imageModal .modal-content {
     border-radius: 10px; /* Bo góc nhẹ */
    }

    #imageModal .modal-header {
        border-bottom: none; /* Bỏ đường viền dưới của header */
        padding: 10px 20px; /* Giảm padding */
    }

    #imageModal .modal-title {
        font-size: 16px; /* Kích thước chữ tiêu đề */
        font-weight: bold;
        color: #333; /* Màu chữ tiêu đề */
    }

    #imageModal .modal-body img {
        border: 1px solid #ccc;
        border-radius: 5px;
        max-height: 400px;
        object-fit: cover;
    }

    #imageModal .modal-footer {
        border-top: none;
        padding: 0;
    }

    #imageModal .btn-primary {
        background-color: #f0f0f0;
        color: #333;
        border: none;
        border-radius: 20px;
        padding: 5px 20px;
        font-size: 14px;
        font-weight: 500;
    }

    #imageModal .btn-primary:hover {
        background-color: #e0e0e0;
    }

    #imageModal .modal-body {
        padding: 20px;
    }

    .photos-section, .surfaces-section {
        max-height: 357px;
        transition: max-height 0.3s ease;
    }
    .photos-section.expanded, .surfaces-section.expanded {
        max-height: 1000px; /* hoặc auto nếu bạn dùng JS để xử lý chính xác */
    }

    .overflow-hidden {
        overflow: hidden;
    }

    .header-logo {
        margin-top: 1rem;
    }


    @media (max-width: 767.98px) {
        .header-logo {
            border-top: none;
        }
        .navbar-nav {
            text-align: center;
        }
    }

    @media (min-width: 767.98px) {
        .header .navbar-collapse{
            display: none !important;
        }
    }


    .photos-section, .surfaces-section {
         max-height: 350px;
     }

     .image-container {
        position: relative;
        display: inline-block;
    }

    .rectangle {
        position: absolute;
        border: 2px solid red;
        display: none;
        background-repeat: no-repeat;
        overflow: hidden;
    }

    .dot {
        position: absolute;
        width: 10px;
        height: 10px;
        background-color: red;
        border-radius: 50%;
    }

    .top-left {
        top: -5px;
        left: -5px;
    }

    .top-right {
        top: -5px;
        right: -5px;
    }

    .bottom-left {
        bottom: -5px;
        left: -5px;
    }

    .bottom-right {
        bottom: -5px;
        right: -5px;
    }

    /* Select2 Custom Styles */
    .select2-container--bootstrap-5 .select2-selection {
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 0.375rem 0.75rem;
        min-height: 38px;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice {
        background-color: #e9ecef;
        border: 1px solid #dee2e6;
        border-radius: 0.25rem;
        padding: 2px 8px;
        margin: 2px;
    }

    .select2-container--bootstrap-5 .select2-selection--multiple .select2-selection__choice__remove {
        margin-right: 5px;
        color: #6c757d;
    }

    .select2-container--bootstrap-5 .select2-dropdown {
        border-color: #dee2e6;
        border-radius: 0.25rem;
    }

    .select2-container--bootstrap-5 .select2-results__option--highlighted[aria-selected] {
        background-color: #0d6efd;
        color: white;
    }

    #imageCanvas {
        max-width: 100%;
        height: auto;
        border: 1px solid #ddd;
        background-color: #f8f8f8; /* Light gray background to show canvas bounds */
    }

    .modal-lg {
        max-width: 900px;
    }

    #titleImage {
        border: none;
        border-bottom: 1px solid #333;
        border-radius: 0;
        padding: 5px 0;
    }

    #titleImage:focus {
        box-shadow: none;
        border-bottom: 2px solid #333;
    }

    #saveLayout {
        background-color: #f0f0f0;
        border: 1px solid #ddd;
        padding: 5px 20px;
    }

    .input-group {
        border: 1px solid #dee2e6;
        border-radius: 4px;
        overflow: hidden;
    }

    .input-group-text {
        background: none;
        border: none;
        color: #6c757d;
    }

    .input-group .form-control {
        border: none;
        text-align: center;
    }

    .input-group .form-control:focus {
        box-shadow: none;
    }

    .image-preview {
        border: 1px solid #dee2e6;
        border-radius: 8px;
        overflow: hidden;
        margin-bottom: 15px;
    }

    .preview-header {
        display: flex;
        align-items: center;
        padding: 10px;
        background-color: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
    }

    .image-name-input {
        flex-grow: 1;
        margin-right: 10px;
        border: 1px solid #dee2e6;
        border-radius: 4px;
        padding: 5px 10px;
    }

    .preview-image {
        padding: 10px;
    }

    .preview-image img {
        width: 100%;
        height: auto;
        border-radius: 4px;
    }

    .remove-btn {
        cursor: pointer;
        padding: 5px 10px;
        color: #dc3545;
    }

    .remove-btn:hover {
        color: #c82333;
    }

    .layout-item .card {
        height: 100%;
    }

    .layout-item .img-home {
        height: 200px;
        overflow: hidden;
    }

    .layout-item .img-home img {
        width: 100%;
        height: 100%;
        object-fit: cover;
    }

    .card-layout {
        height: 100%;
        min-height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
</style>
@endpush
