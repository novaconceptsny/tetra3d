@extends('layouts.redesign')

@section('content')
<div class="container">
    <div id="dashboard-section" class="row">
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
                                            <button class="btn enter-link" >
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
                                            <div class="d-flex justify-content-between align-items-start mb-3">
                                                <p class="card-text mb-0">
                                                    <span>{{ $project->name }}</span><br>
                                                    <small>Created: {{ $project->created_at->format('F jS, Y') }}</small>
                                                </p>
                                                <div class="d-flex flex-column justify-content-end mb-2 gap-1">
                                                    <div class="action-icons">
                                                        <button class="btn btn-link p-0 me-2" onclick="handleEditProject({{ $project->id }})">
                                                            <i class="fas fa-edit"></i>
                                                        </button>
                                                        <button class="btn btn-link p-0" onclick="handleDeleteProject({{ $project->id }})">
                                                            <i class="fas fa-trash"></i>
                                                        </button>
                                                    </div>

                                                    <button type="button"
                                                        class="btn enter-link"
                                                        data-mode="edit"
                                                        data-project-name="{{ $project->name }}"
                                                        data-project-id="{{ $project->id }}"
                                                        data-bs-toggle="modal"
                                                        >
                                                    Enter
                                                </button>

                                                </div>

                                            </div>
                                            <hr class="my-2">
                                            <div class="project-stats">
                                                <span class="me-3"><i class="fas fa-cube"></i> {{ $project->tours_count ?? 0 }} Tours</span>
                                                <span class="me-3"><i class="fas fa-users"></i> {{ $project->contributors_count ?? 0 }} Contributors</span>
                                                <span><i class="fas fa-folder"></i> {{ $project->collections_count ?? 0 }} Collections</span>
                                            </div>
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
                                <!-- <button
                                    class="add-image-btn create-new-box"
                                    data-mode="create"
                                    data-bs-toggle="modal"
                                    data-bs-target="#projectModal"
                                >
                                    <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                    <span class="add-image-text">Create New Project</span>
                                </button> -->
                                <button
                                    class="add-image-btn create-new-box"
                                    onclick="openCreateProject()"
                                >
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

    <div class="create-project-section" id="createProjectSection" style="display: none;">
        <div class="card p-4">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h5 class="modal-title">Add new project</h5>
                <button type="button" class="btn-close close-create-section"></button>
            </div>

            <div class="mb-3">
                <label for="inlineProjectNameInput" class="form-label">Name</label>
                <input type="text" class="form-control" id="inlineProjectNameInput" placeholder="Name">
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

            <div class="mb-3">
                <label for="inlineContributors" class="form-label">Contributors</label>
                <div class="input-group">
                    <select id="inlineContributors"  name="states[]" multiple="multiple" style="width: 100%;">
                        <!-- <option selected disabled>Select Contributor</option> -->
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label for="inlineUnits" class="form-label">Units</label>
                <select class="form-select" id="inlineUnits">
                    <option selected disabled>Select measurement units</option>
                    <option value="imperial">Imperial</option>
                    <option value="metric">Metric</option>
                </select>
            </div>

            <div class="mb-3 col-md-4">
                <label class="form-label">Thumbnail</label>
                <div class="image-upload-box mb-2" id="inlineImageUploadBox">
                    <input type="file" class="image-input" id="inlineImageInput" accept="image/jpeg, image/png">
                    <span>Click to add image</span>
                    <div class="overlay">Click to replace image</div>
                </div>
                <div class="image-name" id="inlineImageName"></div>
            </div>

            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-primary mb-3" id="inlineSaveButton" style="width: 200px" onclick="handleCreateProject()">Create</button>
            </div>
            <div class="d-flex justify-content-center">
                <button type="button" class="btn btn-primary" id="inlineCancelButton" onclick="closeCreateProject()" style="width: 200px">Cancel</button>
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

<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
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

        /* Remove the static first-child styles since we'll apply them dynamically */
    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script>
        const projectModal = document.getElementById('projectModal');
        const modalTitle = document.getElementById('projectModalLabel');
        const projectNameInput = document.getElementById('projectNameInput');
        const imageUploadBox = document.getElementById('imageUploadBox');
        const imageInput = document.getElementById('imageInput');
        const imageName = document.getElementById('imageName');
        let mode = '';
        let projectId = '';

        // Add references to dashboard and create project sections
        const dashboardSection = document.getElementById('dashboard-section');
        const createProjectSection = document.getElementById('createProjectSection');

        // New form elements
        const inlineProjectNameInput = document.getElementById('inlineProjectNameInput');
        const inlineTourSelect = document.getElementById('inlineTourSelect');
        const inlineCollections = document.getElementById('inlineCollections');
        const inlineContributors = document.getElementById('inlineContributors');
        const inlineUnits = document.getElementById('inlineUnits');
        const inlineImageUploadBox = document.getElementById('inlineImageUploadBox');
        const inlineImageInput = document.getElementById('inlineImageInput');
        const inlineImageName = document.getElementById('inlineImageName');
        const inlineSaveButton = document.getElementById('inlineSaveButton');

        async function openCreateProject() {
            try {
                // Fetch data from the create endpoint
                const response = await fetch('/tour360/create');
                const data = await response.json();

                // Populate the tour select dropdown
                // inlineTourSelect.innerHTML = '<option selected disabled>Select Tour</option>';
                data.tours.forEach(tour => {
                    inlineTourSelect.innerHTML += `<option value="${tour.id}">${tour.name}</option>`;
                });

                // Populate the contributors select dropdown
                // inlineContributors.innerHTML = '<option selected disabled>Select Contributor</option>';
                data.users.forEach(user => {
                    inlineContributors.innerHTML += `<option value="${user.id}">${user.name}</option>`;
                });

                // Populate the collections select dropdown
                // inlineCollections.innerHTML = '<option selected disabled>Select Collection</option>';
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
        }

        function handleCreateProject() {
            // Create FormData object to handle file upload
            const formData = new FormData();
            
            // Get all form values
            const name = document.getElementById('inlineProjectNameInput').value;
            const tours = $('#inlineTourSelect').val(); // Using jQuery for Select2
            const collections = $('#inlineCollections').val();
            const contributors = $('#inlineContributors').val();
            const units = document.getElementById('inlineUnits').value;
            const thumbnailFile = document.getElementById('inlineImageInput').files[0];

            // Validate required fields
            if (!name) {
                alert('Please enter a project name');
                return;
            }

            if (!thumbnailFile) {
                alert('Please select a thumbnail image');
                return;
            }

            // Append all data to FormData
            formData.append('name', name);
            formData.append('tour_ids', JSON.stringify(tours));
            formData.append('artwork_collection_ids', JSON.stringify(collections));
            formData.append('user_ids', JSON.stringify(contributors));
            formData.append('units', units);
            formData.append('thumbnail', thumbnailFile);

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
            // First fetch the project data
            fetch(`/tour360/create`, {
                headers: {
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Get the project element
                    const projectElement = document.querySelector(`[data-project-id="${id}"]`);
                    const project = {
                        id: id,
                        name: projectElement.getAttribute('data-project-name'),
                        units: projectElement.getAttribute('data-project-units') || 'imperial',
                        tour_ids: projectElement.getAttribute('data-project-tours') ? JSON.parse(projectElement.getAttribute('data-project-tours')) : [],
                        collection_ids: projectElement.getAttribute('data-project-collections') ? JSON.parse(projectElement.getAttribute('data-project-collections')) : [],
                        contributor_ids: projectElement.getAttribute('data-project-contributors') ? JSON.parse(projectElement.getAttribute('data-project-contributors')) : []
                    };

                    // Show the create project section (we'll reuse it for editing)
                    dashboardSection.style.display = 'none';
                    createProjectSection.style.display = 'block';

                    // Populate form with existing data
                    document.getElementById('inlineProjectNameInput').value = project.name;
                    document.getElementById('inlineUnits').value = project.units;

                    // Populate dropdowns
                    populateSelect('inlineTourSelect', data.tours);
                    populateSelect('inlineCollections', data.artworkCollections);
                    populateSelect('inlineContributors', data.users);
                    // Set up Select2 dropdowns with existing values
                    $('#inlineTourSelect').val(project.tour_ids).trigger('change');
                    $('#inlineCollections').val(project.collection_ids).trigger('change');
                    $('#inlineContributors').val(project.contributor_ids).trigger('change');

                    // Update the save button to handle edit
                    const saveButton = document.getElementById('inlineSaveButton');
                    saveButton.textContent = 'Update';
                    saveButton.onclick = () => handleUpdateProject(id);


                } else {
                    alert(data.message || 'Failed to load project data');
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred while loading project data');
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
            const units = document.getElementById('inlineUnits').value;
            const thumbnailFile = document.getElementById('inlineImageInput').files[0];

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
            formData.append('units', units);
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
                opt.textContent = option.name;
                select.appendChild(opt);
            });
        }

        // Handle inline image upload
        inlineImageUploadBox.addEventListener('click', () => {
            inlineImageInput.click();
        });

        inlineImageInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    const img = document.createElement('img');
                    img.src = e.target.result;
                    img.className = 'img-preview';
                    inlineImageUploadBox.innerHTML = '';
                    inlineImageUploadBox.appendChild(img);
                    const overlay = document.createElement('div');
                    overlay.className = 'overlay';
                    overlay.textContent = 'Click to replace image';
                    inlineImageUploadBox.appendChild(overlay);
                    inlineImageUploadBox.appendChild(inlineImageInput);
                    inlineImageName.textContent = file.name;
                };
                reader.readAsDataURL(file);
            }
        });

        // Handle inline save button
        inlineSaveButton.addEventListener('click', async function() {
            const formData = new FormData();
            formData.append('name', inlineProjectNameInput.value);
            formData.append('tour', inlineTourSelect.value);
            formData.append('collections', inlineCollections.value);
            formData.append('contributors', inlineContributors.value);
            formData.append('units', inlineUnits.value);
            formData.append('image', inlineImageInput.files[0]);

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
                    inlineImageUploadBox.innerHTML = `
                        <input type="file" class="image-input" id="inlineImageInput" accept="image/jpeg, image/png">
                        <span>Click to add image</span>
                        <div class="overlay">Click to replace image</div>
                    `;
                    inlineImageName.textContent = '';

                    // Refresh the page to show updated project
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error:', error);
                alert('An error occurred while saving the project');
            }
        });

     
        $(document).ready(function() {
            $('#inlineTourSelect').select2();
            $('#inlineCollections').select2();
            $('#inlineContributors').select2();
        });

    </script>
@endsection
