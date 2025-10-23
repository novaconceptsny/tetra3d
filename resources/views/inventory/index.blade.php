@extends('layouts.redesign')

@section('content')
    <section class="collection">
        <div class="main-intro container-fluid artworks-table">
            <div class="mt-5">
                <div class="bg-light-page">
                    <div id="show-collections-container" style="display: block;">
                        <div class="d-flex">
                            <!-- Sidebar -->
                            <div class="collections-sidebar" style="width: 250px; min-width: 220px; background: #f8f9fa; border-radius: 12px; margin-right: 24px;">
                                <div class="card shadow-sm border-0 rounded-4 p-3" style="background: #fff;">
                                    @if(auth()->user()->isSuperAdmin())
                                    <!-- Company Filter for Super Admin -->
                                    <div class="mb-3">
                                        <h6 class="mb-2" style="font-weight: 500; color: #495057;">Company</h6>
                                        <select id="companyFilter" class="form-select form-select-sm" onchange="filterCollectionsByCompany()" style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 12px;">
                                            <option value="">All</option>
                                            @foreach($companies as $company)
                                                <option value="{{ $company->id }}">{{ $company->name }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    @endif
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h6 class="mb-2" style="font-weight: 500; color: #495057;">Collections</h6>
                                        <button class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#addCollectionModal" onclick="handleOpenCollectionModal()" style="width: 36px; height: 36px; border-radius: 50%; background: #099F9A; border: none; display: flex; align-items: center; justify-content: center; box-shadow: 0 2px 4px rgba(9, 159, 154, 0.2); transition: all 0.3s ease;">
                                            <i class="fas fa-plus text-white" style="font-size: 16px; font-weight: 500;"></i>
                                        </button>
                                    </div>
                                    <div class="collections-dropdown">
                                        <div class="dropdown w-100">
                                            @php
                                                $totalItems = $collections->sum(function($collection) {
                                                    return $collection->artworks()->count();
                                                });
                                            @endphp
                                            <button class="btn btn-light w-100 d-flex align-items-center justify-content-between p-3 border rounded" type="button" data-bs-toggle="dropdown" aria-expanded="false" style="background: #fff; border: 1px solid #dee2e6; min-height: 60px; box-shadow: none;">
                                                <div class="d-flex align-items-center">
                                                    @php
                                                        $selectedCollectionData = $selectedCollection ? $collections->firstWhere('id', $selectedCollection) : null;
                                                    @endphp

                                                    @if($selectedCollectionData)
                                                        @if($selectedCollectionData->thumbnail_url)
                                                            <img src="{{ $selectedCollectionData->thumbnail_url }}" alt="{{ $selectedCollectionData->name }}" class="me-3" style="width: 18px; height: 18px; object-fit: cover; border-radius: 0;">
                                                        @else
                                                            <i class="fas fa-image me-3" style="color: #6c757d; font-size: 18px;"></i>
                                                        @endif
                                                        <div class="text-start">
                                                            <div  style="font-weight: 500; color: #495057;">{{ $selectedCollectionData->name }}</div>
                                                            <small class="text-muted">{{ $selectedCollectionData->artworks()->count() }} items</small>
                                                        </div>
                                                    @else
                                                        <i class="fas fa-image me-3" style="color: #6c757d; font-size: 18px;"></i>
                                                        <div class="text-start">
                                                            <div  style="font-weight: 500; color: #495057; font-size: 14px;">All Collections</div>
                                                            <small class="text-muted">{{ $totalItems }} items</small>
                                                        </div>
                                                    @endif
                                                </div>
                                                <i class="fas fa-chevron-down text-muted"></i>
                                            </button>
                                            <ul id="collectionsDropdownMenu" class="dropdown-menu w-100" style="max-height: 500px; overflow-y: auto; min-height: 200px; border: 1px solid #dee2e6; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); color: #6c757d; font-size: 14px; font-weight: 500;">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center p-2" href="#" onclick="selectCollection('', 'All Collections', '{{ $totalItems }}')" style="border-bottom: 1px solid #f8f9fa;">
                                                        <i class="fas fa-image me-3" ></i>
                                                        <div>
                                                            <div  style="font-weight: 500; color: #495057;">All Collections</div>
                                                            <small class="text-muted">{{ $totalItems }} items</small>
                                                        </div>
                                                    </a>
                                                </li>
                                                @foreach($collections as $collection)
                                                <li data-company-id="{{ $collection->company_id }}">
                                                    <a class="dropdown-item d-flex align-items-center p-2" href="#" onclick="selectCollection('{{$collection->id}}', '{{$collection->name}}', '{{$collection->artworks()->count()}}', '{{$collection->thumbnail_url}}')" style="border-bottom: 1px solid #f8f9fa;">
                                                        @if($collection->thumbnail_url)
                                                            <img src="{{ $collection->thumbnail_url }}" alt="" width="24" height="24" class="me-3" style="object-fit: cover; border-radius: 0;">
                                                        @else
                                                            <i class="fas fa-image me-3" ></i>
                                                        @endif
                                                        <div>
                                                            <div  style="font-weight: 500; color: #495057;">{{ $collection->name }}</div>
                                                            <small class="text-muted">{{ $collection->artworks()->count() }} items</small>
                                                        </div>
                                                    </a>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    <div class="mt-2 d-flex justify-content-center gap-2" >
                                        <button class="btn btn-outline-secondary btn-sm" onclick="editCollection()" title="Edit Collection" id="editCollectionBtn" style="display: none;" >
                                            <i class="fas fa-edit" ></i>
                                        </button>
                                        <button class="btn btn-outline-danger btn-sm" onclick="deleteCollection()" title="Delete Collection" id="deleteCollectionBtn" style="display: none;" >
                                            <i class="fas fa-trash" ></i>
                                        </button>
                                    </div>
                                </div>
                            </div>
                            <!-- Main Content -->
                            <div class="flex-grow-1">
                                <div class="card shadow-sm border-0 rounded-4 p-3" style="background: #fff;">

                                    <div class="card-body py-0">
                                        <!-- Search Section -->
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <div class="dataTables_filter">
                                                    <div class="search-container d-flex align-items-center">
                                                        <input type="search" id="tableSearch" class="form-control form-control-sm me-2" placeholder="Search..." style="flex: 1;">
                                                        <!-- <div class="search-toggle-buttons">
                                                            <button type="button" class="btn btn-sm search-toggle-btn active" data-type="artwork" id="artworkToggle">
                                                                Artwork
                                                            </button>
                                                            <button type="button" class="btn btn-sm search-toggle-btn" data-type="collections" id="collectionsToggle">
                                                                Collections
                                                            </button>
                                                        </div> -->
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                        <!-- Add New Artwork Button - Top Left Position -->
                                        <div class="d-flex align-items-center mb-3 gap-3">
                                            <div class="dropdown" style="display: flex; align-items: center; gap: 8px;">
                                                <label for="tableLength" class="form-label mb-0" style="font-size: 14px; color: #495057; font-weight: 500;">Show entries:</label>
                                                <select id="tableLength" class="form-select form-select-sm" style="width: auto; min-width: 70px; border: 1px solid #dee2e6; border-radius: 6px; padding: 6px 12px; font-size: 14px; background: #ffffff;">
                                                    <option value="10">10</option>
                                                    <option value="25" selected>25</option>
                                                    <option value="50">50</option>
                                                    <option value="100">100</option>
                                                </select>
                                            </div>
                                            <div class="d-flex align-items-center">
                                                <!-- Main Artwork Button -->
                                                <button class="btn d-flex align-items-center gap-2" type="button" id="addArtworkBtn" style="background: #f8f9fa; border: 1px solid #dee2e6; color: #495057; border-radius: 8px 0 0 8px; padding: 4px 16px; font-weight: 500; font-size: 14px; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                                                    <i class="fas fa-plus" style="color: #495057;"></i>
                                                    <span>Artwork</span>
                                                </button>

                                                <!-- Dropdown Arrow Button -->
                                                <div class="dropdown">
                                                    <button class="btn dropdown-toggle" type="button" id="artworkDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" style="background: #ffffff; border: 1px solid #dee2e6; color: #495057; border-radius: 0 8px 8px 0; padding: 4px 12px; font-weight: 500; border-left: none; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="artworkDropdownBtn" style="border-radius: 8px; border: 1px solid #dee2e6; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); min-width: 160px; font-size: 14px;">
                                                        <li><a class="dropdown-item" href="#" id="addMultipleArtworksBtn" style="padding: 4px 16px; color: #495057; text-decoration: none; display: flex; align-items: center; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                                                            <i class="fas fa-layer-group me-2" style="color: #6c757d; "></i>Add multiple
                                                        </a></li>
                                                    </ul>
                                                </div>
                                            </div>

                                                                                    <!-- Bulk Edit Controls -->
                                            <div id="bulkEditControls" style="display: none;">
                                                <div class="d-flex align-items-center gap-3">
                                                    <button id="bulkEditBtn" class="btn btn-primary icon-button"
                                                            data-bs-toggle="tooltip"
                                                            data-bs-placement="top"
                                                            title="Edit Selected Items">
                                                        <i class="fas fa-edit"></i>
                                                    </button>
                                                    <span id="selectedCount" class="text-muted">0 items selected</span>
                                                </div>
                                            </div>

                                                                                    <!-- Save All and Cancel All buttons for new items -->
                                            <div id="bulkNewItemControls"  style="display: none;">
                                                <div class="d-flex align-items-center gap-3">
                                                    <button id="saveAllNewItemsBtn" class="btn btn-success" style="background: #099F9A; border: none; border-radius: 6px; padding: 4px 16px; font-weight: 500;">
                                                        <i class="fas fa-save me-2"></i>Save All
                                                    </button>
                                                    <button id="cancelAllNewItemsBtn" class="btn btn-danger" style="background: #dc3545; border: none; border-radius: 6px; padding: 4px 16px; font-weight: 500;">
                                                        <i class="fas fa-times me-2"></i>Cancel All
                                                    </button>
                                                    <span id="newItemsCount" class="text-muted">0 new items</span>
                                                </div>
                                            </div>

                                            <!-- Right Side Action Buttons -->
                                            <div class="d-flex align-items-center gap-2 ms-auto">

                                                                                                                                        <!-- Download Button -->
                                                <button class="btn icon-button" type="button" id="downloadTableBtn"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Download Table Data">
                                                    <i class="fas fa-download"></i>
                                                </button>
                                                <!-- Copy/Duplicate Button -->
                                                <button class="btn icon-button" type="button" id="duplicateArtworkBtn"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Duplicate Selected Items">
                                                    <i class="fas fa-copy"></i>
                                                </button>

                                                <!-- Sync/Transfer Button -->
                                                <button class="btn icon-button" type="button" id="moveToCollection"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Move to Collection">
                                                    <i class="fas fa-exchange-alt"></i>
                                                </button>

                                                <!-- Upload Multiple Button -->
                                                <button class="btn icon-button" type="button" id="uploadMultipleBtn"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Upload Multiple Items"
                                                        onclick="handleOpenUploadArtworks()"> >
                                                    <i class="fas fa-upload" style="color: #495057;"></i>
                                                </button>

                                                <!-- Delete Button -->
                                                <button class="btn icon-button" type="button" id="deleteArtworkBtn"
                                                        data-bs-toggle="tooltip"
                                                        data-bs-placement="top"
                                                        title="Delete Selected Items"
                                                        style="background: #dc3545; color: #fff;">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </div>
                                        </div>


                                        <div class="table-responsive">
                                            <table id="inventoryTable" class="table table-striped table-bordered" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                                        <th>Image</th>
                                                        @if(auth()->user()->isSuperAdmin())
                                                        <th>Company</th>
                                                        @endif
                                                        <th>Collection</th>
                                                        <th>Title</th>
                                                        <th>Artist</th>
                                                        <th>Type</th>
                                                        <th>Height</th>
                                                        <th>Width</th>
                                                        <th>Unit</th>
                                                        <th>Description</th>
                                                    </tr>
                                                </thead>
                                                <tbody>
                                                    <!-- Data will be loaded via AJAX -->
                                                </tbody>
                                            </table>
                                        </div>

                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>

                    <div id="upload-artwork-container" style="display: none;">
                        <div class="card shadow-sm border-0 rounded-4 p-3" style="background: #fff;">
                            <div class="d-flex justify-content-start">
                                <button class="btn btn-outline-secondary mb-3 d-flex align-items-center" onclick="backToCollections()" style="width: fit-content; background: transparent;">
                                    <i class="fas fa-arrow-left me-2"></i> Back
                                </button>
                            </div>
                            <div class="text-center mb-4">
                                <button class="btn btn-primary" id="download-template-btn" onclick="downloadSpreadsheet()">Download spreadsheet template (.csv, .xlsx)</button>
                            </div>
                            <div class="row mb-4">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Upload spreadsheet</label>
                                    <div class="upload-box" id="spreadsheet-upload">
                                        <span id="spreadsheet-upload-text">Drag & drop a file here<br>or choose .csv, .xlsx, .xls file</span>
                                        <div id="spreadsheet-progress" style="display: none;">
                                            <div class="progress-container">
                                                <div class="progress-bar-upload"></div>
                                            </div>
                                            <span class="progress-text">Uploading...</span>
                                        </div>
                                        <span id="spreadsheet-filename" style="display: none; font-weight: bold; color: #099F9A;"></span>
                                        <input type="file" class="form-control-file" style="display:none;" id="spreadsheetInput" accept=".csv,.xlsx,.xls">
                                    </div>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Upload image files</label>
                                    <div class="upload-box" id="image-upload">
                                        <span id="image-upload-text">Drag & drop a file here<br>or choose .png, .jpg, .jpeg files</span>
                                        <div id="image-progress" style="display: none;">
                                            <div class="progress-container">
                                                <div class="progress-bar-upload"></div>
                                            </div>
                                            <span class="progress-text">Uploading...</span>
                                        </div>
                                        <span id="image-filename" style="display: none; font-weight: bold; color: #099F9A;"></span>
                                        <input type="file" class="form-control-file" style="display:none;" id="imageInput" multiple accept=".png,.jpg,.jpeg">
                                    </div>
                                </div>
                            </div>
                            <div class="text-center mb-3">
                                <button class="btn btn-warning" id="generate-artwork-btn" onclick="handleGenerateArtwork()" disabled>Generate Artwork</button>
                            </div>
                            <div id="artwork-progress-bar" style="display:none; margin-bottom: 20px;">
                                <div style="width: 500px; margin: 0 auto; background: #eee; border-radius: 8px; height: 20px; position: relative;">
                                    <div id="artwork-progress-bar-inner" class="artwork-progress-inner"></div>
                                    <span id="artwork-progress-bar-label" class="artwork-progress-label">0/0 processed</span>
                                </div>
                            </div>


                            <!-- Artworks Table -->
                            <div class="table-responsive mb-3">
                                <!-- Top Pagination Controls -->
                                <div id="pagination-controls-top" style="display: none;" class="mb-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <div class="pagination-info">
                                            <span id="pagination-text-top">Page 1 of 1</span>
                                            <span class="ms-3">(<span id="total-artworks-top">0</span> total artworks)</span>
                                        </div>
                                        <div class="pagination-buttons d-flex align-items-center">
                                            <button class="btn btn-outline-secondary btn-sm" id="prev-page-btn-top" onclick="handlePrevPage()">
                                                <i class="fas fa-chevron-left"></i> Previous
                                            </button>
                                            <div class="page-numbers ms-2 me-2" id="page-numbers-top">
                                                <!-- Page numbers will be generated here -->
                                            </div>
                                            <button class="btn btn-outline-secondary btn-sm" id="next-page-btn-top" onclick="handleNextPage()">
                                                Next <i class="fas fa-chevron-right"></i>
                                            </button>
                                        </div>
                                    </div>
                                </div>

                                @if(auth()->user()->isSuperAdmin())
                                Company
                                <select id="masterCompanyDropdown" class="form-select" style="width: auto; display: inline-block; margin-left: 8px;">
                                    @foreach($companies as $company)
                                        <option value="{{$company->id}}">{{$company->name}}</option>
                                    @endforeach
                                </select>
                                @endif

                                Collection
                                <select id="masterCollectionHeader" class="form-select" style="width: auto; display: inline-block; margin-left: 8px;">
                                    @foreach($collections as $collection)
                                        <option value="{{$collection->id}}">{{$collection->name}}</option>
                                    @endforeach
                                </select>

                                <table class="table align-middle">
                                    <thead style="background-color: #f8f9fa;">
                                        <tr>
                                            <th style="color: black; font-weight: 500;">Image</th>
                                            <th style="color: black; font-weight: 500;">Title</th>
                                            <th style="color: black; font-weight: 500;">Artist</th>
                                            <th style="color: black; font-weight: 500;">Height</th>
                                            <th style="color: black; font-weight: 500;">Width</th>
                                            <th style="color: black; font-weight: 500;">
                                            Preferred unit
                                                <select id="masterUnit" class="form-select" style="width: auto; display: inline-block; margin-left: 8px;">
                                                    <option value="inch">Inch</option>
                                                    <option value="cm">cm</option>
                                                </select>
                                            </th>
                                            <th style="color: black; font-weight: 500;">Description</th>
                                            <th style="color: black; font-weight: 500;">Type</th>
                                            <th style="color: black; font-weight: 500;"></th>
                                        </tr>
                                    </thead>
                                    <tbody id="artworkTableBody">
                                        <!-- Example row, repeat for each artwork -->
                                        <!-- <tr>
                                            <td><img src="..." style="width:40px;height:40px;object-fit:cover;border-radius:6px;"></td>
                                            <td contenteditable="true">Indispensable exhibition</td>
                                            <td contenteditable="true">Jaguar Attacking a Horse</td>
                                            <td contenteditable="true">Anna Ovanesova</td>
                                            <td contenteditable="true">45.6</td>
                                            <td contenteditable="true">35.4</td>
                                            <td contenteditable="true">Digital Art</td>
                                            <td><button class="btn btn-danger btn-sm">Remove</button></td>
                                        </tr> -->
                                        <!-- More rows... -->
                                    </tbody>
                                </table>
                            </div>

                            <!-- Pagination Controls -->
                            <div id="pagination-controls" style="display: none;" class="mb-3">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div class="pagination-info">
                                        <span id="pagination-text">Page 1 of 1</span>
                                        <span class="ms-3">(<span id="total-artworks">0</span> total artworks)</span>
                                    </div>
                                    <div class="pagination-buttons d-flex align-items-center">
                                        <button class="btn btn-outline-secondary btn-sm" id="prev-page-btn" onclick="handlePrevPage()">
                                            <i class="fas fa-chevron-left"></i> Previous
                                        </button>
                                        <div class="page-numbers ms-2 me-2" id="page-numbers">
                                            <!-- Page numbers will be generated here -->
                                        </div>
                                        <button class="btn btn-outline-secondary btn-sm" id="next-page-btn" onclick="handleNextPage()">
                                            Next <i class="fas fa-chevron-right"></i>
                                        </button>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex  align-items-end gap-2" style="width: fit-content; margin-left: auto; ;">
                                <button class="btn btn-outline-primary" id="add-artwork-btn"  style="width: fit-content;" onclick="handleAddRow()">Add Artwork</button>
                                <button class="btn btn-success" id="submit-artworks-btn" style="width: fit-content;" onclick="handleSubmitArtworks()" disabled>Submit</button>
                            </div>
                        </div>
                    </div>

                </div>
            </div>
        </div>
    </section>

        <!-- Add Collection Modal -->
        <div class="modal fade" id="addCollectionModal" tabindex="-1" aria-labelledby="addCollectionModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered add-collection-modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="addCollectionModalLabel">Add new collection</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="addCollectionForm">
                        <div class="mb-3">
                            <label for="collectionName" class="form-label">Name</label>
                            <input type="text" class="form-control" id="collectionName" name="name" placeholder="Name" required>
                        </div>
                        <div class="mb-3">
                            <label for="collectionCompany" class="form-label">Company</label>
                            @if(auth()->user()->name === 'Super Admin')
                            <select class="form-control rounded-0" id="collectionCompany" name="company">
                                <option value="">Select Company</option>
                                @foreach($companies as $company)
                                    <option value="{{$company->id}}">{{$company->name}}</option>
                                @endforeach
                            </select>
                            @else
                                <input type="text" class="form-control" id="collectionCompany" placeholder="Company" disabled value="{{ user()->company->name }}">
                            @endif
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Thumbnail</label>
                            <div class="image-upload-box mb-2" id="collectionImageUploadBox">
                                <input type="file" class="image-input" id="collectionThumbnail" name="thumbnail" accept="image/*">
                                <span>Click or drag & drop to add image</span>
                                <div class="overlay">Click to replace image</div>
                            </div>
                            <div class="image-name" id="collectionImageName"></div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-save-collection" style="background-color: #099F9A; border-color: #099F9A; color: white;" id="saveCollectionBtn" onclick="handleSaveCollection()">Save</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Multiple Artwork Upload Modal -->
    <div class="modal fade" id="multipleArtworkModal" tabindex="-1" aria-labelledby="multipleArtworkModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content" style="border-radius: 12px; border: 1px solid #e0e0e0;">
                <div class="modal-header" style="background: #f8f9fa; border-bottom: 1px solid #dee2e6; border-radius: 12px 12px 0 0;">
                    <h5 class="modal-title" id="multipleArtworkModalLabel" style="font-weight: 600; color: #495057;">Add Multiple Artworks</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body" style="padding: 0px">
                    <form id="multipleArtworkForm">
                        <div class="col-md-12" style="padding: 12px 24px;">
                            <label for="numberOfRows" class="form-label" style="font-weight: 500; color: #495057;">Enter number of new artworks to add to inventory</label>
                            <input type="number" class="form-control" id="numberOfRows" name="number_of_rows" min="1" max="50" value="2" style="width : 50%; border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 12px;">
                        </div>

                        <!-- Horizontal line -->
                        <hr style="margin: 0px; background-color: #82868a;" >
                        <div style="background-color: #f5f5f5">
                            <div style="padding: 24px 24px 12px 24px;">
                                <div class="row">
                                    <div class="col-12">
                                        <p style="font-size: 0.9em; color: #495057; margin-bottom: 16px;">
                                            Entering information into the boxes below will pre fill all the new artwork pieces you are adding to the table.
                                            Leave the boxes blank to keep them empty and add the details later.
                                        </p>
                                    </div>
                                </div>

                                <!-- Form fields section with pale grey background -->
                                <div class="row">
                                    <div class="col-md-12">
                                        @if(auth()->user()->isSuperAdmin())
                                            <div class="mb-3">
                                                <label for="prefillCompany" class="form-label" style="font-weight: 500; font-size: 1em; color: #495057;">Company</label>
                                                <select class="form-select" id="prefillCompany" name="company" style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 12px;">
                                                    <option value="">Select company</option>
                                                    @foreach($companies as $company)
                                                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                                                    @endforeach
                                                </select>
                                            </div>
                                            @endif
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="prefillCollection" class="form-label" style="font-weight: 500; font-size: 1em; color: #495057;">Collection</label>
                                            <select class="form-select" id="prefillCollection" name="collection" style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 12px;">
                                                <option value="">Select collection</option>
                                                @foreach($collections as $collection)
                                                    <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                                                @endforeach
                                            </select>
                                        </div>
                                        <div class="mb-3">
                                            <label for="prefillArtist" class="form-label" style="font-weight: 500; font-size: 1em; color: #495057;">Artist</label>
                                            <input type="text" class="form-control" id="prefillArtist" name="artist" placeholder="Enter artist name" style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 12px;">
                                        </div>
                                        <div class="mb-3">
                                            <label for="prefillHeight" class="form-label" style="font-weight: 500; font-size: 1em; color: #495057;">Height</label>
                                            <input type="number" class="form-control" id="prefillHeight" name="height" placeholder="Enter height" step="0.01" style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 12px;">
                                        </div>
                                        <div class="mb-3">
                                            <label for="prefillUnit" class="form-label" style="font-weight: 500; font-size: 1em; color: #495057;">Unit</label>
                                            <select class="form-select" id="prefillUnit" name="unit" style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 12px;">
                                                <option value="cm">cm</option>
                                                <option value="inch">inch</option>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="mb-3">
                                            <label for="prefillTitle" class="form-label" style="font-weight: 500; font-size: 1em; color: #495057;">Title</label>
                                            <input type="text" class="form-control" id="prefillTitle" name="title" placeholder="Enter title" style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 12px;">
                                        </div>
                                        <div class="mb-3">
                                            <label for="prefillType" class="form-label" style="font-weight: 500; font-size: 1em; color: #495057;">Type</label>
                                            <input type="text" class="form-control" id="prefillType" name="type" placeholder="Enter artwork type" style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 12px;">
                                        </div>
                                        <div class="mb-3">
                                            <label for="prefillWidth" class="form-label" style="font-weight: 500; font-size: 1em; color: #495057;">Width</label>
                                            <input type="number" class="form-control" id="prefillWidth" name="width" placeholder="Enter width" step="0.01" style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 12px;">
                                        </div>

                                        <div class="mb-3">
                                            <label for="prefillDescription" class="form-label" style="font-weight: 500; font-size: 1em; color: #495057;">Description</label>
                                            <textarea class="form-control" id="prefillDescription" name="description" rows="5" placeholder="Enter description" style="border: 1px solid #dee2e6; border-radius: 6px; padding: 8px 12px;"></textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>


                    </form>
                </div>
                <div class="modal-footer" style="border-top: 1px solid #dee2e6; padding: 16px 24px;">
                    <button type="button" class="btn btn-success" id="createMultipleBtn" style="background: #099F9A; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600; color: white;">Create</button>
                    <button type="button" class="btn" data-bs-dismiss="modal" style="background: #dc3545; border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600; color: white; margin-right: 10px;">Cancel</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Bulk Edit Modal -->
    <div class="modal fade" id="bulkEditModal" tabindex="-1" aria-labelledby="bulkEditModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="bulkEditModalLabel">Edit information for selected pieces</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="bulkEditForm">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bulkCollection" class="form-label">Collection</label>
                                    <select class="form-select" id="bulkCollection" name="collection">
                                        <option value="">-- Keep existing --</option>
                                        @foreach($collections as $collection)
                                            <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="mb-3">
                                    <label for="bulkArtist" class="form-label">Artist</label>
                                    <input type="text" class="form-control" id="bulkArtist" name="artist" placeholder="Enter artist name">
                                </div>
                                <div class="mb-3">
                                    <label for="bulkHeight" class="form-label">Height</label>
                                    <input type="number" class="form-control" id="bulkHeight" name="height" placeholder="Enter height">
                                </div>
                                <div class="mb-3">
                                    <label for="bulkUnit" class="form-label">Unit</label>
                                    <select class="form-select" id="bulkUnit" name="unit">
                                        <option value="">-- Keep existing --</option>
                                        <option value="cm">cm</option>
                                        <option value="inch">inch</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="mb-3">
                                    <label for="bulkTitle" class="form-label">Title</label>
                                    <input type="text" class="form-control" id="bulkTitle" name="name" placeholder="Enter title">
                                </div>
                                <div class="mb-3">
                                    <label for="bulkDescription" class="form-label">Description</label>
                                    <textarea class="form-control" id="bulkDescription" name="description" rows="3" placeholder="Enter description"></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="bulkWidth" class="form-label">Width</label>
                                    <input type="number" class="form-control" id="bulkWidth" name="width" placeholder="Enter width">
                                </div>
                                <div class="mb-3">
                                    <label for="bulkType" class="form-label">Type</label>
                                    <input type="text" class="form-control" id="bulkType" name="type" placeholder="Enter artwork type">
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="bulkUpdateBtn">Update Selected Items</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Move To Collection Modal -->
    <div class="modal fade" id="moveToCollectionModal" tabindex="-1" aria-labelledby="moveToCollectionModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="moveToCollectionModalLabel">Move to Collection</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="mb-3">
                        <label for="moveCollectionSelect" class="form-label">Select Collection</label>
                        <select class="form-select" id="moveCollectionSelect" name="collection">
                            <option value="">Select collection</option>
                            @foreach($collections as $collection)
                                <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmMoveBtn">Move</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Inline Editing Row Template (Hidden) -->
    <template id="newArtworkRowTemplate">
        <tr class="new-artwork-row" data-temp-id="">
            <td>
                <input type="checkbox" class="form-check-input">
            </td>
            <td>
                <div class="drag-drop-area" style="width: 40px; height: 40px; border: 2px dashed #dee2e6; border-radius: 6px; display: flex; align-items: center; justify-content: center; cursor: pointer; background: #f8f9fa;">
                    <i class="fas fa-plus text-muted"></i>
                </div>
                <input type="file" class="image-upload-input" multiple accept="image/*" style="display: none;">
            </td>
            @if(auth()->user()->isSuperAdmin())
            <td>
                <select class="form-select form-select-sm company-select" required>
                    <option value="">Select company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </td>
            @endif
            <td>
                <select class="form-select form-select-sm collection-select" required>
                    <option value="">Select collection</option>
                    @foreach($collections as $collection)
                        <option value="{{ $collection->id }}">{{ $collection->name }}</option>
                    @endforeach
                </select>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm title-input" placeholder="Enter title" required>
            </td>
            <td>
                <input type="text" class="form-control form-control-sm artist-input" placeholder="Enter artist">
            </td>
            <!-- <td>
                <textarea class="form-control form-control-sm description-input" rows="1" placeholder="Enter description"></textarea>
            </td> -->
            <td>
                <input type="text" class="form-control form-control-sm type-input" placeholder="Enter type">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm height-input" placeholder="Height" step="0.01">
            </td>
            <td>
                <input type="number" class="form-control form-control-sm width-input" placeholder="Width" step="0.01">
            </td>
            <td>
                <select class="form-select form-select-sm unit-select">
                    <option value="cm">cm</option>
                    <option value="inch">inch</option>
                </select>
            </td>
            <td>
               <textarea class="form-control form-control-sm description-input" rows="1" placeholder="Enter description"></textarea>
            </td>
            <td>
                <button class="btn btn-danger btn-sm delete-row-btn" title="Delete">
                    <i class="fas fa-trash"></i>
                </button>
            </td>
        </tr>
    </template>


    <!-- Submit Progress Modal -->
    <div class="modal fade" id="submitProgressModal" tabindex="-1" aria-labelledby="submitProgressModalLabel" aria-hidden="true" data-bs-backdrop="static" data-bs-keyboard="false">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <div class="spinner-border text-primary" role="status" style="width: 3rem; height: 3rem;">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                    <h5 class="mb-3">Saving Artworks...</h5>
                    <div class="progress mb-3" style="height: 8px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated" id="submitProgressBar" role="progressbar" style="width: 0%"></div>
                    </div>
                    <p class="text-muted mb-0" id="submitProgressText">Processing artwork data...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Page Upload Confirmation Modal -->
    <div class="modal fade" id="pageUploadModal" tabindex="-1" aria-labelledby="pageUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="pageUploadModalLabel">Upload Artworks</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p id="pageUploadText">Do you want to upload the artworks on this page?</p>
                    <div class="alert alert-info">
                        <small>
                            <strong>Note:</strong> You can upload artworks page by page. After each page is uploaded,
                            you'll be asked if you want to continue with the next page.
                        </small>
                    </div>
                    <div class="text-center mt-3">
                        <div class="countdown-container">
                            <span class="text-muted">Auto-submit in </span>
                            <span id="countdown-timer" class="fw-bold text-primary">10</span>
                            <span class="text-muted"> seconds</span>
                        </div>
                        <div class="progress mt-2" style="height: 4px;">
                            <div class="progress-bar bg-primary" id="countdown-progress" role="progressbar" style="width: 100%"></div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button type="button" class="btn btn-primary" id="confirmPageUploadBtn">Yes, Upload This Page</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Success Modal -->
    <div class="modal fade" id="successModal" tabindex="-1" aria-labelledby="successModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-success text-white">
                    <h5 class="modal-title" id="successModalLabel">
                        <i class="fas fa-check-circle me-2"></i>Upload Successful
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-check-circle text-success" style="font-size: 3rem;"></i>
                    </div>
                    <h6 id="successModalTitle">Upload Completed!</h6>
                    <p id="successModalMessage" class="text-muted mb-0"></p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-success" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Collection Selection Alert Modal -->
    <div class="modal fade" id="collectionSelectionAlertModal" tabindex="-1" aria-labelledby="collectionSelectionAlertModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-warning text-dark">
                    <h5 class="modal-title" id="collectionSelectionAlertModalLabel">
                        <i class="fas fa-exclamation-triangle me-2"></i>Collection Selection Required
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-exclamation-triangle text-warning" style="font-size: 3rem;"></i>
                    </div>
                    <h6>Please Select a Collection</h6>
                    <p class="text-muted mb-0">You need to select a specific collection before downloading the inventory. Please choose a collection from the sidebar and try again.</p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-warning" data-bs-dismiss="modal">OK</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Continue Upload Confirmation Modal -->
    <div class="modal fade" id="continueUploadModal" tabindex="-1" aria-labelledby="continueUploadModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title" id="continueUploadModalLabel">
                        <i class="fas fa-question-circle me-2"></i>Continue Upload
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body text-center">
                    <div class="mb-3">
                        <i class="fas fa-arrow-right text-primary" style="font-size: 3rem;"></i>
                    </div>
                    <h6 id="continueUploadTitle">Continue with next page?</h6>
                    <p id="continueUploadMessage" class="text-muted mb-0"></p>
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No, Stop Here</button>
                    <button type="button" class="btn btn-primary" id="continueUploadBtn">Yes, Continue</button>
                </div>
            </div>
        </div>
    </div>

    <!-- Artwork Detail Popup Modal -->
    <div class="modal fade" id="artworkDetailModal" tabindex="-1" aria-labelledby="artworkDetailModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header text-white" style="background-color: #099F9A;">
                    <h5 class="modal-title" id="artworkDetailModalLabel">
                        <i class="fas fa-image me-2"></i>Artwork Details
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="artwork-image-container text-center mb-3">
                                <img id="artworkDetailImage" src="" alt="Artwork" class="img-fluid rounded shadow" style="max-height: 400px; object-fit: contain;">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="artwork-details">
                                <h6 class="text-muted mb-3">Artwork Information</h6>
                                <div class="detail-item mb-2">
                                    <strong>Name:</strong> <span id="artworkDetailName">-</span>
                                </div>
                                <div class="detail-item mb-2">
                                    <strong>Artist:</strong> <span id="artworkDetailArtist">-</span>
                                </div>
                                <div class="detail-item mb-2">
                                    <strong>Type:</strong> <span id="artworkDetailType">-</span>
                                </div>
                                <div class="detail-item mb-2">
                                    <strong>Collection:</strong> <span id="artworkDetailCollection">-</span>
                                </div>
                                <div class="detail-item mb-2">
                                    <strong>Company:</strong> <span id="artworkDetailCompany">-</span>
                                </div>
                                <div class="detail-item mb-2">
                                    <strong>Dimensions:</strong> <span id="artworkDetailDimensions">-</span>
                                </div>
                                <div class="detail-item mb-2">
                                    <strong>Unit:</strong> <span id="artworkDetailUnit">-</span>
                                </div>
                                <div class="detail-item">
                                    <strong>Description:</strong>
                                    <div id="artworkDetailDescription" >-</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

@endsection


@section('styles')
<!-- DataTables CSS and JS -->
<link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/vendor/dataTables.bootstrap4.css') }}">
<link href="{{ asset('css/page/inventory.css') }}" rel="stylesheet">

@endsection



@section('scripts')
<!-- Load DataTables libraries -->
<script type="text/javascript" charset="utf8" src="{{ asset('backend/assets/js/vendor/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" charset="utf8" src="{{ asset('backend/assets/js/vendor/dataTables.bootstrap4.js') }}"></script>
<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>

<script>

    const allCollections = @json($collections);
    const allCompanies = @json($companies);
    const mainContainer = document.getElementById('show-collections-container');
    const uploadContainer = document.getElementById('upload-artwork-container');

    const spreadUploadText = document.getElementById('spreadsheet-upload-text');
    const spreadProgress = document.getElementById('spreadsheet-progress');
    const spreadFilename = document.getElementById('spreadsheet-filename');

    const imageUploadText = document.getElementById('image-upload-text');
    const imageProgress = document.getElementById('image-progress');
    const imageFilename = document.getElementById('image-filename');

    const isSuperAdmin = @json(auth()->user()->isSuperAdmin());


    let uploadedSpreadsheetData = null;
    let uploadedImageFiles = [];
    let totalExpectedArtworks = 0;
    let processedArtworks = 0;
    let isProcessingArtworks = false;

    // Pagination variables
    let currentPage = 1;
    let totalPages = 1;
    let artworksPerPage = 100;
    let allArtworksData = []; // Store all processed artwork data
    let currentPageArtworks = []; // Store current page artwork data
    let uploadedPages = new Set(); // Track which pages have been uploaded

    // Auto-submit countdown variables
    let countdownInterval = null;
    let countdownTime = 10; // 10 seconds
    let currentCountdown = 10;


    function getFilteredCollections(artworkRow = null) {
        if (isSuperAdmin) {
            // For super admin, try to get company from the artwork row first
            let selectedCompanyId = null;

            if (artworkRow) {
                // Try to get company from the artwork row's company cell
                const companyCell = artworkRow.querySelector('[data-field="company"]');
                if (companyCell) {
                    // Get the company name from the cell text
                    const companyName = companyCell.textContent.trim();
                    if (companyName) {
                        // Find the company ID by name
                        const company = allCompanies.find(c => c.name === companyName);
                        if (company) {
                            selectedCompanyId = company.id;
                        }
                    }
                } else {
                    // Try to get company from company select dropdown (for new rows)
                    const companySelect = artworkRow.querySelector('.company-select');
                    if (companySelect && companySelect.value) {
                        selectedCompanyId = companySelect.value;
                    }
                }
            }


            if (selectedCompanyId) {
                return allCollections.filter(collection =>
                    collection.company_id == selectedCompanyId
                );
            }
            return allCollections; // Show all collections if no company selected
        } else {
            // For non-super admin, filter by user's company
            const userCompanyId = @json(auth()->user()->company_id);
            return allCollections.filter(collection =>
                collection.company_id == userCompanyId
            );
        }
    }

    function updateCollectionDropdownForRow(rowElement) {
        const collectionSelect = rowElement.querySelector('.collection-select');
        if (!collectionSelect) return;

        // Get filtered collections for this row
        const filteredCollections = getFilteredCollections(rowElement);

        // Clear existing options
        collectionSelect.innerHTML = '<option value="">Select collection</option>';

        // Add filtered collections
        if (filteredCollections.length > 0) {
            filteredCollections.forEach(function(collection) {
                const option = document.createElement('option');
                option.value = collection.id;
                option.textContent = collection.name;
                collectionSelect.appendChild(option);
            });
        } else {
            const noCollectionsOption = document.createElement('option');
            noCollectionsOption.value = '';
            noCollectionsOption.textContent = 'No collections available';
            noCollectionsOption.disabled = true;
            collectionSelect.appendChild(noCollectionsOption);
        }
    }

    // Helper function to get property value from multiple possible property names
    function getProperty(obj, propertyNames) {
        for (let propName of propertyNames) {
            if (obj[propName] !== undefined && obj[propName] !== null && obj[propName] !== '') {
                return obj[propName];
            }
        }
        return null;
    }
    // Find the "Add" button (the first .btn-light with title="Add")
    function handleOpenUploadArtworks() {
        mainContainer.style.display = 'none';
        uploadContainer.style.display = 'block';
    }

    function backToCollections() {
        uploadContainer.style.display = 'none';
        mainContainer.style.display = 'block'; // or 'block' if flex doesn't work
    }



    // Function to check if submit button should be enabled
    function updateSubmitButtonState() {
        const submitBtn = document.getElementById('submit-artworks-btn');
        const tbody = document.getElementById('artworkTableBody');
        const hasEntries = tbody.children.length > 0;
        const allProcessed = !isProcessingArtworks && processedArtworks >= totalExpectedArtworks;

        // For paginated data, check if current page has been uploaded
        const currentPageUploaded = uploadedPages.has(currentPage);

        // Enable button only if we have entries, all processing is complete, and current page hasn't been uploaded
        submitBtn.disabled = !hasEntries || !allProcessed || currentPageUploaded;

        // Update button text based on upload status
        if (currentPageUploaded) {
            submitBtn.textContent = 'Page Uploaded';
            submitBtn.classList.add('btn-secondary');
            submitBtn.classList.remove('btn-success');
        } else {
            submitBtn.textContent = 'Submit';
            submitBtn.classList.remove('btn-secondary');
            submitBtn.classList.add('btn-success');
        }
    }

    // Function to update generate artwork button state
    function updateGenerateArtworkButtonState() {
        const generateBtn = document.getElementById('generate-artwork-btn');
        const hasSpreadsheet = uploadedSpreadsheetData && uploadedSpreadsheetData.length > 0;
        const hasImages = uploadedImageFiles && uploadedImageFiles.length > 0;

        generateBtn.disabled = !(hasSpreadsheet && hasImages);
    }

    // Find the "Add" button (the first .btn-light with title="Add")
    function handleOpenUploadArtworks() {
        mainContainer.style.display = 'none';
        uploadContainer.style.display = 'block';
        updateSubmitButtonState(); // Ensure submit button is disabled initially
        updateGenerateArtworkButtonState(); // Ensure generate button is disabled initially
    }

    function backToCollections() {
        uploadContainer.style.display = 'none';
        mainContainer.style.display = 'block'; // or 'block' if flex doesn't work
        resetSpreadsheetUpload();
        resetImageUpload();
        resetPagination(); // Reset pagination when going back
        updateGenerateArtworkButtonState(); // Reset generate button state
    }

    function resetSpreadsheetUpload() {
        // Reset upload box to initial state
        spreadUploadText.style.display = 'block';
        spreadProgress.style.display = 'none';
        spreadFilename.style.display = 'none';
        document.getElementById('spreadsheetInput').value = '';
        uploadedSpreadsheetData = null;
    }

    function resetImageUpload() {
        // Reset image upload box to initial state
        imageUploadText.style.display = 'block';
        imageProgress.style.display = 'none';
        imageFilename.style.display = 'none';
        document.getElementById('imageInput').value = '';
        uploadedImageFiles = [];
    }

    // Click on upload box triggers file input
    document.getElementById('spreadsheet-upload').onclick = () => document.getElementById('spreadsheetInput').click();
    document.getElementById('image-upload').onclick = () => document.getElementById('imageInput').click();

    // Add drag and drop functionality for spreadsheet upload
    const spreadsheetUpload = document.getElementById('spreadsheet-upload');
    const spreadsheetInput = document.getElementById('spreadsheetInput');

    spreadsheetUpload.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('dragover');
    });

    spreadsheetUpload.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('dragover');
    });

    spreadsheetUpload.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            const file = files[0];
            // Check if file type is valid
            const validTypes = ['.csv', '.xlsx', '.xls'];
            const fileExtension = '.' + file.name.split('.').pop().toLowerCase();

            if (validTypes.includes(fileExtension)) {
                // Set the file to the input
                const dataTransfer = new DataTransfer();
                dataTransfer.items.add(file);
                spreadsheetInput.files = dataTransfer.files;

                // Trigger the change event
                const event = new Event('change', { bubbles: true });
                spreadsheetInput.dispatchEvent(event);
            } else {
                alert('Please select a valid file type (.csv, .xlsx, .xls)');
            }
        }
    });

    // Add drag and drop functionality for image upload
    const imageUpload = document.getElementById('image-upload');
    const imageInput = document.getElementById('imageInput');

    imageUpload.addEventListener('dragover', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.add('dragover');
    });

    imageUpload.addEventListener('dragleave', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('dragover');
    });

    imageUpload.addEventListener('drop', function(e) {
        e.preventDefault();
        e.stopPropagation();
        this.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
            // Check if all files are valid image types
            const validTypes = ['.png', '.jpg', '.jpeg'];
            const validFiles = Array.from(files).filter(file => {
                const fileExtension = '.' + file.name.split('.').pop().toLowerCase();
                return validTypes.includes(fileExtension);
            });

            if (validFiles.length === files.length) {
                // Set the files to the input
                const dataTransfer = new DataTransfer();
                validFiles.forEach(file => dataTransfer.items.add(file));
                imageInput.files = dataTransfer.files;

                // Trigger the change event
                const event = new Event('change', { bubbles: true });
                imageInput.dispatchEvent(event);
            } else {
                alert('Please select valid image files (.png, .jpg, .jpeg)');
            }
        }
    });

    function downloadSpreadsheet() {
        const data = [
            ['Artwork upload spreadsheet'],
            [],
            ['Add required information for each piece of artwork'],
            ["Ensure the 'Filename' fully matches the images filename"],
            ['Upload completed spreadsheet to Tetra'],
            [],
            ['Filename', 'Company', 'Collection', 'Title', 'Artist', 'Height', 'Width', 'Unit', 'Description', 'Type']
        ];

        const rows = document.querySelectorAll('#artworkTableBody tr');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length < 9) return;

            const rowData = [];
            const img = cells[0].querySelector('img');
            rowData.push(img ? img.getAttribute('data-filename') || '' : '');

            // 2. Collection
            const collectionSelect = cells[2].querySelector('select');
            rowData.push(collectionSelect && collectionSelect.value ? collectionSelect.options[collectionSelect.selectedIndex].text : '');

            rowData.push(cells[3].textContent.trim());
            rowData.push(cells[4].textContent.trim());
            rowData.push(cells[5].querySelector('input').value);
            rowData.push(cells[6].querySelector('input').value);
            const unitSelect = cells[7].querySelector('select');
            rowData.push(unitSelect ? unitSelect.value : '');

            rowData.push(cells[7].textContent.trim());
            rowData.push(cells[8].textContent.trim());

            data.push(rowData);
        });

        // Download both formats
        // downloadCSV(data);
        downloadXLSX(data);
    }

    function downloadCSV(data) {
        const worksheet = XLSX.utils.aoa_to_sheet(data, {
            cellStyles: false,
            sheetStubs: true
        });

        if (!worksheet['!merges']) worksheet['!merges'] = [];
        worksheet['!merges'].push({ s: { r: 0, c: 0 }, e: { r: 0, c: 6 } });
        worksheet['!merges'].push({ s: { r: 2, c: 0 }, e: { r: 2, c: 6 } });
        worksheet['!merges'].push({ s: { r: 3, c: 0 }, e: { r: 3, c: 6 } });
        worksheet['!merges'].push({ s: { r: 4, c: 0 }, e: { r: 4, c: 6 } });

        const csvContent = XLSX.utils.sheet_to_csv(worksheet);

        const blob = new Blob([csvContent], { type: 'text/csv;charset=utf-8;' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'artworks_template.csv');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    function downloadXLSX(data) {
        const worksheet = XLSX.utils.aoa_to_sheet(data, {
            cellStyles: false,
            sheetStubs: true
        });

        if (!worksheet['!merges']) worksheet['!merges'] = [];
        worksheet['!merges'].push({ s: { r: 0, c: 0 }, e: { r: 0, c: 6 } });
        worksheet['!merges'].push({ s: { r: 2, c: 0 }, e: { r: 2, c: 6 } });
        worksheet['!merges'].push({ s: { r: 3, c: 0 }, e: { r: 3, c: 6 } });
        worksheet['!merges'].push({ s: { r: 4, c: 0 }, e: { r: 4, c: 6 } });

        // Create workbook with the worksheet
        const workbook = XLSX.utils.book_new();
        XLSX.utils.book_append_sheet(workbook, worksheet, 'Artworks Template');

        // Generate XLSX file
        const xlsxContent = XLSX.write(workbook, { bookType: 'xlsx', type: 'array' });

        const blob = new Blob([xlsxContent], { type: 'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet' });
        const link = document.createElement('a');
        const url = URL.createObjectURL(blob);
        link.setAttribute('href', url);
        link.setAttribute('download', 'artworks_template.xlsx');
        link.style.visibility = 'hidden';
        document.body.appendChild(link);
        link.click();
        document.body.removeChild(link);
    }

    // Global variable to track if we're in edit mode
    let isEditMode = false;
    let editingCollectionId = null;

    function handleSaveCollection() {
        const companySelect = document.getElementById('collectionCompany');

        const collectionName = document.getElementById('collectionName').value;
        const collectionThumbnail = document.getElementById('collectionThumbnail').files[0];
        let companyName;
        if (isSuperAdmin) {
            companyName = companySelect.options[companySelect.selectedIndex].text;
        } else {
            companyName = companySelect.value;
        }

        const formData = new FormData();
        formData.append('collection_name', collectionName);
        formData.append('collection_company_name', companyName);
        if (collectionThumbnail) {
            formData.append('collection_thumbnail', collectionThumbnail);
        }

        // Debug logging

        const token = document.querySelector('meta[name="csrf-token"]').content;

        // Determine the URL and method based on edit mode
        const url = isEditMode ? `/inventory/collections/${editingCollectionId}/edit` : '/inventory/collections/add';

        // Add _method field for Laravel to recognize PUT requests
        if (isEditMode) {
            formData.append('_method', 'PUT');
        }

        fetch(url, {
            method: 'POST', // Always use POST for FormData, Laravel will handle the method via _method field
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                const message = isEditMode ? 'Collection updated successfully' : 'Collection added successfully';
                alert(message);

                // Update collection image dynamically instead of reloading
                if (isEditMode) {
                    $('#addCollectionModal').modal('hide');
                    updateCollectionImageInUI(editingCollectionId, data.collection.name, data.collection.thumbnail_url, data.collection.item_count);
                } else {
                    // For new collections, refresh the dropdown menu to show the new collection
                    window.location.reload();
                }
            } else {
                alert('Error: ' + (data.message || `Could not ${isEditMode ? 'update' : 'add'} collection.`));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert(`Error ${isEditMode ? 'updating' : 'adding'} collection.`);
        });
    }

    // Add Artwork button (add a new editable row)
    function handleAddRow() {
        const tbody = document.getElementById('artworkTableBody');
        const row = document.createElement('tr');
        const uniqueId = 'artwork-image-' + Date.now() + '-' + Math.floor(Math.random() * 10000);
        const masterUnitDropdown = document.getElementById('masterUnit');

        row.innerHTML = `
            <td>
                <div class="upload-box artwork-image-upload" style="width: 60px; height: 60px; min-height: 0; padding: 0; font-size: 12px; cursor: pointer;">
                    <span class="artwork-image-upload-text">Select or drag file</span>
                    <input type="file" accept=".png,.jpg,.jpeg" style="display:none;" id="${uniqueId}">
                </div>
            </td>

            <td contenteditable="true" data-placeholder="Enter title..." class="empty-cell"></td>
            <td contenteditable="true" data-placeholder="Enter artist name..." class="empty-cell"></td>
            <td><input type="number" id="artwork-height" class="form-control empty-cell" style="width: 100px; min-width: 60px;" /></td>
            <td><input type="number" id="artwork-width" class="form-control empty-cell" style="width: 100px; min-width: 60px;" /></td>
            <td>
                <select class="form-select artwork-unit-select" >
                    <option value="inch" ${masterUnitDropdown.value === 'inch' ? 'selected' : ''}>inch</option>
                    <option value="cm" ${masterUnitDropdown.value === 'cm' ? 'selected' : ''}>cm</option>
                </select>
            </td>
            <td contenteditable="true" data-placeholder="Enter artwork description..." class="empty-cell"></td>
            <td contenteditable="true" data-placeholder="Enter artwork type..." class="empty-cell"></td>
            <td><button class="btn btn-danger btn-sm">Remove</button></td>
        `;
        row.querySelector('button').onclick = function() {
            row.remove();
            updateSubmitButtonState(); // Update state when row is removed
        };
        tbody.appendChild(row);

        // Update submit button state after adding row
        updateSubmitButtonState();
        updateGenerateArtworkButtonState(); // Update generate button state after adding row

        // --- Image upload logic for this row ---
        const uploadBox = row.querySelector('.artwork-image-upload');
        const fileInput = row.querySelector('input[type="file"]');
        const uploadText = row.querySelector('.artwork-image-upload-text');

        // Function to handle image selection and populate fields
        function handleImageSelection(file) {
            if (!file) return;

            const reader = new FileReader();
            reader.onload = function(ev) {
                // Create image element to get dimensions
                const img = new Image();
                img.onload = function() {
                    // Update the upload box with image preview
                    uploadBox.innerHTML = `<img src="${ev.target.result}" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;" data-filename="${file.name}">`;

                    // Populate title with filename (without extension)
                    const titleCell = row.querySelector('td:nth-child(3)');
                    const filenameWithoutExt = file.name.replace(/\.[^/.]+$/, "");
                    titleCell.textContent = filenameWithoutExt;

                    // Populate width and height fields
                    const widthInput = row.querySelector('input[id="artwork-width"]');
                    const heightInput = row.querySelector('input[id="artwork-height"]');

                    // Convert pixels to inches (assuming 96 DPI for web images)
                    const widthInInches = Math.round((img.width / 96) * 10) / 10;
                    const heightInInches = Math.round((img.height / 96) * 10) / 10;

                    widthInput.value = widthInInches;
                    heightInput.value = heightInInches;
                };
                img.src = ev.target.result;
            };
            reader.readAsDataURL(file);
        }

        // Click upload box triggers file input
        uploadBox.onclick = function(e) {
            if (e.target === fileInput) return; // Don't double-trigger
            fileInput.click();
        };

        // Drag & drop support
        uploadBox.addEventListener('dragover', function(e) {
            e.preventDefault();
            uploadBox.classList.add('dragover');
        });
        uploadBox.addEventListener('dragleave', function(e) {
            e.preventDefault();
            uploadBox.classList.remove('dragover');
        });
        uploadBox.addEventListener('drop', function(e) {
            e.preventDefault();
            uploadBox.classList.remove('dragover');
            const files = e.dataTransfer.files;
            if (files.length > 0) {
                fileInput.files = files;
                handleImageSelection(files[0]);
            }
        });

        // File input change: show preview and populate fields
        fileInput.addEventListener('change', function(e) {
            const file = fileInput.files[0];
            handleImageSelection(file);
        });

        // Add event listeners to remove empty-cell class when user interacts
        const contentEditableCells = row.querySelectorAll('[contenteditable="true"]');
        contentEditableCells.forEach(cell => {
            cell.addEventListener('input', function() {
                if (this.textContent.trim() !== '') {
                    this.classList.remove('empty-cell');
                } else {
                    this.classList.add('empty-cell');
                }
            });
        });

        const inputFields = row.querySelectorAll('input[type="number"]');
        inputFields.forEach(input => {
            input.addEventListener('input', function() {
                if (this.value.trim() !== '') {
                    this.classList.remove('empty-cell');
                } else {
                    this.classList.add('empty-cell');
                }
            });
        });

        const selectFields = row.querySelectorAll('select');
        selectFields.forEach(select => {
            select.addEventListener('change', function() {
                if (this.value !== '') {
                    this.classList.remove('empty-cell');
                } else {
                    this.classList.add('empty-cell');
                }
            });
        });

        // Add unit change event listener for individual unit dropdowns
        const unitSelect = row.querySelector('.artwork-unit-select');
        if (unitSelect) {
            // Set initial previous unit
            unitSelect.dataset.previousUnit = unitSelect.value;

            unitSelect.addEventListener('change', function() {
             //   handleUnitChange(this);
            });
        }
    }

    function handleSubmitArtworks() {
        // Check if we have pagination (large dataset)
        if (allArtworksData.length > 0 && totalPages > 1) {
            // Show page upload confirmation modal
            showPageUploadConfirmation();
        } else {
            // Handle single page upload (existing logic)
            uploadCurrentPageArtworks();
        }
    }

    function showPageUploadConfirmation() {
        const modal = new bootstrap.Modal(document.getElementById('pageUploadModal'));
        const pageUploadText = document.getElementById('pageUploadText');
        const confirmBtn = document.getElementById('confirmPageUploadBtn');

        const startIndex = (currentPage - 1) * artworksPerPage;
        const endIndex = Math.min(startIndex + artworksPerPage, allArtworksData.length);
        const pageSize = endIndex - startIndex;

        pageUploadText.textContent = `Do you want to upload artworks ${startIndex + 1}-${endIndex} of page ${currentPage}?`;

        // Update confirm button text
        confirmBtn.textContent = `Yes, Upload ${pageSize} Artworks`;

        // Reset countdown
        currentCountdown = countdownTime;
        updateCountdownDisplay();

        // Set up confirm button click handler
        confirmBtn.onclick = function() {
            clearCountdown();
            modal.hide();
            uploadCurrentPageArtworks();
        };

        // Set up modal events
        modal._element.addEventListener('hidden.bs.modal', function() {
            clearCountdown();
        });

        // Start countdown
        startCountdown(() => {
            modal.hide();
            uploadCurrentPageArtworks();
        });

        modal.show();
    }

    function startCountdown(callback) {
        clearCountdown(); // Clear any existing countdown

        countdownInterval = setInterval(() => {
            currentCountdown--;
            updateCountdownDisplay();

            if (currentCountdown <= 0) {
                clearCountdown();
                if (callback) callback();
            }
        }, 1000);
    }

    function clearCountdown() {
        if (countdownInterval) {
            clearInterval(countdownInterval);
            countdownInterval = null;
        }
    }

    function updateCountdownDisplay() {
        const timerElement = document.getElementById('countdown-timer');
        const progressElement = document.getElementById('countdown-progress');

        if (timerElement && progressElement) {
            timerElement.textContent = currentCountdown;
            const progressPercentage = (currentCountdown / countdownTime) * 100;
            progressElement.style.width = progressPercentage + '%';

            // Add visual urgency indicators
            timerElement.classList.remove('warning', 'danger');
            if (currentCountdown <= 3) {
                timerElement.classList.add('danger');
            } else if (currentCountdown <= 5) {
                timerElement.classList.add('warning');
            }
        }
    }


    function uploadCurrentPageArtworks() {
        const submitBtn = document.getElementById('submit-artworks-btn');
        const originalText = submitBtn.innerHTML;

        // Show loading state on button immediately
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Uploading...';
        submitBtn.disabled = true;

        // Show progress modal immediately
        const progressModal = new bootstrap.Modal(document.getElementById('submitProgressModal'));
        progressModal.show();

        // Use setTimeout to allow the modal to render first, then collect data
        setTimeout(() => {
            const tbody = document.getElementById('artworkTableBody');
            const rows = tbody.querySelectorAll('tr');
            const data = [];

            // Update progress text immediately
            const startIndex = (currentPage - 1) * artworksPerPage;
            const endIndex = Math.min(startIndex + artworksPerPage, allArtworksData.length);
            document.getElementById('submitProgressText').textContent = `Preparing artworks ${startIndex + 1}-${endIndex} for upload...`;
            document.getElementById('submitProgressBar').style.width = '25%';

            // Collect all the data from current page
            rows.forEach((row, index) => {
                const cells = row.querySelectorAll('td');
                const image = cells[0].querySelector('img');
                // Get collection name from master collection header dropdown
                const masterCollectionSelect = document.getElementById('masterCollectionHeader');
                const collectionName = masterCollectionSelect.options[masterCollectionSelect.selectedIndex].text;
                const unitSelect = cells[5].querySelector('select');
                const unitValue = unitSelect ? unitSelect.value : '';

                const rowData = {
                    collection_name: collectionName,
                    title: cells[1].textContent.trim(),
                    artist: cells[2].textContent.trim(),
                    height: cells[3].querySelector('input').value,
                    width: cells[4].querySelector('input').value,
                    description: cells[6].textContent.trim(),
                    type: cells[7].textContent.trim(),
                    unit: unitValue,
                };

                if (image && image.src && image.src.startsWith('data:')) {
                    // Send the base64 data directly
                    rowData.image = image.src;
                }

                data.push(rowData);
            });

            // Create FormData and append artwork data
            const formData = new FormData();
            formData.append('artwork_data', JSON.stringify(data));

            const token = document.querySelector('meta[name="csrf-token"]').content;

            // Simulate progress updates
            const progressInterval = setInterval(() => {
                const currentWidth = parseInt(document.getElementById('submitProgressBar').style.width) || 25;
                if (currentWidth < 90) {
                    const newWidth = Math.min(currentWidth + Math.random() * 10, 90);
                    document.getElementById('submitProgressBar').style.width = newWidth + '%';

                    // Update progress text based on progress
                    if (newWidth < 50) {
                        document.getElementById('submitProgressText').textContent = 'Uploading artwork data...';
                    } else if (newWidth < 75) {
                        document.getElementById('submitProgressText').textContent = 'Processing artwork information...';
                    } else {
                        document.getElementById('submitProgressText').textContent = 'Finalizing artwork creation...';
                    }
                }
            }, 500);

            fetch('/inventory/artworks/add', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': token,
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                // Clear progress interval
                clearInterval(progressInterval);

                // Complete the progress bar
                document.getElementById('submitProgressBar').style.width = '100%';
                document.getElementById('submitProgressText').textContent = 'Artworks saved successfully!';

                // Reset button state
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;

                // Hide modal after a short delay
                setTimeout(() => {
                    progressModal.hide();

                    if (data.success) {
                        if (data.created_count > 0) {
                            // Mark current page as uploaded
                            uploadedPages.add(currentPage);

                            // Check if there are more pages to upload
                            if (currentPage < totalPages) {
                                // Ask if user wants to continue with next page
                                showContinueUploadModal(data.created_count, currentPage, function() {
                                    currentPage++;
                                    displayCurrentPage();
                                    updatePaginationControls();
                                    showPageUploadConfirmation();
                                });
                            } else {
                                // All pages uploaded
                                showSuccessModal('Upload Completed!', `All ${allArtworksData.length} artworks have been uploaded successfully.`);
                                // Optionally reload the page or reset
                                window.location.reload();
                            }
                        } else {
                            alert('No artworks were created. Please check the data and try again.');
                        }
                    } else {
                        alert('Error: ' + (data.message || 'Could not add artwork.'));
                    }
                }, 1000);
            })
            .catch(error => {
                // Clear progress interval
                clearInterval(progressInterval);

                // Reset button state on error
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;

                // Hide modal immediately on error
                progressModal.hide();

                console.error('Error:', error);
                alert('Error saving artworks.');
            });
        }, 50); // Small delay to ensure modal renders first
    }


    // Remove row
    document.querySelectorAll('#artworkTableBody .btn-danger').forEach(btn => {
        btn.onclick = function() { btn.closest('tr').remove(); };
    });


    document.getElementById('imageInput').addEventListener('change', function(event) {
        const files = Array.from(event.target.files);
        if (files.length === 0) return;

        // Show progress bar and hide upload text
        imageUploadText.style.display = 'none';
        imageProgress.style.display = 'block';
        imageFilename.style.display = 'none';

        // Simulate upload progress
        const progressBar = imageProgress.querySelector('.progress-bar-upload');
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress >= 100) {
                progress = 100;
                clearInterval(progressInterval);

                // Hide progress and show file count
                setTimeout(() => {
                    imageProgress.style.display = 'none';
                    imageFilename.style.display = 'block';

                    // Display file count with appropriate text
                    const fileCount = files.length;
                    const fileText = fileCount === 1 ? '1 image uploaded' : `${fileCount} images uploaded`;
                    imageFilename.textContent = fileText;

                    // Store the uploaded files
                    uploadedImageFiles = files;
                    updateGenerateArtworkButtonState(); // Update generate button state after image upload
                }, 300);
            }
            progressBar.style.width = progress + '%';
        }, 100);
    });

    // Add spreadsheet input event listener
    document.getElementById('spreadsheetInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (!file) return;

        // Show progress bar and hide upload text
        spreadUploadText.style.display = 'none';
        spreadProgress.style.display = 'block';
        spreadFilename.style.display = 'none';

        // Simulate upload progress
        const progressBar = document.querySelector('.progress-bar-upload');
        let progress = 0;
        const progressInterval = setInterval(() => {
            progress += Math.random() * 15;
            if (progress >= 100) {
                progress = 100;
                clearInterval(progressInterval);

                // Hide progress and show filename
                setTimeout(() => {
                    spreadProgress.style.display = 'none';
                    spreadFilename.style.display = 'block';
                    spreadFilename.textContent = file.name;

                    // Process the file
                    processSpreadsheetFile(file);
                }, 300);
            }
            progressBar.style.width = progress + '%';
        }, 100);
    });

    function processSpreadsheetFile(file) {
        const reader = new FileReader();
        const fileExtension = file.name.split('.').pop().toLowerCase();

        reader.onload = function(e) {
            let rawData;

            if (fileExtension === 'csv') {
                // Handle CSV files
                const csv = e.target.result;
                const workbook = XLSX.read(csv, { type: 'string' });
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                rawData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
            } else if (fileExtension === 'xlsx' || fileExtension === 'xls') {
                // Handle XLSX/XLS files
                const data = new Uint8Array(e.target.result);
                const workbook = XLSX.read(data, { type: 'array' });
                const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
                rawData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
            } else {
                alert('Unsupported file format. Please upload a .csv, .xlsx, or .xls file.');
                return;
            }

            // Convert to array of objects
            if (rawData.length > 1) { // Check if we have header and at least one data row

                // Get the header row (first row)
                const headers = rawData.find(row => row.length >=6 && row[0] === "Filename");

                // Filter out empty rows and process data rows (skip header)
                const filteredData = rawData.filter(row => row[0] !== "Filename" && row.length >= 6);
                uploadedSpreadsheetData = [];

                // Process each data row
                filteredData.forEach(row => {
                    if (row && row.length >= 6) {
                        const artwork = {};

                        // Map each column to its corresponding header
                        headers.forEach((header, index) => {
                            if (row[index] !== undefined) {
                                // Clean up the header name and use it as property name
                                const cleanHeader = header.toString().trim();
                                artwork[cleanHeader] = row[index] ? row[index].toString() : '';
                            }
                        });

                        // Only add if we have at least a filename
                        if (artwork.Filename || artwork['ImageName'] || artwork['Image Name']) {
                            uploadedSpreadsheetData.push(artwork);
                        }
                    }
                });

                updateGenerateArtworkButtonState(); // Update generate button state after processing spreadsheet
            }
        };

        // Use appropriate read method based on file type
        if (fileExtension === 'csv') {
            reader.readAsText(file);
        } else if (fileExtension === 'xlsx' || fileExtension === 'xls') {
            reader.readAsArrayBuffer(file);
        }
    }

    function handleGenerateArtwork() {
        // Hide the button and show the progress bar
        document.getElementById('generate-artwork-btn').style.display = 'none';
        const progressBar = document.getElementById('artwork-progress-bar');
        progressBar.style.display = 'block';

        // Calculate total artworks - only count unique filenames that have both spreadsheet data and images
        let total = 0;

        if (uploadedSpreadsheetData && uploadedSpreadsheetData.length > 0 && uploadedImageFiles.length > 0) {
            // Get filenames from spreadsheet (now objects with Filename property)
            const spreadsheetFilenames = uploadedSpreadsheetData.map(artwork =>
                artwork.Filename || artwork['ImageName'] || artwork['Image Name']
            ).filter(filename => filename);


            // Get filenames from uploaded images
            const imageFilenames = uploadedImageFiles.map(file => file.name);
            // Count matches
            total = spreadsheetFilenames.filter(filename =>
                imageFilenames.some(imageName =>
                    imageName.toLowerCase() === filename.toLowerCase() ||
                    imageName.toLowerCase().replace(/\.[^/.]+$/, "") === filename.toLowerCase().replace(/\.[^/.]+$/, "")
                )
            ).length;
        }

        // Set processing state
        totalExpectedArtworks = total;
        processedArtworks = 0;
        isProcessingArtworks = true;
        updateSubmitButtonState();

        // If nothing to add, just reset UI and return
        if (total === 0) {
            progressBar.style.display = 'none';
            document.getElementById('generate-artwork-btn').style.display = 'inline-block';
            isProcessingArtworks = false;
            updateSubmitButtonState();
            alert('No matching files found. Please ensure spreadsheet filenames match uploaded image filenames.');
            return;
        }

        let current = 0;
        document.getElementById('artwork-progress-bar-inner').style.width = '0%';
        document.getElementById('artwork-progress-bar-label').innerText = `0/${total} processed`;

        // Simulate progress bar filling up over 1 second
        let interval = setInterval(() => {
            current++;
            let percent = Math.round((current / total) * 100);
            document.getElementById('artwork-progress-bar-inner').style.width = percent + '%';
            document.getElementById('artwork-progress-bar-label').innerText = `${current}/${total} processed`;
            if (current >= total) {
                clearInterval(interval);
                setTimeout(() => {
                    actuallyProcessAllArtworks();
                    progressBar.style.display = 'none';
                    document.getElementById('generate-artwork-btn').style.display = 'inline-block';
                    isProcessingArtworks = false;
                    updateSubmitButtonState();
                }, 200);
            }
        }, 1000 / total);
    }

    function actuallyProcessAllArtworks() {
        const tbody = document.getElementById('artworkTableBody');
        tbody.innerHTML = ''; // Clear previous rows

        if (!uploadedSpreadsheetData || uploadedSpreadsheetData.length === 0 || uploadedImageFiles.length === 0) {
            processedArtworks = 0;
            updateSubmitButtonState();
            return;
        }

        // Create a map of image files by filename (without extension)
        const imageFilesMap = new Map();
        uploadedImageFiles.forEach(file => {
            const filenameWithoutExt = file.name.toLowerCase().replace(/\.[^/.]+$/, "");
            imageFilesMap.set(filenameWithoutExt, file);
        });

        let processedCount = 0;
        const totalToProcess = uploadedSpreadsheetData.length;
        allArtworksData = []; // Reset all artworks data

        // Process spreadsheet data (now array of objects)
        uploadedSpreadsheetData.forEach(artwork => {
            const spreadsheetFilename = artwork.Filename || artwork['ImageName'] || artwork['Image Name'];
            if (!spreadsheetFilename) {
                processedCount++;
                if (processedCount >= totalToProcess) {
                    processedArtworks = processedCount;
                    setupPagination();
                }
                return;
            }

            // Try to match filename (with and without extension)
            const filenameWithoutExt = spreadsheetFilename.toLowerCase().replace(/\.[^/.]+$/, "");
            const matchingImageFile = imageFilesMap.get(filenameWithoutExt);

            if (matchingImageFile) {
                // Create image preview
                const reader = new FileReader();
                reader.onload = function(e) {
                    const artworkData = {
                        imageSrc: e.target.result,
                        filename: spreadsheetFilename,
                        collectionId: getCollectionIdByName(getProperty(artwork, ['Collection']) || ''),
                        title: getProperty(artwork, ['title', 'Title']) || '',
                        artist: getProperty(artwork, ['artist', 'Artist']) || '',
                        height: getProperty(artwork, ['height', 'Height', 'Height (in)']) || '',
                        width: getProperty(artwork, ['width', 'Width', 'Width (in)']) || '',
                        unit: getProperty(artwork, ['unit', 'Unit']) || 'inch',
                        description: getProperty(artwork, ['description', 'Description']) || '',
                        type: getProperty(artwork, ['type', 'Type']) || 'Painting'
                    };

                    allArtworksData.push(artworkData);

                    processedCount++;
                    if (processedCount >= totalToProcess) {
                        processedArtworks = processedCount;
                        setupPagination();
                    }
                };
                reader.readAsDataURL(matchingImageFile);
            } else {
                // No matching image found, but still count as processed
                processedCount++;
                if (processedCount >= totalToProcess) {
                    processedArtworks = processedCount;
                    setupPagination();
                }
            }
        });
    }

    function setupPagination() {
        if (allArtworksData.length === 0) {
            hidePaginationControls();
            return;
        }

        // Calculate total pages
        totalPages = Math.ceil(allArtworksData.length / artworksPerPage);
        currentPage = 1;

        // Show pagination controls
        showPaginationControls();

        // Display first page
        displayCurrentPage();

        // Update submit button state
        updateSubmitButtonState();
    }

    function getCollectionIdByName(collectionName) {
        // Find collection ID by name from the collections data
        const collection = allCollections.find(c => c.name === collectionName);
        return collection ? collection.id : '';
    }

    function handleDeleteArtworks() {
        // Collect selected IDs
        const selectedCheckboxes = document.querySelectorAll('.bulk-select-checkbox:checked');

        const selectedIds = Array.from(selectedCheckboxes)
            .map(cb => cb.value);


        if (selectedIds.length === 0) {
            alert('No items selected for deletion.');
            return;
        }

        // Send AJAX request (adjust URL as needed)
        fetch('/inventory/artworks/bulk-delete', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ ids: selectedIds }),
        })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Optionally: remove rows from DOM or reload
                window.location.reload();
            } else {
                alert('Failed to delete artworks.');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error deleting artworks.');
        });
    }

    // Pagination Functions
    function handlePrevPage() {
        if (currentPage > 1) {
            currentPage--;
            displayCurrentPage();
            updatePaginationControls();
        }
    }

    function handleNextPage() {
        if (currentPage < totalPages) {
            currentPage++;
            displayCurrentPage();
            updatePaginationControls();
        }
    }

    function updatePaginationControls() {
        const prevBtn = document.getElementById('prev-page-btn');
        const nextBtn = document.getElementById('next-page-btn');
        const paginationText = document.getElementById('pagination-text');
        const totalArtworks = document.getElementById('total-artworks');

        // Top pagination controls
        const prevBtnTop = document.getElementById('prev-page-btn-top');
        const nextBtnTop = document.getElementById('next-page-btn-top');
        const paginationTextTop = document.getElementById('pagination-text-top');
        const totalArtworksTop = document.getElementById('total-artworks-top');

        // Update both top and bottom controls
        [prevBtn, prevBtnTop].forEach(btn => btn.disabled = currentPage === 1);
        [nextBtn, nextBtnTop].forEach(btn => btn.disabled = currentPage === totalPages);
        [paginationText, paginationTextTop].forEach(text => text.textContent = `Page ${currentPage} of ${totalPages}`);
        [totalArtworks, totalArtworksTop].forEach(total => total.textContent = allArtworksData.length);

        // Generate page number buttons for both top and bottom
        generatePageNumbers();
        generatePageNumbersTop();

        // Update submit button state
        updateSubmitButtonState();
    }

    function generatePageNumbers() {
        const pageNumbersContainer = document.getElementById('page-numbers');
        pageNumbersContainer.innerHTML = '';

        if (totalPages <= 1) return;

        const maxVisiblePages = 10; // Show max 10 page numbers
        let startPage = 1;
        let endPage = totalPages;

        // Calculate which page numbers to show
        if (totalPages > maxVisiblePages) {
            if (currentPage <= 5) {
                endPage = maxVisiblePages;
            } else if (currentPage >= totalPages - 4) {
                startPage = totalPages - maxVisiblePages + 1;
            } else {
                startPage = currentPage - 4;
                endPage = currentPage + 5;
            }
        }

        // Add first page and ellipsis if needed
        if (startPage > 1) {
            addPageButton(1, pageNumbersContainer);
            if (startPage > 2) {
                addEllipsis(pageNumbersContainer);
            }
        }

        // Add page numbers
        for (let i = startPage; i <= endPage; i++) {
            addPageButton(i, pageNumbersContainer);
        }

        // Add last page and ellipsis if needed
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                addEllipsis(pageNumbersContainer);
            }
            addPageButton(totalPages, pageNumbersContainer);
        }
    }

    function generatePageNumbersTop() {
        const pageNumbersContainer = document.getElementById('page-numbers-top');
        pageNumbersContainer.innerHTML = '';

        if (totalPages <= 1) return;

        const maxVisiblePages = 10; // Show max 10 page numbers
        let startPage = 1;
        let endPage = totalPages;

        // Calculate which page numbers to show
        if (totalPages > maxVisiblePages) {
            if (currentPage <= 5) {
                endPage = maxVisiblePages;
            } else if (currentPage >= totalPages - 4) {
                startPage = totalPages - maxVisiblePages + 1;
            } else {
                startPage = currentPage - 4;
                endPage = currentPage + 5;
            }
        }

        // Add first page and ellipsis if needed
        if (startPage > 1) {
            addPageButton(1, pageNumbersContainer);
            if (startPage > 2) {
                addEllipsis(pageNumbersContainer);
            }
        }

        // Add page numbers
        for (let i = startPage; i <= endPage; i++) {
            addPageButton(i, pageNumbersContainer);
        }

        // Add last page and ellipsis if needed
        if (endPage < totalPages) {
            if (endPage < totalPages - 1) {
                addEllipsis(pageNumbersContainer);
            }
            addPageButton(totalPages, pageNumbersContainer);
        }
    }

    function addPageButton(pageNum, container) {
        const button = document.createElement('button');
        button.className = `btn btn-sm page-number-btn ${pageNum === currentPage ? 'btn-primary' : 'btn-outline-secondary'}`;
        button.textContent = pageNum;
        button.onclick = () => handlePageClick(pageNum);
        container.appendChild(button);
    }

    function addEllipsis(container) {
        const span = document.createElement('span');
        span.className = 'mx-2 text-muted';
        span.textContent = '...';
        container.appendChild(span);
    }

    function handlePageClick(pageNum) {
        if (pageNum !== currentPage && pageNum >= 1 && pageNum <= totalPages) {
            currentPage = pageNum;
            displayCurrentPage();
            updatePaginationControls();
        }
    }

    function displayCurrentPage() {

        const masterCollectionSelect = document.getElementById('masterCollectionHeader');

        // Update masterCollectionSelect based on collection data from allArtworksData
        if (allArtworksData.length > 0) {
            // Get the first artwork's collection ID to determine the collection
            const firstArtworkCollectionId = allArtworksData[0].collectionId;
            
            if (firstArtworkCollectionId) {
                // Find the collection name by ID
                const collection = allCollections.find(c => c.id == firstArtworkCollectionId);
                if (collection) {
                    // Set the master collection dropdown to match the first artwork's collection
                    masterCollectionSelect.value = collection.id;
                }
            }
        }

        const tbody = document.getElementById('artworkTableBody');
        tbody.innerHTML = ''; // Clear current page

        const startIndex = (currentPage - 1) * artworksPerPage;
        const endIndex = Math.min(startIndex + artworksPerPage, allArtworksData.length);
        currentPageArtworks = allArtworksData.slice(startIndex, endIndex);

        // Display current page artworks
        currentPageArtworks.forEach(artwork => {
            const row = document.createElement('tr');
            row.innerHTML = `
                <td><img src="${artwork.imageSrc}" data-filename="${artwork.filename}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;"></td>

                <td contenteditable="true" data-placeholder="Enter title..." class="${!artwork.title ? 'empty-cell' : ''}">${artwork.title || ''}</td>
                <td contenteditable="true" data-placeholder="Enter artist name..." class="${!artwork.artist ? 'empty-cell' : ''}">${artwork.artist || ''}</td>
                <td><input type="number" id="artwork-height" class="form-control ${!artwork.height ? 'empty-cell' : ''}" style="width: 100px; min-width: 60px;" value="${artwork.height || ''}" /></td>
                <td><input type="number" id="artwork-width" class="form-control ${!artwork.width ? 'empty-cell' : ''}" style="width: 100px; min-width: 60px;" value="${artwork.width || ''}" /></td>
                <td>
                    <select class="form-select artwork-unit-select">
                        <option value="inch" ${artwork.unit === 'inch' ? 'selected' : ''}>inch</option>
                        <option value="cm" ${artwork.unit === 'cm' ? 'selected' : ''}>cm</option>
                    </select>
                </td>
                <td contenteditable="true" data-placeholder="Enter artwork description..." class="${!artwork.description ? 'empty-cell' : ''}">${artwork.description || ''}</td>
                <td contenteditable="true" data-placeholder="Enter artwork type..." class="${!artwork.type ? 'empty-cell' : ''}">${artwork.type || ''}</td>
                <td><button class="btn btn-danger btn-sm">Remove</button></td>
            `;
            row.querySelector('button').onclick = function() {
                row.remove();
                updateSubmitButtonState();
            };
            tbody.appendChild(row);

            // Add event listeners for empty-cell class
            const contentEditableCells = row.querySelectorAll('[contenteditable="true"]');
            contentEditableCells.forEach(cell => {
                cell.addEventListener('input', function() {
                    if (this.textContent.trim() !== '') {
                        this.classList.remove('empty-cell');
                    } else {
                        this.classList.add('empty-cell');
                    }
                });
            });

            const inputFields = row.querySelectorAll('input[type="number"]');
            inputFields.forEach(input => {
                input.addEventListener('input', function() {
                    if (this.value.trim() !== '') {
                        this.classList.remove('empty-cell');
                    } else {
                        this.classList.add('empty-cell');
                    }
                });
            });

            const selectFields = row.querySelectorAll('select');
            selectFields.forEach(select => {
                select.addEventListener('change', function() {
                    if (this.value !== '') {
                        this.classList.remove('empty-cell');
                    } else {
                        this.classList.add('empty-cell');
                    }
                });
            });

            // Add unit change event listener for individual unit dropdowns
            const unitSelect = row.querySelector('.artwork-unit-select');
            if (unitSelect) {
                // Set initial previous unit
                unitSelect.dataset.previousUnit = unitSelect.value;

                unitSelect.addEventListener('change', function() {
                //    handleUnitChange(this);
                });
            }
        });
    }

    function showPaginationControls() {
        const paginationControls = document.getElementById('pagination-controls');
        const paginationControlsTop = document.getElementById('pagination-controls-top');
        paginationControls.style.display = 'block';
        paginationControlsTop.style.display = 'block';
        updatePaginationControls();
    }

    function hidePaginationControls() {
        const paginationControls = document.getElementById('pagination-controls');
        const paginationControlsTop = document.getElementById('pagination-controls-top');
        paginationControls.style.display = 'none';
        paginationControlsTop.style.display = 'none';
    }

    function resetPagination() {
        currentPage = 1;
        totalPages = 1;
        allArtworksData = [];
        currentPageArtworks = [];
        uploadedPages.clear();
        hidePaginationControls();
    }

    // Function to show success modal
    function showSuccessModal(title, message) {
        const successModal = new bootstrap.Modal(document.getElementById('successModal'));
        document.getElementById('successModalTitle').textContent = title;
        document.getElementById('successModalMessage').textContent = message;
        successModal.show();
    }

    // Function to show continue upload confirmation modal
    function showContinueUploadModal(createdCount, currentPage, callback) {
        const continueModal = new bootstrap.Modal(document.getElementById('continueUploadModal'));
        document.getElementById('continueUploadTitle').textContent = 'Continue with next page?';
        document.getElementById('continueUploadMessage').textContent = `Successfully uploaded ${createdCount} artworks from page ${currentPage}. Do you want to continue with page ${currentPage + 1}?`;

        // Set up the continue button click handler
        const continueBtn = document.getElementById('continueUploadBtn');
        continueBtn.onclick = function() {
            continueModal.hide();
            if (callback) callback();
        };

        // Set up modal hidden event to handle "No" response
        continueModal._element.addEventListener('hidden.bs.modal', function() {
            // If modal is hidden without clicking continue, it means user chose "No"
            // The callback won't be called, so we just show the success message
          //  showSuccessModal('Upload Completed!', `${createdCount} artworks uploaded from page ${currentPage}.`);
        });

        continueModal.show();
    }

    // Unit conversion function
    function convertUnit(value, fromUnit, toUnit) {
        // First convert to inches (base unit)
        let inchesValue;

        switch (fromUnit) {
            case 'inch':
                inchesValue = value;
                break;
            case 'cm':
                inchesValue = value / 2.54;
                break;
            case 'm':
                inchesValue = value * 39.3701;
                break;
            default:
                inchesValue = value; // Default to no conversion
        }

        // Then convert from inches to target unit
        let result;
        switch (toUnit) {
            case 'inch':
                result = inchesValue;
                break;
            case 'cm':
                result = inchesValue * 2.54;
                break;
            case 'm':
                result = inchesValue / 39.3701;
                break;
            default:
                result = inchesValue;
        }

        // Round to 2 decimal places
        return Math.round(result * 100) / 100;
    }

    // Function to handle individual unit dropdown changes
    function handleUnitChange(unitSelect) {
        const row = unitSelect.closest('tr');
        const heightInput = row.querySelector('input[id="artwork-height"]');
        const widthInput = row.querySelector('input[id="artwork-width"]');

        if (heightInput && widthInput && heightInput.value && widthInput.value) {
            const currentUnit = unitSelect.dataset.previousUnit || unitSelect.value;
            const newUnit = unitSelect.value;

            // Only convert if the unit actually changed
            if (currentUnit !== newUnit) {
                const heightValue = parseFloat(heightInput.value);
                const widthValue = parseFloat(widthInput.value);

                // Convert height
                const convertedHeight = convertUnit(heightValue, currentUnit, newUnit);
                heightInput.value = convertedHeight;

                // Convert width
                const convertedWidth = convertUnit(widthValue, currentUnit, newUnit);
                widthInput.value = convertedWidth;
            }

            // Store the new unit as previous unit for next change
            unitSelect.dataset.previousUnit = newUnit;
        } else {
            // If no values to convert, just update the previous unit
            unitSelect.dataset.previousUnit = unitSelect.value;
        }
    }




$(document).ready(function() {
    console.log('Initializing DataTable with inline editing');


     // Handle both master collection dropdowns
     const masterCollectionDropdowns = [
            document.getElementById('masterCollectionStandalone'),
            document.getElementById('masterCollectionHeader')
        ];

        masterCollectionDropdowns.forEach(dropdown => {
            if (dropdown) {
                dropdown.addEventListener('change', function(event) {
                    const selectedCollectionId = event.target.value;
                    if (selectedCollectionId) {
                        const artworkRows = document.querySelectorAll('#artworkTableBody tr');
                        artworkRows.forEach(row => {
                            const collectionSelect = row.querySelector('.artwork-collection-select');
                            if (collectionSelect) {
                                collectionSelect.value = selectedCollectionId;
                            }
                        });

                        // Sync the other dropdown to the same value
                        masterCollectionDropdowns.forEach(otherDropdown => {
                            if (otherDropdown && otherDropdown !== dropdown) {
                                otherDropdown.value = selectedCollectionId;
                            }
                        });
                    }
                });
            }
        });

        const masterUnitDropdown = document.getElementById('masterUnit');
        if (masterUnitDropdown) {
            masterUnitDropdown.addEventListener('change', function(event) {
                const selectedUnit = event.target.value;
                if (selectedUnit) {
                    const artworkRows = document.querySelectorAll('#artworkTableBody tr');
                    artworkRows.forEach(row => {
                        const unitSelect = row.querySelector('.artwork-unit-select');

                        if (unitSelect) {
                            const currentUnit = unitSelect.value;
                            unitSelect.value = selectedUnit;

                            // Convert values if both inputs exist and have values
                            const heightInput = row.querySelector('input[id="artwork-height"]');
                            const widthInput = row.querySelector('input[id="artwork-width"]');

                            if (heightInput && widthInput && heightInput.value && widthInput.value) {
                                // const heightValue = parseFloat(heightInput.value);
                                // const widthValue = parseFloat(widthInput.value);

                                // // Convert height
                                // const convertedHeight = convertUnit(heightValue, currentUnit, selectedUnit);
                                // heightInput.value = convertedHeight;

                                // // Convert width
                                // const convertedWidth = convertUnit(widthValue, currentUnit, selectedUnit);
                                // widthInput.value = convertedWidth;
                            }

                            // Update the previous unit for this row's unit select
                            unitSelect.dataset.previousUnit = selectedUnit;
                        }
                    });
                }
            });
        }


    // Check if DataTables is available
    if (typeof $.fn.DataTable === 'undefined') {
        console.error('DataTables library not loaded!');
        alert('DataTables library not loaded. Please refresh the page.');
        return;
    }

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
    });

    // Store pending changes to reapply after table reload
    var pendingChanges = {};

    // Function to reapply pending changes after table reload
    function reapplyPendingChanges() {
        if (Object.keys(pendingChanges).length === 0) return;

        table.rows().every(function() {
            var rowData = this.data();
            var rowId = rowData.id;

            if (pendingChanges[rowId]) {
                var changes = pendingChanges[rowId];
                for (var field in changes) {
                    rowData[field] = changes[field];
                }
                this.data(rowData).draw(false);
            }
        });
    }

    // Function to clear pending changes for a specific row or all rows
    function clearPendingChanges(rowId) {
        if (rowId) {
            delete pendingChanges[rowId];
        } else {
            pendingChanges = {};
        }
    }

    // Create DataTable instance
    var table = $('#inventoryTable').DataTable({
        processing: true,
        serverSide: true,
        ajax: {
            url: '{{ route("inventory.data") }}',
            type: 'GET',
            data: function(d) {
                d.collection_id = window.selectedCollectionId || '';
                d.search_type = currentSearchType || 'artwork';
            },
            error: function(xhr, error, thrown) {
                console.error('DataTables AJAX error:', error, thrown);
                console.error('Response:', xhr.responseText);
                alert('Error loading data: ' + error + '. Check console for details.');
            }
        },
        columns: [
            {
                data: null,
                orderable: false,
                render: function(data, type, row) {
                    return '<input type="checkbox" class="form-check-input row-checkbox" data-id="' + row.id + '">';
                }
            },
            {
                data: 'image',
                render: function(data, type, row) {
                    return `<img src="${data}" class="artwork-image" alt="Artwork">`;
                },
                orderable: false,
                searchable: false
            },
            @if(auth()->user()->isSuperAdmin())
            {
                data: 'company',
                render: function(data, type, row) {
                    return `<span class="editable-cell" data-field="company" data-id="${row.id}" data-type="company-select" title="Click to edit" data-bs-toggle="tooltip">${data || ''}</span>`;
                }
            },
            @endif
            {
                data: 'collection',
                render: function(data, type, row) {
                    return `<span class="editable-cell" data-field="collection" data-id="${row.id}" data-type="collection-select" title="Click to edit" data-bs-toggle="tooltip">${data || ''}</span>`;
                }
            },
            {
                data: 'name',
                render: function(data, type, row) {
                    return `<span class="editable-cell" data-field="name" data-id="${row.id}" title="Click to edit" data-bs-toggle="tooltip">${data || ''}</span>`;
                }
            },
            {
                data: 'artist',
                render: function(data, type, row) {
                    return `<span class="editable-cell" data-field="artist" data-id="${row.id}" title="Click to edit" data-bs-toggle="tooltip">${data || ''}</span>`;
                }
            },
            {
                data: 'type',
                render: function(data, type, row) {
                    return `<span class="editable-cell" data-field="type" data-id="${row.id}" title="Click to edit" data-bs-toggle="tooltip">${data || ''}</span>`;
                }
            },
            {
                data: 'height',
                render: function(data, type, row) {
                    return `<span class="editable-cell" data-field="height" data-id="${row.id}" title="Click to edit" data-bs-toggle="tooltip">${data || ''}</span>`;
                }
            },
            {
                data: 'width',
                render: function(data, type, row) {
                    return `<span class="editable-cell" data-field="width" data-id="${row.id}" title="Click to edit" data-bs-toggle="tooltip">${data || ''}</span>`;
                }
            },
            {
                data: 'unit',
                render: function(data, type, row) {
                    return `<span class="editable-cell" data-field="unit" data-id="${row.id}" data-type="select" title="Click to edit" data-bs-toggle="tooltip">${data || ''}</span>`;
                }
            },
            {
                data: 'description',
                render: function(data, type, row) {
                    return `<textarea class="form-control form-control-sm description-input" rows="2" placeholder="Enter description..." data-field="description" data-id="${row.id}" data-type="textarea" title="Click to edit" data-bs-toggle="tooltip">${data || ''}</textarea>`;
                }
            }
        ],

        order: [{{ auth()->user()->isSuperAdmin() ? '10' : '9' }}, 'desc'], // Sort by description desc
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        responsive: true,
        search: {
            caseInsensitive: true
        },
        drawCallback: function() {
            // Reinitialize tooltips after table redraw
            $('[data-bs-toggle="tooltip"]').tooltip();

            // Add click handlers to artwork images
            $('.artwork-image').off('click').on('click', function(e) {
                e.preventDefault();
                e.stopPropagation();

                // Get the row data from DataTable
                const row = table.row($(this).closest('tr'));
                const rowData = row.data();

                if (rowData) {
                    showArtworkDetailModal(rowData);
                }
            });

            // Add event handlers for description textareas
            $('.description-input').off('blur keypress').on('blur keypress', function(e) {
                if (e.type === 'keypress') {
                    // Use Ctrl+Enter to save for textareas
                    if (e.which !== 13 || !e.ctrlKey) return;
                }

                const $textarea = $(this);
                const field = $textarea.data('field');
                const id = $textarea.data('id');
                const newValue = $textarea.val();

                console.log('Saving description for ID:', id, 'new value:', newValue);

                // Prepare data for saving
                let saveData = {
                    _token: '{{ csrf_token() }}',
                    description: newValue
                };

                // Save the value
                $.ajax({
                    url: `/inventory/${id}`,
                    type: 'PUT',
                    data: saveData,
                    success: function(response) {
                        console.log('Description update successful');

                        // Update the DataTable row data with the new value
                        const row = table.row($textarea.closest('tr'));
                        const rowData = row.data();
                        if (rowData) {
                            // Store the change in pendingChanges for potential reapplication after reload
                            const rowId = rowData.id;
                            if (!pendingChanges[rowId]) {
                                pendingChanges[rowId] = {};
                            }
                            rowData.description = newValue;
                            pendingChanges[rowId].description = newValue;
                            // Update the row data in the DataTable
                            row.data(rowData).draw(false);
                        }
                    },
                    error: function(xhr) {
                        console.error('Description update failed:', xhr.responseText);
                        alert('Error updating description: ' + (xhr.responseJSON?.message || 'Unknown error'));
                    }
                });
            });

            // Handle escape key to cancel (optional - could revert to original value)
            $('.description-input').off('keydown').on('keydown', function(e) {
                if (e.which === 27) { // Escape key
                    // Optionally revert to original value or just blur
                    $(this).blur();
                }
            });
        },
        dom: 'rtip', // Hide default search and length controls, keep pagination
        initComplete: function(settings, json) {
            console.log('DataTable initialization complete');
            console.log('Data received:', json);
        }
    });

    console.log('DataTable created successfully');

    // Update left collections count after each DataTables AJAX load (including reloads)
    table.on('xhr.dt', function (e, settings, json) {
        if (!json) return;

        var newCount = typeof json.recordsTotal === 'number' ? json.recordsTotal : 0;

        // Update the visible count in the dropdown button (current selection)
        var dropdownButton = document.querySelector('.collections-dropdown .btn');
        if (dropdownButton) {
            var countEl = dropdownButton.querySelector('.text-start small.text-muted');
            if (countEl) {
                countEl.textContent = newCount + ' items';
            }
        }

        // Also sync the corresponding item inside the dropdown list, if present
        if (window.selectedCollectionId) {
            var menuItems = document.querySelectorAll('.collections-dropdown .dropdown-menu .dropdown-item');
            menuItems.forEach(function (item) {
                var onclickAttr = item.getAttribute('onclick') || '';
                if (onclickAttr.indexOf("selectCollection('" + window.selectedCollectionId + "'") !== -1) {
                    var countSpan = item.querySelector('small.text-muted');
                    if (countSpan) countSpan.textContent = newCount + ' items';

                    var nameEl = item.querySelector('.fw-bold');
                    var name = nameEl ? nameEl.textContent.trim() : '';
                    var img = item.querySelector('img');
                    var thumb = img ? img.getAttribute('src') : '';
                    item.setAttribute('onclick', "selectCollection('" + window.selectedCollectionId + "', '" + name.replace(/'/g, "\\'") + "', '" + newCount + "', '" + thumb.replace(/'/g, "\\'") + "')");
                }
            });
        }
    });

    // Initialize Bootstrap tooltips and ensure they hide on mouseleave/blur
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        var tip = bootstrap.Tooltip.getOrCreateInstance(tooltipTriggerEl, { trigger: 'hover focus' });
        tooltipTriggerEl.addEventListener('mouseleave', function() { tip.hide(); });
        tooltipTriggerEl.addEventListener('blur', function() { tip.hide(); });
        return tip;
    });

    // Search toggle functionality
    var currentSearchType = 'artwork';

    $('.search-toggle-btn').on('click', function() {
        var searchType = $(this).data('type');

        // Update active button
        $('.search-toggle-btn').removeClass('active');
        $(this).addClass('active');

        // Update current search type
        currentSearchType = searchType;

        // Update search placeholder
        var placeholder = searchType === 'artwork' ? 'Search artworks...' : 'Search collections...';
        $('#tableSearch').attr('placeholder', placeholder);

        // Clear current search and reload table
        $('#tableSearch').val('');
        table.ajax.reload(function() {
            // Reapply any pending changes after reload
            reapplyPendingChanges();
        }, false);

        console.log('Search type changed to:', searchType);
    });

    // Custom search functionality
    $('#tableSearch').on('keyup', function() {
        var searchValue = this.value;

        // Apply search based on current type
        if (currentSearchType === 'artwork') {
            // Search in artwork table
            table.search(searchValue).draw();
        } else {
            // For collections, we might need to implement different logic
            // For now, still search in the main table but could be extended
            table.search(searchValue).draw();
        }
    });

    // Custom length functionality
    $('#tableLength').on('change', function() {
        table.page.len(parseInt(this.value)).draw();
    });

    // Bulk edit functionality
    var selectedRows = new Set();
    var bulkEditControls = $('#bulkEditControls');
    var bulkEditBtn = $('#bulkEditBtn');
    var selectedCount = $('#selectedCount');
    var bulkEditModal = $('#bulkEditModal');
    var bulkUpdateBtn = $('#bulkUpdateBtn');
    var selectAllCheckbox = $('#selectAll');

    // Handle individual checkbox changes
    $(document).on('change', '.row-checkbox', function() {
        var rowId = $(this).data('id');
        var isChecked = $(this).is(':checked');
        var row = $(this).closest('tr');

        if (isChecked) {
            selectedRows.add(rowId);
            row.addClass('selected-row');
        } else {
            selectedRows.delete(rowId);
            row.removeClass('selected-row');
        }

        updateBulkEditUI();
        updateSelectAllState();
    });

    // Make entire row clickable for checkbox selection
    $(document).on('click', '#inventoryTable tbody tr', function(e) {
        // Don't trigger if clicking on editable cells or other interactive elements
        if ($(e.target).hasClass('editable-cell') ||
            $(e.target).closest('.editable-cell').length > 0 ||
            $(e.target).is('input, select, button, a, textarea') ||
            $(e.target).closest('input, select, button, a, textarea').length > 0) {
            return;
        }

        var checkbox = $(this).find('.row-checkbox');
        var rowId = checkbox.data('id');
        var row = $(this);

        if (checkbox.length > 0) {
            checkbox.prop('checked', !checkbox.prop('checked'));

            if (checkbox.prop('checked')) {
                selectedRows.add(rowId);
                row.addClass('selected-row');
            } else {
                selectedRows.delete(rowId);
                row.removeClass('selected-row');
            }

            updateBulkEditUI();
            updateSelectAllState();
        }
    });

    // Handle select all checkbox
    selectAllCheckbox.on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);

        if (isChecked) {
            $('.row-checkbox').each(function() {
                selectedRows.add($(this).data('id'));
                $(this).closest('tr').addClass('selected-row');
            });
        } else {
            selectedRows.clear();
            $('#inventoryTable tbody tr').removeClass('selected-row');
        }

        updateBulkEditUI();
    });

    // Update bulk edit UI visibility
    function updateBulkEditUI() {
        var count = selectedRows.size;
        selectedCount.text(count + ' item' + (count !== 1 ? 's' : '') + ' selected');

        if (count > 0) {
            bulkEditControls.show();
            // Reinitialize tooltips for dynamically shown elements
            var newTooltipTriggerList = [].slice.call(document.querySelectorAll('#bulkEditBtn[data-bs-toggle="tooltip"]'));
            newTooltipTriggerList.forEach(function (tooltipTriggerEl) {
                // Dispose existing tooltip if any
                var existingTooltip = bootstrap.Tooltip.getInstance(tooltipTriggerEl);
                if (existingTooltip) {
                    existingTooltip.dispose();
                }
                // Create new tooltip
                var tip = bootstrap.Tooltip.getOrCreateInstance(tooltipTriggerEl, { trigger: 'hover focus' });
                tooltipTriggerEl.addEventListener('mouseleave', function() { tip.hide(); });
                tooltipTriggerEl.addEventListener('blur', function() { tip.hide(); });
            });
        } else {
            bulkEditControls.hide();
        }
    }

    // Update select all checkbox state
    function updateSelectAllState() {
        var totalCheckboxes = $('.row-checkbox').length;
        var checkedCheckboxes = $('.row-checkbox:checked').length;

        if (checkedCheckboxes === 0) {
            selectAllCheckbox.prop('indeterminate', false).prop('checked', false);
        } else if (checkedCheckboxes === totalCheckboxes) {
            selectAllCheckbox.prop('indeterminate', false).prop('checked', true);
        } else {
            selectAllCheckbox.prop('indeterminate', true);
        }
    }

    // Open bulk edit modal
    bulkEditBtn.on('click', function() {
        if (selectedRows.size === 0) {
            alert('Please select at least one item to edit.');
            return;
        }
        bulkEditModal.modal('show');
    });

    // Handle bulk update
    bulkUpdateBtn.on('click', function() {
        var formData = $('#bulkEditForm').serialize();
        var selectedIds = Array.from(selectedRows);

        if (selectedIds.length === 0) {
            alert('No items selected.');
            return;
        }

        // Show loading state
        bulkUpdateBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Updating...');

        $.ajax({
            url: '{{ route("inventory.bulk-update") }}',
            type: 'POST',
            data: {
                ids: selectedIds,
                _token: '{{ csrf_token() }}',
                ...Object.fromEntries(new URLSearchParams(formData))
            },
            success: function(response) {
                if (response.success) {
                    alert('Successfully updated ' + response.updated_count + ' item(s).');
                    bulkEditModal.modal('hide');
                    // Clear pending changes since server data is now updated
                    clearPendingChanges();
                    table.ajax.reload(function() {
                        // No need to reapply changes since server data is fresh
                    }, false);
                    selectedRows.clear();
                    updateBulkEditUI();
                    updateSelectAllState();
                    $('#bulkEditForm')[0].reset();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error updating items: ' + (xhr.responseJSON?.message || 'Unknown error'));
            },
            complete: function() {
                bulkUpdateBtn.prop('disabled', false).html('Update Selected Items');
            }
        });
    });

    // Clear selection when modal is closed
    bulkEditModal.on('hidden.bs.modal', function() {
        $('#bulkEditForm')[0].reset();
    });

    // Inline editing functionality
    $(document).on('click', '.editable-cell', function(e) {
        e.preventDefault();
        e.stopPropagation();

        const $cell = $(this);
        const field = $cell.data('field');
        const id = $cell.data('id');
        const type = $cell.data('type');
        const currentValue = $cell.text().trim();

        console.log('Editing field:', field, 'for ID:', id, 'current value:', currentValue);

        // Don't edit if already editing
        if ($cell.hasClass('editing')) return;

        $cell.addClass('editing');

        let input;
        if (type === 'select') {
            // Create select dropdown for unit field
            input = $(`<select class="inline-edit-select">
                <option value="cm" ${currentValue === 'cm' ? 'selected' : ''}>cm</option>
                <option value="inch" ${currentValue === 'inch' ? 'selected' : ''}>inch</option>
            </select>`);
        } else if (type === 'collection-select') {
            // Create select dropdown for collection field with filtered collections
            let options = '';
            const filteredCollections = getFilteredCollections($cell.closest('tr')[0]);
            if (filteredCollections.length > 0) {
                filteredCollections.forEach(function(collection) {
                        const isSelected = currentValue === collection.name ? 'selected' : '';
                        options += `<option value="${collection.id}" ${isSelected}>${collection.name}</option>`;
                    });
            }else{
                options = `<option value="">No collections available</option>`;
            }
            input = $(`<select class="inline-edit-select">${options}</select>`);
        } else if (type === 'company-select') {
            // Create select dropdown for company field
            let options = '';
            allCompanies.forEach(function(company) {
                const isSelected = currentValue === company.name ? 'selected' : '';
                options += `<option value="${company.id}" ${isSelected}>${company.name}</option>`;
            });
            input = $(`<select class="inline-edit-select">${options}</select>`);
        } else if (type === 'textarea') {
            // Create textarea for description field
            input = $(`<textarea class="form-control form-control-sm description-input" rows="2" placeholder="Enter description..." data-field="description" data-id="${id}" data-type="textarea" title="Click to edit" data-bs-toggle="tooltip">${currentValue}</textarea>`);
        } else {
            // Create input field
            const inputType = (field === 'height' || field === 'width') ? 'number' : 'text';
            input = $(`<input type="${inputType}" class="inline-edit-input" value="${currentValue}">`);
        }

        // Replace cell content with input
        $cell.html(input);
        input.focus();

        // Select text for input fields, but not for textareas
        if (type !== 'textarea') {
            input.select();
        }

        // Handle save on blur or enter (Ctrl+Enter for textareas)
        input.on('blur keypress', function(e) {
            if (e.type === 'keypress') {
                if (type === 'textarea') {
                    // For textareas, use Ctrl+Enter to save
                    if (e.which !== 13 || !e.ctrlKey) return;
                } else {
                    // For other inputs, use Enter to save
                    if (e.which !== 13) return;
                }
            }

            const newValue = $(this).val();
            console.log('Saving new value:', newValue);

            // Prepare data for saving
            let saveData = {
                _token: '{{ csrf_token() }}'
            };

            if (field === 'collection') {
                saveData.artwork_collection_id = newValue;
            } else if (field === 'company') {
                saveData.company_id = newValue;
            } else {
                saveData[field] = newValue;
            }

            // Save the value
            $.ajax({
                url: `/inventory/${id}`,
                type: 'PUT',
                data: saveData,
                success: function(response) {
                    console.log('Update successful');

                    // For collection and company fields, display the name instead of ID
                    let displayValue = newValue;
                    if (field === 'collection') {
                        if (newValue) {
                            const selectedCollection = allCollections.find(c => c.id == newValue);
                            displayValue = selectedCollection ? selectedCollection.name : '';
                        } else {
                            displayValue = '';
                        }
                    } else if (field === 'company') {
                        if (newValue) {
                            const selectedCompany = allCompanies.find(c => c.id == newValue);
                            displayValue = selectedCompany ? selectedCompany.name : '';
                        } else {
                            displayValue = '';
                        }
                    }

                    $cell.removeClass('editing').text(displayValue);

                    // Update the DataTable row data with the new value
                    const row = table.row($cell.closest('tr'));
                    const rowData = row.data();
                    if (rowData) {
                        // Store the change in pendingChanges for potential reapplication after reload
                        const rowId = rowData.id;
                        if (!pendingChanges[rowId]) {
                            pendingChanges[rowId] = {};
                        }

                        // Update the specific field in the row data
                        if (field === 'collection') {
                            rowData.collection = displayValue;
                            pendingChanges[rowId].collection = displayValue;
                        } else if (field === 'company') {
                            rowData.company = displayValue;
                            pendingChanges[rowId].company = displayValue;
                        } else {
                            rowData[field] = newValue;
                            pendingChanges[rowId][field] = newValue;
                        }
                        // Update the row data in the DataTable
                        row.data(rowData).draw(false);
                    }
                },
                error: function(xhr) {
                    console.error('Update failed:', xhr.responseText);
                    $cell.removeClass('editing').text(currentValue);
                    alert('Error updating field: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        });

        // Handle escape key to cancel
        input.on('keydown', function(e) {
            if (e.which === 27) { // Escape key
                $cell.removeClass('editing').text(currentValue);
            }
        });
    });

    // Collection selection function
    window.selectCollection = function(collectionId, collectionName, itemCount, thumbnailUrl = null) {
        window.selectedCollectionId = collectionId;

        // Update the dropdown button content
        const dropdownButton = document.querySelector('.dropdown button');
        const imageContainer = dropdownButton.querySelector('.d-flex.align-items-center');

        if (collectionId) {
            // Update the image and text for selected collection
            if (thumbnailUrl) {
                imageContainer.innerHTML = `
                    <img src="${thumbnailUrl}" alt="${collectionName}" class="me-3" style="width: 18px; height: 18px; object-fit: cover; border-radius: 0;">
                    <div class="text-start">
                        <div id="selectedCollectionName" style="font-weigh : 500; color: #495057; font-size : 14px">${collectionName}</div>
                        <small class="text-muted">${itemCount} items</small>
                    </div>
                `;
            } else {
                imageContainer.innerHTML = `
                    <i class="fas fa-image me-3" style="color: #6c757d; font-size: 18px;"></i>
                    <div class="text-start">
                        <div id="selectedCollectionName" style="font-weigh : 500; color: #495057; font-size : 14px">${collectionName}</div>
                        <small class="text-muted">${itemCount} items</small>
                    </div>
                `;
            }
        } else {
            // Update for "All Collections"
            imageContainer.innerHTML = `
                <i class="fas fa-image me-3" style="color: #6c757d; font-size: 18px;"></i>
                <div class="text-start">
                    <div id="selectedCollectionName" style="font-weigh : 500; color: #495057; font-size : 14px">All Collections</div>
                    <small class="text-muted">${itemCount} items</small>
                </div>
            `;
        }

        // Reload the DataTable with the new filter
        table.ajax.reload(function() {
            // Reapply any pending changes after reload
            reapplyPendingChanges();
        }, false); // false = don't reset paging

        // Show/hide edit/delete buttons
        const editCollectionBtn = document.getElementById('editCollectionBtn');
        const deleteCollectionBtn = document.getElementById('deleteCollectionBtn');
        if (editCollectionBtn) {
            editCollectionBtn.style.display = collectionId ? 'flex' : 'none';
        }
        if (deleteCollectionBtn) {
            deleteCollectionBtn.style.display = collectionId ? 'flex' : 'none';
        }
    };

    // Function to filter collections by company (for super admin only)
    window.filterCollectionsByCompany = function() {
        const companyFilter = document.getElementById('companyFilter');
        const selectedCompanyId = companyFilter ? companyFilter.value : '';
        const collectionsDropdown = document.getElementById('collectionsDropdownMenu');

        if (!collectionsDropdown) return;


        // Reset to "All Collections" when company changes
        const allCollectionsItem = collectionsDropdown.querySelector('li:first-child a');
        if (allCollectionsItem) {
            allCollectionsItem.click(); // Trigger the "All Collections" selection
        }

        // If no company selected, show all collections
        if (selectedCompanyId === '') {
            // Show all collection items
            const collectionItems = collectionsDropdown.querySelectorAll('li[data-company-id]');
            collectionItems.forEach(item => {
                item.style.display = 'block';
            });

            // Reset to original total
            const allCollectionsItem = collectionsDropdown.querySelector('li:first-child small.text-muted');
            if (allCollectionsItem) {
                allCollectionsItem.textContent = '{{ $totalItems }} items';
            }
            return;
        }

        // Make AJAX call to get filtered collections
        fetch('{{ route("inventory.collections.by-company") }}?company_id=' + selectedCompanyId)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Hide all collection items first
                const collectionItems = collectionsDropdown.querySelectorAll('li[data-company-id]');
                collectionItems.forEach(item => {
                    item.style.display = 'none';
                });

                // Show only collections for the selected company
                data.collections.forEach(collection => {
                    const items = collectionsDropdown.querySelectorAll(`li[data-company-id="${collection.company_id}"]`);
                    items.forEach(item => {
                        item.style.display = 'block';
                    });
                });

                // Update the "All Collections" count
                const totalItems = data.collections.reduce((sum, collection) => {
                    return sum + (collection.artworks_count || 0);
                }, 0);

                const allCollectionsItem = collectionsDropdown.querySelector('li:first-child small.text-muted');
                if (allCollectionsItem) {
                    allCollectionsItem.textContent = `${totalItems} items`;
                }

                // Update the dropdown button's item count
                const dropdownButton = collectionsDropdown.closest('.dropdown').querySelector('button[data-bs-toggle="dropdown"]');
                if (dropdownButton) {
                    const buttonTextElement = dropdownButton.querySelector('.text-start small.text-muted');
                    if (buttonTextElement) {
                        buttonTextElement.textContent = `${totalItems} items`;
                    }
                }


            }
        })
        .catch(error => {
            console.error('Error filtering collections:', error);
        });
    };

            // Edit collection function
    window.editCollection = function() {
        const selectedCollectionId = window.selectedCollectionId;
        if (selectedCollectionId) {
            // Find the collection data
            const collection = allCollections.find(c => c.id == selectedCollectionId);
            if (collection) {
                // Set edit mode
                isEditMode = true;
                editingCollectionId = selectedCollectionId;

                // Update modal title for editing
                document.getElementById('addCollectionModalLabel').textContent = 'Edit Collection';

                // Change button text to "Update"
                document.getElementById('saveCollectionBtn').textContent = 'Update';

                // Populate the edit modal with collection data
                document.getElementById('collectionName').value = collection.name;
                document.getElementById('collectionCompany').value = collection.company_id || '';

                // Show current thumbnail if it exists
                if (collection.thumbnail_url) {
                    const uploadBox = document.getElementById('collectionImageUploadBox');
                    const img = document.createElement('img');
                    img.src = collection.thumbnail_url;
                    img.className = 'img-preview';
                    img.style.cursor = 'pointer';
                    img.title = 'Click to replace image';
                    img.crossOrigin = 'anonymous';

                    // Clear the upload box and show the current image
                    uploadBox.innerHTML = '';
                    uploadBox.style.backgroundColor = 'grey';
                    uploadBox.appendChild(img);

                    // Re-add the file input and overlay
                    const fileInput = document.createElement('input');
                    fileInput.type = 'file';
                    fileInput.className = 'image-input';
                    fileInput.id = 'collectionThumbnail';
                    fileInput.name = 'thumbnail';
                    fileInput.accept = 'image/*';
                    fileInput.style.display = 'none';
                    uploadBox.appendChild(fileInput);

                    const overlay = document.createElement('div');
                    overlay.className = 'overlay';
                    overlay.textContent = 'Click to replace image';
                    uploadBox.appendChild(overlay);

                    // Re-attach event listeners
                    fileInput.addEventListener('change', (event) => {
                        const file = event.target.files[0];
                        if (file) {
                            handleCollectionImageFile(file, uploadBox, fileInput, document.getElementById('collectionImageName'));
                        }
                    });

                    // Add click event to the preview image
                    img.addEventListener('click', (e) => {
                        e.stopPropagation();
                        fileInput.click();
                    });
                }

                // Show the edit modal
                $('#addCollectionModal').modal('show');
            }
        }
    }

    // Delete collection function
    window.deleteCollection = function() {
        const collectionId = window.selectedCollectionId;
        if (!collectionId) {
            alert('Please select a collection to delete.');
            return;
        }

        // Get collection data
        const collectionData = @json($collections->keyBy('id'));
        const collection = collectionData[collectionId];

        if (!collection) {
            alert('Collection not found.');
            return;
        }

        if (confirm(`Are you sure you want to delete the collection "${collection.name}"? This action cannot be undone.`)) {
            // Delete collection via AJAX
            $.ajax({
                url: `/inventory/collections/${collectionId}/delete`,
                type: 'DELETE',
                data: {
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Collection deleted successfully!');
                        location.reload(); // Reload to update the collections list
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error deleting collection: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        }
    };

    // Inline row editing functionality
    let newRowCounter = 0;
    let pendingImages = [];

    // Add single artwork row - main button click
    $('#addArtworkBtn').on('click', function() {
        addNewArtworkRow();
    });

    // Add multiple artwork rows - show modal
    $('#addMultipleArtworksBtn').on('click', function(e) {
        e.preventDefault();
        $('#multipleArtworkModal').modal('show');
    });

    // Handle multiple artwork creation
    $('#createMultipleBtn').on('click', function() {
        const numberOfRows = parseInt($('#numberOfRows').val()) || 2;
        const prefillData = {
            collection: $('#prefillCollection').val(),
            @if(auth()->user()->isSuperAdmin())
            company: $('#prefillCompany').val(),
            @endif
            artist: $('#prefillArtist').val(),
            height: $('#prefillHeight').val(),
            width: $('#prefillWidth').val(),
            unit: $('#prefillUnit').val(),
            title: $('#prefillTitle').val(),
            description: $('#prefillDescription').val(),
            type: $('#prefillType').val()
        };


        // Create multiple rows with pre-filled data
        for (let i = 0; i < numberOfRows; i++) {
            addNewArtworkRowWithPrefill(prefillData, i + 1);
        }

        // Show multiple drop zone if more than 1 row
        if (numberOfRows > 1) {
            showMultipleDropZone();
        }

        // Close modal and reset form
        $('#multipleArtworkModal').modal('hide');
        $('#multipleArtworkForm')[0].reset();
        $('#numberOfRows').val(2); // Reset to default
    });


    function handleOpenCollectionModal() {
        // Reset edit mode
        isEditMode = false;
        editingCollectionId = null;

        // Reset modal title for adding new collection
        document.getElementById('addCollectionModalLabel').textContent = 'Add new collection';

        // Reset button text to "Save"
        document.getElementById('saveCollectionBtn').textContent = 'Save';

        // Reset form fields
        document.getElementById('collectionName').value = '';
        document.getElementById('collectionCompany').value = '';

        // Reset image upload box to original state
        const uploadBox = document.getElementById('collectionImageUploadBox');
        uploadBox.innerHTML = `
            <input type="file" class="image-input" id="collectionThumbnail" name="thumbnail" accept="image/*">
            <span>Click or drag & drop to add image</span>
            <div class="overlay">Click to replace image</div>
        `;
        uploadBox.style.backgroundColor = '';

        // Clear image name
        document.getElementById('collectionImageName').textContent = '';

        // Re-attach event listeners to the new file input
        const newFileInput = uploadBox.querySelector('#collectionThumbnail');
        newFileInput.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                handleCollectionImageFile(file, uploadBox, newFileInput, document.getElementById('collectionImageName'));
            }
        });

        $('#addCollectionModal').modal('show');
    }



    function addNewArtworkRow() {
        const template = document.getElementById('newArtworkRowTemplate');
        const newRow = template.content.cloneNode(true);
        const tempId = 'temp_' + Date.now() + '_' + (++newRowCounter);

        newRow.querySelector('.new-artwork-row').setAttribute('data-temp-id', tempId);

        // Insert at the beginning of tbody
        const tbody = document.querySelector('#inventoryTable tbody');
        tbody.insertBefore(newRow, tbody.firstChild);

        // Update collection dropdown with filtered collections
        const rowElement = document.querySelector(`[data-temp-id="${tempId}"]`);
        updateCollectionDropdownForRow(rowElement);

        // Add company change handler for super admin
        @if(auth()->user()->isSuperAdmin())
        const companySelect = rowElement.querySelector('.company-select');
        if (companySelect) {
            companySelect.addEventListener('change', function() {
                updateCollectionDropdownForRow(rowElement);
            });
        }
        @endif

        // Initialize drag and drop for this row
        initializeRowDragDrop(tempId);

        // Focus on title input
        setTimeout(() => {
            const titleInput = document.querySelector(`[data-temp-id="${tempId}"] .title-input`);
            if (titleInput) titleInput.focus();
        }, 100);

        // Update bulk controls
        updateBulkNewItemControls();
    }

    function addNewArtworkRowWithPrefill(prefillData, rowNumber) {
        const template = document.getElementById('newArtworkRowTemplate');
        const newRow = template.content.cloneNode(true);
        const tempId = 'temp_' + Date.now() + '_' + (++newRowCounter);

        newRow.querySelector('.new-artwork-row').setAttribute('data-temp-id', tempId);

        // Insert at the beginning of tbody
        const tbody = document.querySelector('#inventoryTable tbody');
        tbody.insertBefore(newRow, tbody.firstChild);

        // Pre-fill the form fields
        const rowElement = document.querySelector(`[data-temp-id="${tempId}"]`);

        // Handle company selection if user is super admin
        @if(auth()->user()->isSuperAdmin())
        if (prefillData.company) {
            const companySelect = rowElement.querySelector('.company-select');
            if (companySelect) {
                companySelect.value = prefillData.company;
            }
        }
        @endif

        // Update collection dropdown with filtered collections
        updateCollectionDropdownForRow(rowElement);

        // Add company change handler for super admin
        @if(auth()->user()->isSuperAdmin())
        const companySelect = rowElement.querySelector('.company-select');
        if (companySelect) {
            companySelect.addEventListener('change', function() {
                updateCollectionDropdownForRow(rowElement);
            });
        }
        @endif

        if (prefillData.collection) {
            // Find the collection select (not the company select)
            const collectionSelects = rowElement.querySelectorAll('.collection-select');
            // The collection select is the last one (after company select if it exists)
            const collectionSelect = collectionSelects[collectionSelects.length - 1];
            if (collectionSelect) {
                collectionSelect.value = prefillData.collection;
            }
        }

        if (prefillData.artist) {
            const artistInput = rowElement.querySelector('.artist-input');
            if (artistInput) artistInput.value = prefillData.artist;
        }

        if (prefillData.height) {
            const heightInput = rowElement.querySelector('.height-input');
            if (heightInput) heightInput.value = prefillData.height;
        }

        if (prefillData.width) {
            const widthInput = rowElement.querySelector('.width-input');
            if (widthInput) widthInput.value = prefillData.width;
        }

        if (prefillData.unit) {
            const unitSelect = rowElement.querySelector('.unit-select');
            if (unitSelect) unitSelect.value = prefillData.unit;
        }

        if (prefillData.title) {
            const titleInput = rowElement.querySelector('.title-input');
            if (titleInput) {
                // If multiple rows, append row number to title
                const titleValue = prefillData.title + (rowNumber > 1 ? ` ${rowNumber}` : '');
                titleInput.value = titleValue;
            }
        }

        if (prefillData.type) {
            const typeInput = rowElement.querySelector('.type-input');
            if (typeInput) typeInput.value = prefillData.type;
        }

        if(prefillData.description) {
            const descriptionInput = rowElement.querySelector('.description-input');
            if (descriptionInput) descriptionInput.value = prefillData.description;
        }

        // Initialize drag and drop for this row
        initializeRowDragDrop(tempId);

        // Focus on title input for the first row
        if (rowNumber === 1) {
            setTimeout(() => {
                const titleInput = document.querySelector(`[data-temp-id="${tempId}"] .title-input`);
                if (titleInput) titleInput.focus();
            }, 100);
        }

        // Update bulk controls
        updateBulkNewItemControls();
    }

    function showMultipleDropZone() {
        // Create multiple drop zone if it doesn't exist
        if (!document.getElementById('multipleDropZone')) {
            const dropZone = document.createElement('div');
            dropZone.id = 'multipleDropZone';
            dropZone.className = 'multiple-drop-zone';
            dropZone.innerHTML = `
                <div class="d-flex align-items-center justify-content-center">
                    <i class="fas fa-cloud-upload-alt me-2" style="font-size: 24px; color: #099F9A;"></i>
                    <div>
                        <h6 class="mb-1">Drag and drop multiple images</h6>
                        <small class="text-muted">Drop 2 or more images to auto-fill rows</small>
                    </div>
                </div>
            `;

            // Insert before the table
            const table = document.querySelector('#inventoryTable');
            table.parentNode.insertBefore(dropZone, table);

            // Initialize multiple drop zone
            initializeMultipleDropZone();
        }
    }

    function initializeRowDragDrop(tempId) {
        const row = document.querySelector(`[data-temp-id="${tempId}"]`);
        const dropArea = row.querySelector('.drag-drop-area');
        const fileInput = row.querySelector('.image-upload-input');

        // Click to upload
        dropArea.addEventListener('click', () => fileInput.click());

        // File input change
        fileInput.addEventListener('change', (e) => {
            handleImageUpload(e.target.files, tempId);
        });

        // Drag and drop events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropArea.addEventListener(eventName, highlight, false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropArea.addEventListener(eventName, unhighlight, false);
        });

        dropArea.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleImageUpload(files, tempId);
        });
    }

    function initializeMultipleDropZone() {
        const dropZone = document.getElementById('multipleDropZone');
        const fileInput = document.createElement('input');
        fileInput.type = 'file';
        fileInput.multiple = true;
        fileInput.accept = 'image/*';
        fileInput.style.display = 'none';
        dropZone.appendChild(fileInput);

        // Click to upload
        dropZone.addEventListener('click', () => fileInput.click());

        // File input change
        fileInput.addEventListener('change', (e) => {
            handleMultipleImageUpload(e.target.files);
        });

        // Drag and drop events
        ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, preventDefaults, false);
        });

        ['dragenter', 'dragover'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.add('drag-over'), false);
        });

        ['dragleave', 'drop'].forEach(eventName => {
            dropZone.addEventListener(eventName, () => dropZone.classList.remove('drag-over'), false);
        });

        dropZone.addEventListener('drop', (e) => {
            const dt = e.dataTransfer;
            const files = dt.files;
            handleMultipleImageUpload(files);
        });
    }

    function preventDefaults(e) {
        e.preventDefault();
        e.stopPropagation();
    }

    function highlight(e) {
        e.currentTarget.classList.add('drag-over');
    }

    function unhighlight(e) {
        e.currentTarget.classList.remove('drag-over');
    }

    function handleImageUpload(files, tempId) {
        if (files.length > 0) {
            const file = files[0];
            const reader = new FileReader();
            reader.onload = (e) => {
                const row = document.querySelector(`[data-temp-id="${tempId}"]`);
                const dropArea = row.querySelector('.drag-drop-area');
                dropArea.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                dropArea.classList.add('has-image');

                // Populate title with filename (without extension)
                const titleInput = row.querySelector('.title-input');
                if (titleInput) {
                    const filenameWithoutExt = file.name.replace(/\.[^/.]+$/, "");
                    titleInput.value = filenameWithoutExt;
                }
            };
            reader.readAsDataURL(file);
        }
    }

    function handleMultipleImageUpload(files) {
        if (files.length >= 2) {
            const newRows = document.querySelectorAll('.new-artwork-row');
            const fileArray = Array.from(files);

            fileArray.forEach((file, index) => {
                if (index < newRows.length) {
                    const row = newRows[index];
                    const tempId = row.getAttribute('data-temp-id');
                    const reader = new FileReader();
                    reader.onload = (e) => {
                        const dropArea = row.querySelector('.drag-drop-area');
                        dropArea.innerHTML = `<img src="${e.target.result}" alt="Preview">`;
                        dropArea.classList.add('has-image');

                        // Populate title with filename (without extension)
                        const titleInput = row.querySelector('.title-input');
                        if (titleInput) {
                            const filenameWithoutExt = file.name.replace(/\.[^/.]+$/, "");
                            titleInput.value = filenameWithoutExt;
                        }
                    };
                    reader.readAsDataURL(file);
                }
            });

            // Hide the multiple drop zone after processing
            document.getElementById('multipleDropZone').classList.add('hidden');
        }
    }

    // Bulk new item controls
    var bulkNewItemControls = $('#bulkNewItemControls');
    var saveAllBtn = $('#saveAllNewItemsBtn');
    var cancelAllBtn = $('#cancelAllNewItemsBtn');
    var newItemsCount = $('#newItemsCount');

    // Update bulk new item controls visibility
    function updateBulkNewItemControls() {
        var count = $('.new-artwork-row').length;
        newItemsCount.text(count + ' new item' + (count !== 1 ? 's' : ''));

        if (count > 0) {
            bulkNewItemControls.show();
        } else {
            bulkNewItemControls.hide();
        }
    }

    // Save All functionality
    saveAllBtn.on('click', function() {
        const newRows = $('.new-artwork-row');
        if (newRows.length === 0) {
            alert('No new items to save.');
            return;
        }

        // Validate all rows first
        let hasErrors = false;
        newRows.each(function() {
            const row = $(this);
            const name = row.find('.title-input').val();
            const collection = row.find('.collection-select').last().val();

            if (!name || !collection) {
                hasErrors = true;
                row.addClass('border-danger');
            } else {
                row.removeClass('border-danger');
            }
        });

        if (hasErrors) {
            alert('Please fill in required fields (Title and Collection) for all items.');
            return;
        }

        // Show loading state
        saveAllBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Saving...');

        // Collect all form data with async image processing
        const allFormData = [];
        let processedCount = 0;
        const totalRows = newRows.length;

        if (totalRows === 0) {
            alert('No new items to save.');
            return;
        }

        newRows.each(function() {
            const row = $(this);
            const formData = {
                name: row.find('.title-input').val(),
                artist: row.find('.artist-input').val(),
                type: row.find('.type-input').val(),
                artwork_collection_id: row.find('.collection-select').last().val(),
                description: row.find('.description-input').val(),
                height: row.find('.height-input').val(),
                width: row.find('.width-input').val(),
                unit: row.find('.unit-select').val()
            };

            // Add company field if user is super admin
            @if(auth()->user()->isSuperAdmin())
            const companySelect = row.find('.company-select');
            if (companySelect.length > 0) {
                formData.company_id = companySelect.val();
            }
            @endif

            // Handle image upload if present
            const fileInput = row.find('.image-upload-input')[0];
            const dropArea = row.find('.drag-drop-area');

            if (fileInput.files.length > 0) {
                // Convert file to base64 data URL
                const file = fileInput.files[0];
                const reader = new FileReader();
                reader.onload = function(e) {
                    formData.image = e.target.result;
                    allFormData.push(formData);
                    processedCount++;

                    // When all rows are processed, send the data
                    if (processedCount === totalRows) {
                        sendBulkData();
                    }
                };
                reader.readAsDataURL(file);
            } else if (dropArea.find('img').length > 0) {
                // If image is already displayed in drop area, get the src
                const imgSrc = dropArea.find('img').attr('src');
                if (imgSrc && imgSrc.startsWith('data:image')) {
                    formData.image = imgSrc;
                }
                allFormData.push(formData);
                processedCount++;

                // When all rows are processed, send the data
                if (processedCount === totalRows) {
                    sendBulkData();
                }
            } else {
                // No image
                allFormData.push(formData);
                processedCount++;

                // When all rows are processed, send the data
                if (processedCount === totalRows) {
                    sendBulkData();
                }
            }
        });

        function sendBulkData() {
            // Send all data to bulk store endpoint
            const formDataObj = new FormData();

            // Add CSRF token first
            formDataObj.append('_token', '{{ csrf_token() }}');

            allFormData.forEach((data, index) => {
                Object.keys(data).forEach(key => {
                    if (key === 'image') {
                        formDataObj.append(`items[${index}][image]`, data[key]);
                    } else if (key !== '_token') { // Skip _token from individual items
                        formDataObj.append(`items[${index}][${key}]`, data[key]);
                    }
                });
            });

            $.ajax({
                url: '{{ route("inventory.bulk-store") }}',
                type: 'POST',
                data: formDataObj,
                processData: false,
                contentType: false,
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Successfully saved ' + response.saved_count + ' item(s).');
                        // Remove all new rows
                        $('.new-artwork-row').remove();
                        // Reload table
                        table.ajax.reload(function() {
                            // Reapply any pending changes after reload
                            reapplyPendingChanges();
                        }, false);
                        // Hide multiple drop zone
                        $('#multipleDropZone').remove();
                        // Update controls
                        updateBulkNewItemControls();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error saving items: ' + (xhr.responseJSON?.message || 'Unknown error'));
                },
                complete: function() {
                    saveAllBtn.prop('disabled', false).html('<i class="fas fa-save me-2"></i>Save All');
                }
            });
        }
    });

    // Cancel All functionality
    cancelAllBtn.on('click', function() {
        if (confirm('Are you sure you want to cancel all new items? This action cannot be undone.')) {
            // Remove all new rows
            $('.new-artwork-row').remove();
            // Hide multiple drop zone
            $('#multipleDropZone').remove();
            // Update controls
            updateBulkNewItemControls();
        }
    });

    // Individual delete row functionality
    $(document).on('click', '.delete-row-btn', function() {
        const row = $(this).closest('.new-artwork-row');
        row.remove();

        // Hide multiple drop zone if no more new rows
        if ($('.new-artwork-row').length === 0) {
            $('#multipleDropZone').remove();
        }

        // Update controls
        updateBulkNewItemControls();
    });

    // Copy/Duplicate functionality
    $('#duplicateArtworkBtn').on('click', function() {
        if (selectedRows.size === 0) {
            alert('Please select at least one item to duplicate.');
            return;
        }

        if (confirm(`Are you sure you want to duplicate ${selectedRows.size} selected item(s)?`)) {
            const selectedIds = Array.from(selectedRows);

            // Show loading state
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: '{{ route("inventory.bulk-copy") }}',
                type: 'POST',
                data: {
                    ids: selectedIds,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Successfully duplicated ' + response.copied_count + ' item(s).');
                        // Clear pending changes since server data is now updated
                        clearPendingChanges();
                        table.ajax.reload(function() {
                            // No need to reapply changes since server data is fresh
                        }, false);
                        selectedRows.clear();
                        updateBulkEditUI();
                        updateSelectAllState();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error duplicating items: ' + (xhr.responseJSON?.message || 'Unknown error'));
                },
                complete: function() {
                    $('#duplicateArtworkBtn').prop('disabled', false).html('<i class="fas fa-copy"></i>');
                }
            });
        }
    });

    // Move to Collection - open modal
    $('#moveToCollection').on('click', function() {
        if (selectedRows.size === 0) {
            alert('Please select at least one item to move.');
            return;
        }
        $('#moveCollectionSelect').val('');
        $('#moveToCollectionModal').modal('show');
    });

    // Confirm move action
    $('#confirmMoveBtn').on('click', function() {
        var targetCollection = $('#moveCollectionSelect').val();
        var selectedIds = Array.from(selectedRows);

        if (!targetCollection) {
            alert('Please select a collection to move to.');
            return;
        }

        // Show loading state
        var $btn = $('#confirmMoveBtn');
        $btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin me-2"></i>Moving...');

        $.ajax({
            url: '{{ route("inventory.bulk-update") }}',
            type: 'POST',
            data: {
                ids: selectedIds,
                collection: targetCollection,
                _token: '{{ csrf_token() }}'
            },
            success: function(response) {
                if (response.success) {
                    alert('Successfully moved ' + response.updated_count + ' item(s).');
                    $('#moveToCollectionModal').modal('hide');
                    // Clear pending changes since server data is now updated
                    clearPendingChanges();
                    table.ajax.reload(function() {
                        // No need to reapply changes since server data is fresh
                    }, false);
                    selectedRows.clear();
                    updateBulkEditUI();
                    updateSelectAllState();
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error moving items: ' + (xhr.responseJSON?.message || 'Unknown error'));
            },
            complete: function() {
                $btn.prop('disabled', false).html('Move');
            }
        });
    });


    // Delete functionality
    $('#deleteArtworkBtn').on('click', function() {
        if (selectedRows.size === 0) {
            alert('Please select at least one item to delete.');
            return;
        }

        if (confirm(`Are you sure you want to delete ${selectedRows.size} selected item(s)? This action cannot be undone.`)) {
            const selectedIds = Array.from(selectedRows);

            // Show loading state
            $(this).prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

            $.ajax({
                url: '{{ route("inventory.bulk-delete") }}',
                type: 'POST',
                data: {
                    ids: selectedIds,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Successfully deleted ' + response.deleted_count + ' item(s).');
                        // Clear pending changes since server data is now updated
                        clearPendingChanges();
                        table.ajax.reload(function() {
                            // No need to reapply changes since server data is fresh
                        }, false);
                        selectedRows.clear();
                        updateBulkEditUI();
                        updateSelectAllState();
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error deleting items: ' + (xhr.responseJSON?.message || 'Unknown error'));
                },
                complete: function() {
                    $('#deleteArtworkBtn').prop('disabled', false).html('<i class="fas fa-trash"></i>');
                }
            });
        }
    });

    // Image upload functionality for collection thumbnail
    const collectionImageUploadBox = document.getElementById('collectionImageUploadBox');
    const collectionThumbnail = document.getElementById('collectionThumbnail');
    const collectionImageName = document.getElementById('collectionImageName');

    if (collectionImageUploadBox && collectionThumbnail) {
        // Handle click on upload box
        collectionImageUploadBox.addEventListener('click', (e) => {
            // Check if there's already an image displayed
            // const existingImg = collectionImageUploadBox.querySelector('.img-preview');
            // if (existingImg) {
            //     // If there's an existing image, don't trigger file input
            //     e.stopPropagation();
            //     return;
            // }
            // Only trigger file input if no image is present
            collectionThumbnail.click();
        });

        // Drag and drop functionality
        collectionImageUploadBox.addEventListener('dragover', (e) => {
            e.preventDefault();
            collectionImageUploadBox.classList.add('drag-over');
        });

        collectionImageUploadBox.addEventListener('dragleave', (e) => {
            e.preventDefault();
            collectionImageUploadBox.classList.remove('drag-over');
        });

        collectionImageUploadBox.addEventListener('drop', (e) => {
            e.preventDefault();
            collectionImageUploadBox.classList.remove('drag-over');

            const files = e.dataTransfer.files;
            if (files.length > 0) {
                const file = files[0];
                if (file.type.startsWith('image/')) {
                    handleCollectionImageFile(file, collectionImageUploadBox, collectionThumbnail, collectionImageName);
                } else {
                    alert('Please drop an image file (JPEG or PNG)');
                }
            }
        });

        // Handle file input change
        collectionThumbnail.addEventListener('change', (event) => {
            const file = event.target.files[0];
            if (file) {
                handleCollectionImageFile(file, collectionImageUploadBox, collectionThumbnail, collectionImageName);
            }
        });
    }

    // Function to handle collection image file processing
    function handleCollectionImageFile(file, uploadBox, inputElement, nameElement) {
        // CRITICAL FIX: Set the file to the input element so it's available when saving
        // Create a new FileList with the dropped file
        const dataTransfer = new DataTransfer();
        dataTransfer.items.add(file);
        inputElement.files = dataTransfer.files;

        const reader = new FileReader();
        reader.onload = (e) => {
            const img = document.createElement('img');
            img.src = e.target.result;
            img.className = 'img-preview';
            img.style.cursor = 'pointer';
            img.title = 'Click to replace image';
            img.crossOrigin = 'anonymous';

            // Wait for image to load to get dimensions
            img.onload = () => {
                uploadBox.innerHTML = '';
                uploadBox.style.backgroundColor = 'grey';
                uploadBox.appendChild(img);

                // Re-add the file input and overlay
                uploadBox.appendChild(inputElement);
                const overlay = document.createElement('div');
                overlay.className = 'overlay';
                overlay.textContent = 'Click to replace image';
                uploadBox.appendChild(overlay);

                // Add click event to the preview image to trigger file input
                img.addEventListener('click', (e) => {
                    e.stopPropagation();
                    inputElement.click();
                });

                // Re-attach event listeners
                inputElement.addEventListener('change', (event) => {
                    const file = event.target.files[0];
                    if (file) {
                        handleCollectionImageFile(file, uploadBox, inputElement, nameElement);
                    }
                });
            };
        };
        reader.readAsDataURL(file);

        if (nameElement) {
            nameElement.textContent = file.name;
        }
    }

    // Handle company dropdown change to filter collections
    @if(auth()->user()->isSuperAdmin())
    const companySelect = document.getElementById('prefillCompany');
    const collectionSelect = document.getElementById('prefillCollection');

    if (companySelect) {
        companySelect.addEventListener('change', function() {
            const companyId = this.value;

            // Clear current options
            collectionSelect.innerHTML = '<option value="">Select collection</option>';

            if (!companyId) {
                return;
            }

            // Show loading state
            const loadingOption = document.createElement('option');
            loadingOption.value = '';
            loadingOption.textContent = 'Loading collections...';
            loadingOption.disabled = true;
            collectionSelect.appendChild(loadingOption);

            // Fetch collections for the selected company
            fetch('{{ route("inventory.collections.by-company") }}?company_id=' + companyId)
                .then(response => response.json())
                .then(data => {
                    // Clear loading option
                    collectionSelect.innerHTML = '<option value="">Select collection</option>';

                    if (data.success && data.collections.length > 0) {
                        data.collections.forEach(collection => {
                            const option = document.createElement('option');
                            option.value = collection.id;
                            option.textContent = collection.name;
                            collectionSelect.appendChild(option);
                        });
                    } else {
                        const noCollectionsOption = document.createElement('option');
                        noCollectionsOption.value = '';
                        noCollectionsOption.textContent = 'No collections available for this company';
                        noCollectionsOption.disabled = true;
                        collectionSelect.appendChild(noCollectionsOption);
                    }
                })
                .catch(error => {
                    console.error('Error fetching collections:', error);
                    collectionSelect.innerHTML = '<option value="">Error loading collections</option>';
                });
        });
    }
    @else
    // For non-super admin users, collections are already filtered by their company
    // No additional JavaScript needed as collections are pre-filtered on the server
    @endif
});
    // Export inventory as ZIP (CSV + images)
    (function attachExportHandler(){
        const downloadBtn = document.getElementById('downloadTableBtn');
        if (!downloadBtn) return;
        downloadBtn.addEventListener('click', function(){
            // Check if a specific collection is selected
            const dropdownBtn = document.querySelector('.collections-dropdown .btn');
            const selectedCollectionNameEl = document.getElementById('selectedCollectionName');
            const selectedName = selectedCollectionNameEl ? selectedCollectionNameEl.textContent.trim() : null;

            // If no specific collection is selected (showing "All Collections"), show alert modal
            if (!selectedName || selectedName === 'All Collections') {
                // Show the collection selection alert modal
                const alertModal = new bootstrap.Modal(document.getElementById('collectionSelectionAlertModal'));
                alertModal.show();
                return; // Stop execution here
            }

            const params = new URLSearchParams();
            const searchInput = document.getElementById('tableSearch');
            if (searchInput && searchInput.value) params.set('q', searchInput.value);

            // Set collection_id parameter
            const matched = (allCollections || []).find(c => c.name === selectedName);
            if (matched) params.set('collection_id', matched.id);

            const url = `${'{{ route("inventory.export") }}'}${params.toString() ? ('?' + params.toString()) : ''}`;
            window.location.href = url;
        });
    })();

    // Function to update collection image in the UI without page reload
    function updateCollectionImageInUI(collectionId, collectionName, thumbnailUrl, itemCount) {
        // Update the dropdown button if this collection is currently selected
        if (window.selectedCollectionId == collectionId) {
            const dropdownButton = document.querySelector('.dropdown button');
            const imageContainer = dropdownButton.querySelector('.d-flex.align-items-center');

            if (thumbnailUrl) {
                imageContainer.innerHTML = `
                    <img src="${thumbnailUrl}" alt="${collectionName}" class="me-3" style="width: 18px; height: 18px; object-fit: cover; border-radius: 0;">
                    <div class="text-start">
                        <div id="selectedCollectionName" style="font-weigh : 500; color: #495057; font-size : 14px">${collectionName}</div>
                        <small class="text-muted">${itemCount} items</small>
                    </div>
                `;
            } else {
                imageContainer.innerHTML = `
                    <i class="fas fa-image me-3" style="color: #6c757d; font-size: 18px;"></i>
                    <div class="text-start">
                        <div id="selectedCollectionName" style="font-weigh : 500; color: #495057; font-size : 14px">${collectionName}</div>
                        <small class="text-muted">${itemCount} items</small>
                    </div>
                `;
            }
        }

        // Update the dropdown menu item for this collection
        updateCollectionInDropdown(collectionId, collectionName, thumbnailUrl, itemCount);
    }

    // Function to update a specific collection in the dropdown menu
    function updateCollectionInDropdown(collectionId, collectionName, thumbnailUrl, itemCount) {
        const dropdownItems = document.querySelectorAll('.dropdown-item');
        dropdownItems.forEach(item => {
            const onclick = item.getAttribute('onclick');
            if (onclick && onclick.includes(`'${collectionId}'`)) {
                // Update the image and text for this collection item
                const imageElement = item.querySelector('img');
                const iconElement = item.querySelector('i');
                const nameElement = item.querySelector('.fw-bold');
                const countElement = item.querySelector('.text-muted');

                if (thumbnailUrl) {
                    if (imageElement) {
                        imageElement.src = thumbnailUrl;
                    } else if (iconElement) {
                        // Replace icon with image
                        const newImg = document.createElement('img');
                        newImg.src = thumbnailUrl;
                        newImg.alt = '';
                        newImg.width = 24;
                        newImg.height = 24;
                        newImg.className = 'me-3 rounded';
                        newImg.style = 'object-fit: cover;';
                        iconElement.parentNode.replaceChild(newImg, iconElement);
                    }
                } else {
                    if (imageElement) {
                        // Replace image with icon
                        const newIcon = document.createElement('i');
                        newIcon.className = 'fas fa-image me-3';
                        newIcon.style = 'color: #6c757d; font-size: 16px;';
                        imageElement.parentNode.replaceChild(newIcon, imageElement);
                    }
                }

                if (nameElement) nameElement.textContent = collectionName;
                if (countElement) countElement.textContent = itemCount;

                // Update the onclick attribute with new parameters
                item.setAttribute('onclick', `selectCollection('${collectionId}', '${collectionName}', '${itemCount}', '${thumbnailUrl || ''}')`);
            }
        });
    }

    // Function to show artwork detail modal
    function showArtworkDetailModal(artworkData) {
        // Populate the modal with artwork data
        document.getElementById('artworkDetailImage').src = artworkData.image || '';
        document.getElementById('artworkDetailName').textContent = artworkData.name || '-';
        document.getElementById('artworkDetailArtist').textContent = artworkData.artist || '-';
        document.getElementById('artworkDetailType').textContent = artworkData.type || '-';
        document.getElementById('artworkDetailCollection').textContent = artworkData.collection || '-';
        document.getElementById('artworkDetailCompany').textContent = artworkData.company || '-';

        // Format dimensions
        const height = artworkData.height || '';
        const width = artworkData.width || '';
        const unit = artworkData.unit || '';
        const dimensions = (height && width) ? `${height} × ${width} ${unit}` : '-';
        document.getElementById('artworkDetailDimensions').textContent = dimensions;

        document.getElementById('artworkDetailUnit').textContent = artworkData.unit || '-';
        document.getElementById('artworkDetailDescription').innerHTML = artworkData.description || '-';

        // Show the modal
        const modal = new bootstrap.Modal(document.getElementById('artworkDetailModal'));
        modal.show();
    }

    // Handle company dropdown change to filter collections
    document.addEventListener('DOMContentLoaded', function() {
        const companyDropdown = document.getElementById('masterCompanyDropdown');
        const collectionDropdown = document.getElementById('masterCollectionHeader');
        
        if (companyDropdown && collectionDropdown) {
            companyDropdown.addEventListener('change', function() {
                const companyId = this.value;
                
                // Clear current collections
                collectionDropdown.innerHTML = '<option value="">Loading...</option>';
                
                // Fetch collections for the selected company
                fetch(`{{ route('inventory.collections.by-company') }}?company_id=${companyId}`)
                    .then(response => response.json())
                    .then(data => {
                        // Clear the loading option
                        collectionDropdown.innerHTML = '';
                        
                        // Add new collection options
                        if (data.collections && data.collections.length > 0) {
                            data.collections.forEach(collection => {
                                const option = document.createElement('option');
                                option.value = collection.id;
                                option.textContent = collection.name;
                                collectionDropdown.appendChild(option);
                            });
                        } else {
                            // Add a "No collections" option if no collections found
                            const option = document.createElement('option');
                            option.value = '';
                            option.textContent = 'No collections found';
                            collectionDropdown.appendChild(option);
                        }
                    })
                    .catch(error => {
                        console.error('Error fetching collections:', error);
                        collectionDropdown.innerHTML = '<option value="">Error loading collections</option>';
                    });
            });
        }
    });


</script>
@endsection
