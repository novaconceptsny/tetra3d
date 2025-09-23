@extends('layouts.redesign')

@section('content')
<div class="container">
    <!-- Global Search Section -->
    <div class="global-search-section mb-4">
        <div class="row">
            <div class="col-12">
                <div class="card search-card">
                    <div class="card-body">
                        <div class="d-flex align-items-center">
                            <div class="search-container flex-grow-1 me-3">
                                <div class="input-group">
                                    <span class="input-group-text">
                                        <i class="fas fa-search"></i>
                                    </span>
                                    <input type="text"
                                           class="form-control search-input global-search-input"
                                           placeholder="Search all layouts, tours, and projects..."
                                           id="globalSearchInput"
                                           style="min-width: 300px;">
                                    <button class="btn btn-outline-secondary" type="button" id="clearGlobalSearch">
                                        <i class="fas fa-times"></i>
                                    </button>
                                </div>
                            </div>
                            <div class="search-filters">
                                <div class="btn-group" role="group">
                                    <input type="checkbox" class="btn-check" id="searchLayouts" >
                                    <label class="btn btn-outline-primary btn-sm" for="searchLayouts">
                                        <i class="fas fa-cube me-1"></i>Layouts
                                    </label>

                                    <input type="checkbox" class="btn-check" id="searchTours" >
                                    <label class="btn btn-outline-primary btn-sm" for="searchTours">
                                        <i class="fas fa-map me-1"></i>Tours
                                    </label>

                                    <input type="checkbox" class="btn-check" id="searchProjects" >
                                    <label class="btn btn-outline-primary btn-sm" for="searchProjects">
                                        <i class="fas fa-folder me-1"></i>Projects
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="search-results mt-3" id="globalSearchResults" style="display: none;">
                            <div class="row">
                                <div class="col-12">
                                    <h6 class="text-muted mb-2">Search Results</h6>
                                    <div id="searchResultsContainer"></div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Favourites Section - Full Width -->
    <div class="favourites-section-full-width">
        <div class="container">
            <div class="favourites-section">
                <div class="d-flex align-items-center justify-content-between mb-3">
                    <div class="d-flex align-items-center">
                        <h5 class="mb-0">Favourites</h5>
                        @if(auth()->user() && auth()->user()->isSuperAdmin())
                            <button id="toggleFavouritesBtn" class="btn btn-link ms-2" title="Show/Hide Favourites" style="font-size: 1.2rem; color: #099F9A;">
                                <i id="favouritesEyeIcon" class="fas fa-eye"></i>
                            </button>
                        @endif
                    </div>
                    <div class="search-container">
                        <div class="input-group">
                            <span class="input-group-text">
                                <i class="fas fa-search"></i>
                            </span>
                            <input type="text"
                                   class="form-control search-input"
                                   placeholder="Search favorites..."
                                   id="favoritesSearchInput"
                                   style="min-width: 250px;">
                        </div>
                    </div>
                </div>
                <div id="favouritesSection">
                    <div class="favourite-items" id="favoritesContainer">
                        <div class="row">
                            @if($favorites->count() > 0)
                                @foreach($favorites as $favorite)
                                    <div class="col-lg-2 col-md-3 col-sm-4 favourite-card" data-favorite-id="{{ $favorite->id }}">
                                        <div class="bg-light rounded p-2">
                                            <div class="d-flex justify-content-between align-items-start mb-1">
                                                <h4 class="mb-0">
                                                    <i class="fas fa-star text-primary favorite-star"
                                                       onclick="removeFavorite({{ $favorite->id }})"
                                                       title="Remove from favorites"
                                                       style="cursor: pointer;"></i>
                                                    {{ $favorite->name }}
                                                </h4>
                                            </div>
                                            <div class="mb-1">
                                                <small class="text-muted" style="font-size: 11px;">
                                                    <i class="fas fa-folder me-1"></i>
                                                    {{ $favorite->project ? $favorite->project->name : 'No Project' }}
                                                </small>
                                            </div>
                                            <div class="d-flex align-items-center justify-content-between">
                                                <span style="font-size: 12px;">{{ $favorite->assignedTour()->name }}</span>
                                                <a href="{{ route('tours.show', [$favorite->tour_id, 'layout_id' => $favorite->id]) }}" class="btn-enter ms-1" style="font-size: 11px; padding: 2px 8px;">
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
        </div>
    </div>

    <div id="dashboard-section" class="row">
        <div class="col-12">
            <div class="projects-section">
                @if($companies->count() > 0)
                    @foreach($companies as $company)
                        <div class="company-section mb-5">
                            <div class="d-flex justify-content-between align-items-center mb-3">
                                <h5>
                                    @if(str_contains($company->name, 'My Workspace'))
                                        My workspace
                                    @else
                                        {{ $company->name }}
                                    @endif
                                </h5>
                                <div class="d-flex align-items-center gap-3">
                                    <div class="search-container">
                                        <div class="input-group">
                                            <span class="input-group-text">
                                                <i class="fas fa-search"></i>
                                            </span>
                                            <input type="text"
                                                   class="form-control search-input"
                                                   placeholder="Search projects..."
                                                   data-company-id="{{ $company->id }}"
                                                   style="min-width: 250px;">
                                        </div>
                                    </div>
                                    <div class="sort-dropdown">
                                        <select class="form-select">
                                            <option>Recently added</option>
                                            <!-- Add other sort options -->
                                        </select>
                                    </div>
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
                                                    <div class="rounded img-home p-2 d-flex justify-content-center align-items-center" >
                                                        <img src="{{ $project->background_url }}" class="card-img-top img-fluid" alt="{{ $project->title }}">
                                                    </div>
                                                    <div class="card-body">
                                                        <div class="d-flex justify-content-between align-items-start mb-2 flex-grow-1">
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

                                                        <div class="mb-1">
                                                            <div>
                                                                <small>Created: {{ $project->created_at->format('F jS, Y') }}</small><br>
                                                                <div class="d-flex align-items-center justify-content-between">
                                                                    <span style="font-size: 0.875rem;">{{ $project->layouts_count ?? $project->layouts()->count() }} layouts</span>
                                                                    <a href="javascript:void(0)" class="btn-enter ms-2" onclick="handleEnterProject({{ $project->id }}, {{ $project->assignedTours()->count() }})">Enter</a>
                                                                </div>
                                                            </div>
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

            <div class="mb-3 login-custum-form-group">
                <label for="inlineCompanyInput" class="form-label">Company</label>
                <input type="text" class="form-control" id="inlineCompanyInput" placeholder="Company" disabled>
            </div>

            <div class="mb-3 login-custum-form-group">
                <label for="inlineProjectNameInput" class="form-label">Project Name</label>
                <input type="text" class="form-control" id="inlineProjectNameInput" >
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

            <div class="mb-3 col-md-4 d-flex justify-content-between align-items-end w-100">
                <div>
                    <label class="form-label">Thumbnail</label>
                    <div class="image-upload-box mb-2" id="inlineImageUploadBox">
                        <input type="file" class="image-input" id="inlineImageInput" accept="image/jpeg, image/png">
                        <span>Click or drag & drop to add image</span>
                        <div class="overlay">Click to replace image</div>
                    </div>
                    <div class="image-name" id="inlineImageName"></div>
                </div>

                <div class="d-flex justify-content-end gap-3 mb-1">
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn mb-3" id="inlineSaveButton" style="width: 200px; background-color: #099F9A; color: white; border: none;" onclick="handleCreateProject()">Create</button>
                    </div>
                    <div class="d-flex justify-content-center">
                        <button type="button" class="btn mb-3" id="inlineCancelButton" onclick="closeCreateProject()" style="width: 200px; background-color: #E24B4B; color: white; border: none;">Cancel</button>
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
                    <span>+ Image (or drag & drop)</span>
                    <div class="overlay">Click to replace image</div>
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
@endsection

@section('styles')
    <link href="{{ mix('css/page/tour360.css') }}" rel="stylesheet">
    <style>
        /* CroPro Integration Styles */
        .img-preview {
            transition: all 0.3s ease;
            border-radius: 8px;
        }
        
        .img-preview:hover {
            transform: scale(1.02);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15);
        }
        
        .image-upload-box .overlay {
            background: rgba(0, 0, 0, 0.7);
            color: white;
            font-size: 14px;
            font-weight: 500;
            display: flex;
            align-items: center;
            justify-content: center;
            opacity: 0;
            transition: opacity 0.3s ease;
        }
        
        .image-upload-box:hover .overlay {
            opacity: 1;
        }
        
        .overlay-actions {
            display: flex;
            gap: 8px;
        }
        
        .overlay-actions .btn {
            font-size: 12px;
            padding: 6px 12px;
            border-radius: 4px;
            border: none;
            font-weight: 500;
        }
        
        .overlay-actions .btn-primary {
            background: rgba(0, 123, 255, 0.9);
            color: white;
        }
        
        .overlay-actions .btn-secondary {
            background: rgba(108, 117, 125, 0.9);
            color: white;
        }
        
        .overlay-actions .btn:hover {
            transform: translateY(-1px);
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
        }
        
        /* Ensure CroPro modal appears above other elements */
        .cropro-modal {
            z-index: 9999 !important;
        }
        
        /* Style for the image upload box */
        .image-upload-box {
            position: relative;
        }
        
        /* Hide search and filter bars */
        .company-section .search-container,
        .company-section .sort-dropdown,
        .favourites-section .search-container {
            display: none !important;
        }

        .__cropro_  {
            position: fixed !important;
        }

    </style>
@endsection

@section('scripts')
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
    <script src="https://unpkg.com/cropro/cropro.js"></script>

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

        // Add all layouts data for global search
        const allLayouts = @json($allLayouts);
        console.log('All layouts for search:', allLayouts);

        // Add all tours data for global search
        const allTours = @json($allTours);
        console.log('All tours for search:', allTours);

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

        function clearCreateProjectForm() {
            // Clear all form fields
            document.getElementById('inlineProjectNameInput').value = '';
            document.getElementById('inlineCompanyInput').value = '';
            document.getElementById('inlineUnits').value = 'metric';
            
            // Clear Select2 dropdowns
            $('#inlineTourSelect').val(null).trigger('change');
            $('#inlineCollections').val(null).trigger('change');
            $('#inlineContributors').val(null).trigger('change');
            
            // Reset image upload
            document.getElementById('inlineImageInput').value = '';
            document.getElementById('inlineImageName').textContent = '';
            
            // Reset image upload box to original state
            const inlineImageUploadBox = document.getElementById('inlineImageUploadBox');
            inlineImageUploadBox.innerHTML = `
                <input type="file" class="image-input" id="inlineImageInput" accept="image/jpeg, image/png">
                <span>Click or drag & drop to add image</span>
                <div class="overlay">Click to replace image</div>
            `;
            
            // Re-attach event listeners to the new file input
            const newImageInput = inlineImageUploadBox.querySelector('#inlineImageInput');
            newImageInput.addEventListener('change', (event) => {
                const file = event.target.files[0];
                if (file) {
                    handleImageFile(file, inlineImageUploadBox, newImageInput, document.getElementById('inlineImageName'));
                }
            });
            
            // Reset save button text and onclick handler
            const saveButton = document.getElementById('inlineSaveButton');
            saveButton.textContent = 'Create';
            saveButton.onclick = handleCreateProject;
            
            // Reset modal title
            document.querySelector('.create-project-section .modal-title').textContent = 'Add new project';
        }

        async function openCreateProject(companyId) {
            try {
                // Clear form first to ensure fresh start
                clearCreateProjectForm();
                
                // Hide the global search section
                const globalSearchSection = document.querySelector('.global-search-section');
                if (globalSearchSection) {
                    globalSearchSection.style.display = 'none';
                }
                
                // Hide the favourites section
                const favouritesSection = document.querySelector('.favourites-section-full-width');
                if (favouritesSection) {
                    favouritesSection.style.display = 'none';
                }
                
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
            // Clear the form
            clearCreateProjectForm();
            
            // Show the global search section
            const globalSearchSection = document.querySelector('.global-search-section');
            if (globalSearchSection) {
                globalSearchSection.style.display = 'block';
            }
            
            // Show the favourites section
            const favouritesSection = document.querySelector('.favourites-section-full-width');
            if (favouritesSection) {
                favouritesSection.style.display = 'block';
            }
            
            // Hide the form and show dashboard
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
            const unit = document.getElementById('inlineUnits').value;
            const thumbnailFile = document.getElementById('inlineImageInput').files[0];
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
                    // Show the global search section before reloading
                    const globalSearchSection = document.querySelector('.global-search-section');
                    if (globalSearchSection) {
                        globalSearchSection.style.display = 'block';
                    }
                    // Show the favourites section before reloading
                    const favouritesSection = document.querySelector('.favourites-section-full-width');
                    if (favouritesSection) {
                        favouritesSection.style.display = 'block';
                    }
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
                // Hide the global search section
                const globalSearchSection = document.querySelector('.global-search-section');
                if (globalSearchSection) {
                    globalSearchSection.style.display = 'none';
                }
                
                // Hide the favourites section
                const favouritesSection = document.querySelector('.favourites-section-full-width');
                if (favouritesSection) {
                    favouritesSection.style.display = 'none';
                }
                
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

                // If there's an existing thumbnail, show it
                if (data.project.background_url) {
                    const img = document.createElement('img');
                    img.src = data.project.background_url;
                    img.className = 'img-preview';
                    img.style.cursor = 'pointer';
                    img.title = 'Click to edit/crop image';
                    img.crossOrigin = 'anonymous';
                    
                    // Wait for image to load to get dimensions
                    img.onload = () => {
                        inlineImageUploadBox.innerHTML = '';
                        inlineImageUploadBox.style.backgroundColor = 'grey';
                        inlineImageUploadBox.appendChild(img);
                        
                        // Create crop preview for existing image
                        createCropPreview(inlineImageUploadBox, img, inlineImageInput, null, inlineImageName);
                    };
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
                    // Show the global search section before reloading
                    const globalSearchSection = document.querySelector('.global-search-section');
                    if (globalSearchSection) {
                        globalSearchSection.style.display = 'block';
                    }
                    // Show the favourites section before reloading
                    const favouritesSection = document.querySelector('.favourites-section-full-width');
                    if (favouritesSection) {
                        favouritesSection.style.display = 'block';
                    }
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

        
        // Handle inline image upload with drag and drop
        inlineImageUploadBox.addEventListener('click', (e) => {
            // Check if there's already an image displayed
            const existingImg = inlineImageUploadBox.querySelector('.img-preview');
            if (existingImg) {
                // If there's an existing image, don't trigger file input
                e.stopPropagation();
                return;
            }
            // Only trigger file input if no image is present
            inlineImageInput.click();
        });

        // Drag and drop functionality for inline image upload
        inlineImageUploadBox.addEventListener('dragover', (e) => {
            e.preventDefault();
            inlineImageUploadBox.classList.add('drag-over');
        });

        inlineImageUploadBox.addEventListener('dragleave', (e) => {
            e.preventDefault();
            inlineImageUploadBox.classList.remove('drag-over');
        });

        inlineImageUploadBox.addEventListener('drop', (e) => {
            e.preventDefault();
            inlineImageUploadBox.classList.remove('drag-over');
            
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type.startsWith('image/')) {
                    handleImageFile(file, inlineImageUploadBox, inlineImageInput, inlineImageName);
                } else {
                    alert('Please drop an image file (JPEG or PNG)');
                }
            }
        });

        inlineImageInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                handleImageFile(file, inlineImageUploadBox, inlineImageInput, inlineImageName);
            }
        });

        // Function to handle image file processing
        function handleImageFile(file, uploadBox, inputElement, nameElement) {
            const reader = new FileReader();
            reader.onload = (e) => {
                const img = document.createElement('img');
                img.src = e.target.result;
                img.className = 'img-preview';
                img.style.cursor = 'pointer';
                img.title = 'Click to edit/crop image';
                img.crossOrigin = 'anonymous';
                
                // Wait for image to load to get dimensions
                img.onload = () => {
                    uploadBox.innerHTML = '';
                    uploadBox.style.backgroundColor = 'grey';
                    uploadBox.appendChild(img);
                    
                    // Create crop preview overlay immediately
                    createCropPreview(uploadBox, img, inputElement, file, nameElement);
                };
            };
            reader.readAsDataURL(file);
        }

        // Function to create crop preview overlay
        function createCropPreview(uploadBox, img, inputElement, file, nameElement) {
            // Create the crop overlay
            const cropOverlay = document.createElement('div');
            cropOverlay.className = 'crop-overlay';
            
            // Create the crop area rectangle
            const cropArea = document.createElement('div');
            cropArea.className = 'crop-area-rectangle';
            
            // Calculate crop area dimensions based on the actual image size
            const uploadBoxRect = uploadBox.getBoundingClientRect();
            const imgRect = img.getBoundingClientRect();
            
            // Get the actual displayed image dimensions
            const cropWidth = imgRect.width;
            const cropHeight = cropWidth / 2; // 2:1 aspect ratio
            
            cropArea.style.width = cropWidth + 'px';
            cropArea.style.height = cropHeight + 'px';
            
            // Create a copy of the image for the crop area
            const cropImg = document.createElement('img');
            cropImg.src = img.src;
            cropImg.style.width = '100%';
            cropImg.style.height = '100%';
            cropImg.style.objectFit = 'cover';
            
            cropArea.appendChild(cropImg);
            cropOverlay.appendChild(cropArea);
            
            // Create action overlay
            const overlay = document.createElement('div');
            overlay.className = 'overlay';
            
            // Different button set based on whether we have a file or not
            const buttonHtml = `
                <div class="overlay-actions">
                    <button type="button" class="btn btn-sm btn-primary me-2 edit-btn" style ="background-color: #099F9A !important; color: white; ">
                        <i class="fas fa-crop"></i> Edit
                    </button>
                    <button type="button" class="btn btn-sm btn-secondary replace-btn">
                        <i class="fas fa-upload"></i> Replace
                    </button>
                </div>
            ` ;
            
            overlay.innerHTML = buttonHtml;
            
            // Add event listeners to the buttons
            const editBtn = overlay.querySelector('.edit-btn');
            const replaceBtn = overlay.querySelector('.replace-btn');
            
            editBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                if (file) {
                    showCropArea(img, inputElement, file);
                } else {
                    // For existing images, create a temporary file
                    const tempFile = new File([''], 'existing-image.jpg', { type: 'image/jpeg' });
                    showCropArea(img, inputElement, tempFile);
                }
            });
            
            
            replaceBtn.addEventListener('click', (e) => {
                e.stopPropagation();
                inputElement.click();
            });
            
            uploadBox.appendChild(cropOverlay);
            uploadBox.appendChild(overlay);
            uploadBox.appendChild(inputElement);
            
            if (nameElement && file) {
                nameElement.textContent = file.name;
            }
            
            // Add resize observer to recalculate crop area if image size changes
            if (window.ResizeObserver) {
                const resizeObserver = new ResizeObserver(() => {
                    // Recalculate crop area dimensions
                    const newUploadBoxRect = uploadBox.getBoundingClientRect();
                    const newImgRect = img.getBoundingClientRect();
                    
                    const newCropWidth = newImgRect.width;

                    const newCropHeight = newCropWidth / 2;
                    
                    cropArea.style.width = newCropWidth + 'px';
                    cropArea.style.height = newCropHeight + 'px';
                });
                
                resizeObserver.observe(uploadBox);
                resizeObserver.observe(img);
            }
        }


          // Function to show CroPro editing interface
          function showCropArea(targetImage, inputElement, originalFile) {
            // Create a new CropArea instance for the target image
            const cropArea = new cropro.CropArea(targetImage);

            cropArea.displayMode = 'popup';

            cropArea.zoomToCropEnabled = false;
            cropArea.aspectRatios = [
            { horizontal: 2, vertical: 1 }, 
            ];
            // Add an event listener to handle the cropped image data
            cropArea.addRenderEventListener((croppedImageDataUrl) => {
                // Update the source of the target image with the cropped image
                targetImage.src = croppedImageDataUrl;
                
                // Convert data URL back to file and update the input element
                dataURLtoFile(croppedImageDataUrl, originalFile.name).then(croppedFile => {
                    // Create a new FileList-like object
                    const dataTransfer = new DataTransfer();
                    dataTransfer.items.add(croppedFile);
                    inputElement.files = dataTransfer.files;
                });
            });

            // Display the cropping interface
            cropArea.show();
        }

        // Helper function to convert data URL to File object
        function dataURLtoFile(dataurl, filename) {
            return new Promise((resolve) => {
                const arr = dataurl.split(',');
                const mime = arr[0].match(/:(.*?);/)[1];
                const bstr = atob(arr[1]);
                let n = bstr.length;
                const u8arr = new Uint8Array(n);
                while(n--){
                    u8arr[n] = bstr.charCodeAt(n);
                }
                resolve(new File([u8arr], filename, {type:mime}));
            });
        }

        // Handle modal image upload with drag and drop
        if (imageUploadBox) {
            imageUploadBox.addEventListener('click', (e) => {
                // Check if there's already an image displayed
                const existingImg = imageUploadBox.querySelector('.img-preview');
                if (existingImg) {
                    // If there's an existing image, don't trigger file input
                    e.stopPropagation();
                    return;
                }
                // Only trigger file input if no image is present
                imageInput.click();
            });

            // Drag and drop functionality for modal image upload
            imageUploadBox.addEventListener('dragover', (e) => {
                e.preventDefault();
                imageUploadBox.classList.add('drag-over');
            });

            imageUploadBox.addEventListener('dragleave', (e) => {
                e.preventDefault();
                imageUploadBox.classList.remove('drag-over');
            });

            imageUploadBox.addEventListener('drop', (e) => {
                e.preventDefault();
                imageUploadBox.classList.remove('drag-over');
                
                const files = e.dataTransfer.files;
                if (files.length > 0) {
                    const file = files[0];
                    if (file.type.startsWith('image/')) {
                        handleImageFile(file, imageUploadBox, imageInput, imageName);
                    } else {
                        alert('Please drop an image file (JPEG or PNG)');
                    }
                }
            });

            imageInput.addEventListener('change', (event) => {
                const file = event.target.files[0];
                if (file) {
                    handleImageFile(file, imageUploadBox, imageInput, imageName);
                }
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
                const projectName = favoriteCard.querySelector('small.text-muted').textContent.trim();
                const tourName = favoriteCard.querySelector('span').textContent.trim();

                // Populate the modal with favorite information
                document.getElementById('favoriteItemInfo').innerHTML = `
                    <div class="d-flex align-items-center">
                        <i class="fas fa-star text-primary me-3"></i>
                        <div class="text-start">
                            <strong>${favoriteName}</strong><br>
                            <small class="text-muted">${projectName}</small><br>
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
                    <div class="col-lg-2 col-md-3 col-sm-4 favourite-card" data-favorite-id="${favorite.id}">
                        <div class="bg-light rounded p-2">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <h4 class="mb-0">
                                    <i class="fas fa-star text-primary favorite-star"
                                       onclick="removeFavorite(${favorite.id})"
                                       title="Remove from favorites"
                                       style="cursor: pointer;"></i>
                                    ${favorite.name}
                                </h4>
                            </div>
                            <div class="mb-1">
                                <small class="text-muted" style="font-size: 11px;">
                                    <i class="fas fa-folder me-1"></i>
                                    ${favorite.project ? favorite.project.name : 'No Project'}
                                </small>
                            </div>
                            <div class="d-flex align-items-center justify-content-between">
                                <span style="font-size: 12px;">${favorite.tour ? favorite.tour.name : 'No Tour Assigned'}</span>
                                <a href="/tours/${favorite.tour_id}?layout_id=${favorite.id}" class="btn-enter ms-1" style="font-size: 11px; padding: 2px 8px;">
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
            
            // Add event listener for the close button (X) in the form header
            document.querySelector('.close-create-section').addEventListener('click', function() {
                closeCreateProject();
            });
        });


        // Search functionality for project cards and favorites
        document.addEventListener('DOMContentLoaded', function() {
            const searchInputs = document.querySelectorAll('.search-input');

            // Global search functionality
            const globalSearchInput = document.getElementById('globalSearchInput');
            const globalSearchResults = document.getElementById('globalSearchResults');
            const searchResultsContainer = document.getElementById('searchResultsContainer');
            const clearGlobalSearchBtn = document.getElementById('clearGlobalSearch');

            // Search filters
            const searchLayoutsCheckbox = document.getElementById('searchLayouts');
            const searchToursCheckbox = document.getElementById('searchTours');
            const searchProjectsCheckbox = document.getElementById('searchProjects');

            let searchTimeout;

            // Global search function
            function performGlobalSearch(searchTerm) {
                if (!searchTerm.trim()) {
                    globalSearchResults.style.display = 'none';
                    return;
                }

                const results = [];
                const term = searchTerm.toLowerCase().trim();

                // Search in favorites (layouts)
                if (searchLayoutsCheckbox.checked) {
                    // Search through all layouts from server data
                    allLayouts.forEach(layout => {
                        const layoutName = layout.name || '';
                        const projectName = layout.project ? layout.project.name : '';
                        // Get tour name - we need to fetch it separately since it's not loaded
                        const tourName = layout.tour ? layout.tour.name : '';
                        const userName = layout.user ? `${layout.user.first_name} ${layout.user.last_name}` : '';

                        if (layoutName.toLowerCase().includes(term) ||
                            projectName.toLowerCase().includes(term) ||
                            tourName.toLowerCase().includes(term) ||
                            userName.toLowerCase().includes(term)) {

                            // Check if this layout is in favorites (for star icon)
                            const isFavorite = layout.is_favorite;
                            const starIcon = isFavorite ? 'fas fa-star text-primary' : 'far fa-star text-muted';

                            results.push({
                                type: 'layout',
                                id: layout.id,
                                title: layoutName,
                                subtitle: `${projectName} • ${tourName} • ${userName}`,
                                action: `window.location.href='/tours/${layout.tour_id}?layout_id=${layout.id}'`,
                                element: null, // Not in DOM, so null
                                isFavorite: isFavorite,
                                starIcon: starIcon
                            });
                        }
                    });
                }

                // Search in projects
                if (searchProjectsCheckbox.checked) {
                    const projectCards = document.querySelectorAll('.layout-item .card[data-project-id]');
                    projectCards.forEach(card => {
                        const projectName = card.querySelector('.card-text span')?.textContent || '';
                        const projectData = card.getAttribute('data-project-name') || '';
                        const projectUnits = card.getAttribute('data-project-units') || '';
                        const projectId = card.getAttribute('data-project-id');

                        if (projectName.toLowerCase().includes(term) ||
                            projectData.toLowerCase().includes(term) ||
                            projectUnits.toLowerCase().includes(term)) {

                            const enterBtn = card.querySelector('.btn-enter');
                            const tourCount = card.querySelector('.project-stats span:first-child')?.textContent || '';

                            results.push({
                                type: 'project',
                                id: projectId,
                                title: projectName,
                                subtitle: `${tourCount} • Created: ${card.querySelector('small')?.textContent || ''}`,
                                action: enterBtn ? `handleEnterProject(${projectId}, ${tourCount.match(/\d+/)?.[0] || 0})` : '',
                                element: card
                            });
                        }
                    });
                }

                // Search in tours (if available in the DOM)
                if (searchToursCheckbox.checked) {
                    // Search through all tours from server data
                    allTours.forEach(tour => {
                        const tourName = tour.name || '';
                        const companyName = tour.company ? tour.company.name : '';
                        const tourDescription = tour.description || '';

                        if (tourName.toLowerCase().includes(term) ||
                            companyName.toLowerCase().includes(term) ||
                            tourDescription.toLowerCase().includes(term)) {

                            results.push({
                                type: 'tour',
                                id: tour.id,
                                title: tourName,
                                subtitle: `${companyName} • ${tourDescription ? tourDescription.substring(0, 50) + '...' : 'No description'}`,
                                action: `window.location.href='/tours/${tour.id}'`,
                                element: null, // Not in DOM, so null
                                companyName: companyName
                            });
                        }
                    });
                }

                // Display results
                displaySearchResults(results, searchTerm);
            }

            // Display search results
            function displaySearchResults(results, searchTerm) {
                if (results.length === 0) {
                    searchResultsContainer.innerHTML = `
                        <div class="no-search-results">
                            <i class="fas fa-search"></i>
                            <p>No results found for "${searchTerm}"</p>
                            <small>Try adjusting your search terms or filters</small>
                        </div>
                    `;
                } else {
                    const resultsHtml = results.map(result => {
                        const highlightedTitle = highlightSearchTerm(result.title, searchTerm);
                        const highlightedSubtitle = highlightSearchTerm(result.subtitle, searchTerm);

                        // Handle different action types
                        let actionButton = '';
                        if (result.type === 'layout') {
                            // For layouts, show star icon and proper action
                            actionButton = `
                                <div class="search-result-action">
                                    <button class="btn btn-outline-primary btn-sm me-2" onclick="toggleLayoutFavorite(${result.id}, this)">
                                        <i class="${result.starIcon}"></i>
                                    </button>
                                    <button class="btn btn-primary btn-sm" onclick="${result.action}">
                                        <i class="fas fa-external-link-alt me-1"></i>Open
                                    </button>
                                </div>
                            `;
                        } else {
                            // For other types, use the original action
                            actionButton = `
                                <div class="search-result-action">
                                    <button class="btn btn-primary btn-sm" onclick="${result.action}">
                                        <i class="fas fa-external-link-alt me-1"></i>Open
                                    </button>
                                </div>
                            `;
                        }

                        return `
                            <div class="search-result-item" data-type="${result.type}" data-id="${result.id}">
                                <div class="search-result-icon ${result.type}">
                                    <i class="fas ${getIconForType(result.type)}"></i>
                                </div>
                                <div class="search-result-content">
                                    <div class="search-result-title">${highlightedTitle}</div>
                                    <div class="search-result-subtitle">${highlightedSubtitle}</div>
                                </div>
                                ${actionButton}
                            </div>
                        `;
                    }).join('');

                    searchResultsContainer.innerHTML = resultsHtml;
                }

                globalSearchResults.style.display = 'block';
            }

            // Highlight search terms
            function highlightSearchTerm(text, searchTerm) {
                if (!searchTerm) return text;
                const regex = new RegExp(`(${searchTerm})`, 'gi');
                return text.replace(regex, '<span class="search-highlight">$1</span>');
            }

            // Get icon for result type
            function getIconForType(type) {
                switch (type) {
                    case 'layout': return 'fa-cube';
                    case 'tour': return 'fa-map';
                    case 'project': return 'fa-folder';
                    default: return 'fa-file';
                }
            }

            // Global search event listeners
            globalSearchInput.addEventListener('input', function() {
                clearTimeout(searchTimeout);
                const searchTerm = this.value;

                searchTimeout = setTimeout(() => {
                    performGlobalSearch(searchTerm);
                }, 300);
            });

            // Clear global search
            clearGlobalSearchBtn.addEventListener('click', function() {
                globalSearchInput.value = '';
                globalSearchResults.style.display = 'none';
                globalSearchInput.focus();
            });

            // Search filter change events
            [searchLayoutsCheckbox, searchToursCheckbox, searchProjectsCheckbox].forEach(checkbox => {
                checkbox.addEventListener('change', function() {
                    if (globalSearchInput.value.trim()) {
                        performGlobalSearch(globalSearchInput.value);
                    }
                });
            });

            // Global keyboard shortcuts
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + K to focus global search
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    globalSearchInput.focus();
                }

                // Escape to clear global search
                if (e.key === 'Escape' && document.activeElement === globalSearchInput) {
                    globalSearchInput.value = '';
                    globalSearchResults.style.display = 'none';
                }
            });

            searchInputs.forEach(input => {
                input.addEventListener('input', function() {
                    const searchTerm = this.value.toLowerCase().trim();

                    // Handle favorites search
                    if (this.id === 'favoritesSearchInput') {
                        const favoritesSection = this.closest('.favourites-section');
                        const favoriteCards = favoritesSection.querySelectorAll('.favourite-card');

                        favoriteCards.forEach(card => {
                            const favoriteName = card.querySelector('h4')?.textContent.toLowerCase() || '';
                            const projectName = card.querySelector('small.text-muted')?.textContent.toLowerCase() || '';
                            const tourName = card.querySelector('span')?.textContent.toLowerCase() || '';

                            // Check if search term matches favorite name, project name, or tour name
                            const matches = favoriteName.includes(searchTerm) ||
                                          projectName.includes(searchTerm) ||
                                          tourName.includes(searchTerm);

                            if (matches || searchTerm === '') {
                                card.style.display = '';
                                card.style.opacity = '1';
                            } else {
                                card.style.display = 'none';
                                card.style.opacity = '0';
                            }
                        });

                        // Show/hide "no results" message for favorites
                        const visibleFavorites = favoritesSection.querySelectorAll('.favourite-card:not([style*="display: none"])');
                        let noResultsMsg = favoritesSection.querySelector('.no-results-message');

                        if (searchTerm !== '' && visibleFavorites.length === 0) {
                            if (!noResultsMsg) {
                                noResultsMsg = document.createElement('div');
                                noResultsMsg.className = 'no-results-message col-12 text-center mt-3';
                                noResultsMsg.innerHTML = `
                                    <div class="alert alert-info">
                                        <i class="fas fa-search me-2"></i>
                                        No favorites found matching "${searchTerm}"
                                    </div>
                                `;
                                favoritesSection.querySelector('.favourite-items .row').appendChild(noResultsMsg);
                            }
                        } else if (noResultsMsg) {
                            noResultsMsg.remove();
                        }
                    } else if (this.classList.contains('global-search-input')) {
                        // Global search is handled separately
                        return;
                    } else {
                        // Handle project cards search
                        const companyId = this.getAttribute('data-company-id');
                        const companySection = this.closest('.company-section');
                        const projectCards = companySection.querySelectorAll('.layout-item');

                        projectCards.forEach(card => {
                            const projectName = card.querySelector('.card-text span')?.textContent.toLowerCase() || '';
                            const projectData = card.querySelector('.card');

                            if (projectData) {
                                const projectNameData = projectData.getAttribute('data-project-name')?.toLowerCase() || '';
                                const projectUnits = projectData.getAttribute('data-project-units')?.toLowerCase() || '';

                                // Check if search term matches project name, units, or any other relevant data
                                const matches = projectName.includes(searchTerm) ||
                                              projectNameData.includes(searchTerm) ||
                                              projectUnits.includes(searchTerm);

                                if (matches || searchTerm === '') {
                                    card.style.display = '';
                                    card.style.opacity = '1';
                                } else {
                                    card.style.display = 'none';
                                    card.style.opacity = '0';
                                }
                            }
                        });

                        // Show/hide "no results" message for projects
                        const visibleCards = companySection.querySelectorAll('.layout-item:not([style*="display: none"])');
                        let noResultsMsg = companySection.querySelector('.no-results-message');

                        if (searchTerm !== '' && visibleCards.length <= 1) { // 1 because "Create New Project" card is always visible
                            if (!noResultsMsg) {
                                noResultsMsg = document.createElement('div');
                                noResultsMsg.className = 'no-results-message col-12 text-center mt-3';
                                noResultsMsg.innerHTML = `
                                    <div class="alert alert-info">
                                        <i class="fas fa-search me-2"></i>
                                        No projects found matching "${searchTerm}"
                                    </div>
                                `;
                                companySection.querySelector('.layout-section .row').appendChild(noResultsMsg);
                            }
                        } else if (noResultsMsg) {
                            noResultsMsg.remove();
                        }
                    }
                });

                // Clear search when input is cleared
                input.addEventListener('keyup', function(e) {
                    if (e.key === 'Escape') {
                        this.value = '';
                        this.dispatchEvent(new Event('input'));
                    }
                });

                // Add focus effects
                input.addEventListener('focus', function() {
                    this.parentElement.classList.add('focused');
                });

                input.addEventListener('blur', function() {
                    this.parentElement.classList.remove('focused');
                });

                // Add keyboard shortcuts
                input.addEventListener('keydown', function(e) {
                    // Ctrl/Cmd + F to focus search
                    if ((e.ctrlKey || e.metaKey) && e.key === 'f') {
                        e.preventDefault();
                        this.focus();
                    }

                    // Enter to clear search if it has content
                    if (e.key === 'Enter' && this.value.trim() !== '') {
                        e.preventDefault();
                        this.value = '';
                        this.dispatchEvent(new Event('input'));
                    }
                });
            });

            // Add global keyboard shortcut for search
            document.addEventListener('keydown', function(e) {
                // Ctrl/Cmd + Shift + F to focus the first search input
                if ((e.ctrlKey || e.metaKey) && e.shiftKey && e.key === 'F') {
                    e.preventDefault();
                    const firstSearchInput = document.querySelector('.search-input:not(.global-search-input)');
                    if (firstSearchInput) {
                        firstSearchInput.focus();
                    }
                }
            });
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

        // Function to toggle layout favorite status from search results
        function toggleLayoutFavorite(layoutId, buttonElement) {
            fetch(`/tour360/toggle-favorite/${layoutId}`, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    // Update the star icon in the search result
                    const starIcon = buttonElement.querySelector('i');
                    if (data.is_favorite) {
                        starIcon.className = 'fas fa-star text-primary';
                    } else {
                        starIcon.className = 'far fa-star text-muted';
                    }

                    // Update the layout data in allLayouts array
                    const layoutIndex = allLayouts.findIndex(layout => layout.id === layoutId);
                    if (layoutIndex !== -1) {
                        allLayouts[layoutIndex].is_favorite = data.is_favorite;
                    }

                    // Show feedback
                    console.log(data.message);
                } else {
                    console.error('Failed to toggle favorite:', data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
            });
        }
    </script>
@endsection
