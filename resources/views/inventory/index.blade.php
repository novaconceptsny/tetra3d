@extends('layouts.redesign')

@section('content')
    <section class="collection">
        <div class="main-intro container-fluid artworks-table">
            <div class="mt-5">
                <div class="bg-light-page">
                    <div id="show-collections-container" style="display: block;">
                        <div class="d-flex">
                            <!-- Sidebar -->
                            <div class="collections-sidebar" style="width: 280px; min-width: 220px; background: #f8f9fa; border-radius: 12px; margin-right: 24px;">
                                <div class="card shadow-sm border-0 rounded-4 p-3" style="background: #fff;">
                                    <div class="d-flex align-items-center justify-content-between mb-3">
                                        <h5 class="mb-0">Collections</h5>
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
                                                    <i class="fas fa-image me-3" style="color: #6c757d; font-size: 18px;"></i>
                                                    <div class="text-start">
                                                        @if($selectedCollection)
                                                            @php
                                                                $selectedCollectionData = $collections->firstWhere('id', $selectedCollection);
                                                            @endphp
                                                            @if($selectedCollectionData)
                                                                <div class="fw-bold">{{ $selectedCollectionData->name }}</div>
                                                                <small class="text-muted">{{ $selectedCollectionData->artworks()->count() }} items</small>
                                                            @else
                                                                <div class="fw-bold">All Collections</div>
                                                                <small class="text-muted">{{ $totalItems }} items</small>
                                                            @endif
                                                        @else
                                                            <div class="fw-bold">All Collections</div>
                                                            <small class="text-muted">{{ $totalItems }} items</small>
                                                        @endif
                                                    </div>
                                                </div>
                                                <i class="fas fa-chevron-down text-muted"></i>
                                            </button>
                                            <ul class="dropdown-menu w-100" style="max-height: 500px; overflow-y: auto; min-height: 200px; border: 1px solid #dee2e6; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center p-2" href="#" onclick="selectCollection('', 'All Collections', '{{ $totalItems }} items')" style="border-bottom: 1px solid #f8f9fa;">
                                                        <i class="fas fa-image me-3" style="color: #6c757d; font-size: 16px;"></i>
                                                        <div>
                                                            <div class="fw-bold">All Collections</div>
                                                            <small class="text-muted">{{ $totalItems }} items</small>
                                                        </div>
                                                    </a>
                                                </li>
                                                @foreach($collections as $collection)
                                                <li>
                                                    <a class="dropdown-item d-flex align-items-center p-2" href="#" onclick="selectCollection('{{$collection->id}}', '{{$collection->name}}', '{{$collection->artworks()->count()}} items')" style="border-bottom: 1px solid #f8f9fa;">
                                                        @if($collection->thumbnail_url)
                                                            <img src="{{ $collection->thumbnail_url }}" alt="" width="24" height="24" class="me-3 rounded" style="object-fit: cover;">
                                                        @else
                                                            <i class="fas fa-image me-3" style="color: #6c757d; font-size: 16px;"></i>
                                                        @endif
                                                        <div>
                                                            <div class="fw-bold">{{ $collection->name }}</div>
                                                            <small class="text-muted">{{ $collection->artworks()->count() }} items</small>
                                                        </div>
                                                    </a>
                                                </li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    </div>
                                    @if($selectedCollection)
                                        <div class="mt-2 d-flex justify-content-center gap-2">
                                            <button class="btn btn-outline-secondary btn-sm" onclick="editCollection()" title="Edit Collection" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-edit" style="font-size: 12px;"></i>
                                            </button>
                                            <button class="btn btn-outline-danger btn-sm" onclick="deleteCollection()" title="Delete Collection" style="width: 32px; height: 32px; padding: 0; display: flex; align-items: center; justify-content: center;">
                                                <i class="fas fa-trash" style="font-size: 12px;"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <!-- Main Content -->
                            <div class="flex-grow-1">
                                <div class="card shadow-sm border-0 rounded-4 p-3" style="background: #fff;">
                                    <x-loader/>

                                    <div class="card-body py-0">
                                        <!-- Search Section -->
                                        <div class="row mb-3">
                                            <div class="col-md-12">
                                                <div class="dataTables_filter">
                                                    <label for="tableSearch" class="form-label">Search:</label>
                                                    <div class="search-container d-flex align-items-center">
                                                        <input type="search" id="tableSearch" class="form-control form-control-sm me-2" placeholder="Search..." style="flex: 1;">
                                                        <div class="search-toggle-buttons">
                                                            <button type="button" class="btn btn-sm search-toggle-btn active" data-type="artwork" id="artworkToggle">
                                                                Artwork
                                                            </button>
                                                            <button type="button" class="btn btn-sm search-toggle-btn" data-type="collections" id="collectionsToggle">
                                                                Collections
                                                            </button>
                                                        </div>
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
                                                <button class="btn d-flex align-items-center gap-2" type="button" id="addArtworkBtn" style="background: #f8f9fa; border: 1px solid #dee2e6; color: #495057; border-radius: 8px 0 0 8px; padding: 4px 16px; font-weight: 500; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                                                    <i class="fas fa-plus" style="color: #495057;"></i>
                                                    <span>Artwork</span>
                                                </button>
                                                
                                                <!-- Dropdown Arrow Button -->
                                                <div class="dropdown">
                                                    <button class="btn dropdown-toggle" type="button" id="artworkDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false" style="background: #ffffff; border: 1px solid #dee2e6; color: #495057; border-radius: 0 8px 8px 0; padding: 4px 12px; font-weight: 500; border-left: none; box-shadow: 0 1px 3px rgba(0, 0, 0, 0.1);">
                                                    </button>
                                                    <ul class="dropdown-menu" aria-labelledby="artworkDropdownBtn" style="border-radius: 8px; border: 1px solid #dee2e6; box-shadow: 0 4px 12px rgba(0, 0, 0, 0.15); min-width: 160px;">
                                                        <li><a class="dropdown-item" href="#" id="addMultipleArtworksBtn" style="padding: 4px 16px; color: #495057; text-decoration: none; display: flex; align-items: center; transition: background-color 0.2s;" onmouseover="this.style.backgroundColor='#f8f9fa'" onmouseout="this.style.backgroundColor='transparent'">
                                                            <i class="fas fa-layer-group me-2" style="color: #6c757d;"></i>Add multiple
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
                                        </div>
                                        
                                        <!-- Show entries control moved here -->
                                        <div class="row mb-3">
                                            <div class="col-md-6">

                                            </div>
                                        </div>
                                        


                                        <div class="table-responsive">
                                            <table id="inventoryTable" class="table table-striped table-bordered" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th><input type="checkbox" id="selectAll" class="form-check-input"></th>
                                                        <th>Image</th>
                                                        <th>Company</th>
                                                        <th>Collection</th>
                                                        <th>Name</th>
                                                        <th>Artist</th>
                                                        <th>Type</th>
                                                        <th>Height</th>
                                                        <th>Width</th>
                                                        <th>Unit</th>
                                                        <th>Created</th>
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
                 </div>   
            </div>
        </div>
    </section>

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
                                    <select class="form-select" id="bulkType" name="type">
                                        <option value="">-- Keep existing --</option>
                                        <option value="Painting">Painting</option>
                                        <option value="Digital Art">Digital Art</option>
                                        <option value="Sculpture">Sculpture</option>
                                        <option value="Photography">Photography</option>
                                        <option value="Drawing">Drawing</option>
                                        <option value="Mixed Media">Mixed Media</option>
                                    </select>
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
            <td>
                <select class="form-select form-select-sm collection-select" required>
                    <option value="">Select company</option>
                    @foreach($companies as $company)
                        <option value="{{ $company->id }}">{{ $company->name }}</option>
                    @endforeach
                </select>
            </td>
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
                <select class="form-select form-select-sm type-select">
                    <option value="">Select type</option>
                    <option value="Painting">Painting</option>
                    <option value="Sculpture">Sculpture</option>
                </select>
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
                <div class="d-flex gap-1">
                    <button class="btn btn-success btn-sm save-row-btn" title="Save">
                        <i class="fas fa-check"></i>
                    </button>
                    <button class="btn btn-danger btn-sm cancel-row-btn" title="Cancel">
                        <i class="fas fa-times"></i>
                    </button>
                </div>
            </td>
        </tr>
    </template>
@endsection


@section('styles')
<!-- DataTables CSS and JS -->
<link rel="stylesheet" type="text/css" href="{{ asset('backend/assets/css/vendor/dataTables.bootstrap4.css') }}">

<style>
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .table td {
        vertical-align: middle;
    }

    .artwork-image {
        width: 40px;
        height: 40px;
        object-fit: cover;
        border-radius: 6px;
    }

    /* DataTables Editor styles */
    .dt-editor-inline {
        cursor: pointer;
    }

    .dt-editor-inline:hover {
        background-color: #f8f9fa;
    }

    .editable-cell {
        cursor: pointer;
        padding: 8px;
        border-radius: 4px;
        transition: background-color 0.2s;
    }

    .editable-cell:hover {
        background-color: #f8f9fa;
    }

    .editable-cell.editing {
        background-color: #fff3cd;
        border: 1px solid #ffeaa7;
    }

    .inline-edit-input {
        width: 100%;
        border: none;
        background: transparent;
        padding: 4px;
        font-size: inherit;
        font-family: inherit;
    }

    .inline-edit-select {
        width: 100%;
        border: none;
        background: transparent;
        padding: 4px;
        font-size: inherit;
        font-family: inherit;
    }

    /* DataTables processing indicator styling */
    .dataTables_processing {
        position: absolute;
        top: 50%;
        left: 50%;
        transform: translate(-50%, -50%);
        background: rgba(255, 255, 255, 0.9);
        border: 1px solid #ddd;
        border-radius: 4px;
        padding: 20px;
        z-index: 1000;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    /* Bulk edit controls styling */
    #bulkEditControls {
        background: #f8f9fa;
        border: 1px solid #dee2e6;
        border-radius: 8px;
        padding: 0px;
        transition: all 0.3s ease;
    }

    #bulkEditBtn {
        background: #099F9A;
        border: none;
        border-radius: 6px;
        padding: 4px 16px;
        font-weight: 500;
        transition: all 0.3s ease;
    }

    #bulkEditBtn:hover {
        background: #077a75;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(9, 159, 154, 0.3);
    }

    #selectedCount {
        font-size: 14px;
        color: #6c757d;
        font-weight: 500;
    }

    /* Modal styling */
    .modal-content {
        border-radius: 12px;
        border: none;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
    }

    .modal-header {
        background: #f8f9fa;
        border-bottom: 1px solid #dee2e6;
        border-radius: 12px 12px 0 0;
    }

    .modal-title {
        font-weight: 600;
        color: #495057;
    }

    /* Checkbox styling */
    .form-check-input:checked {
        background-color: #099F9A;
        border-color: #099F9A;
    }

    .form-check-input:focus {
        box-shadow: 0 0 0 0.2rem rgba(9, 159, 154, 0.25);
    }

    /* Pointer cursor for table checkboxes */
    #inventoryTable input[type="checkbox"] {
        cursor: pointer;
    }

    /* Search toggle buttons styling */
    .search-container {
        gap: 8px;
    }

    .search-toggle-buttons {
        display: flex;
        border-radius: 6px;
        overflow: hidden;
        border: 1px solid #dee2e6;
        background: #fff;
    }

    .search-toggle-btn {
        background: #fff;
        border: none;
        padding: 6px 12px;
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
        transition: all 0.2s ease;
        border-radius: 0;
        position: relative;
    }

    .search-toggle-btn:first-child {
        border-top-left-radius: 6px;
        border-bottom-left-radius: 6px;
    }

    .search-toggle-btn:last-child {
        border-top-right-radius: 6px;
        border-bottom-right-radius: 6px;
    }

    .search-toggle-btn:hover {
        background: #f8f9fa;
        color: #495057;
    }

    .search-toggle-btn.active {
        background: #099F9A;
        color: #fff;
        box-shadow: 0 2px 4px rgba(9, 159, 154, 0.2);
    }

    .search-toggle-btn.active:hover {
        background: #077a75;
        color: #fff;
    }

    .search-toggle-btn:focus {
        outline: none;
        box-shadow: 0 0 0 2px rgba(9, 159, 154, 0.25);
    }

    /* Icon button styling */
    .icon-button {
        width: 40px;
        height: 34px;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        padding: 0;
        border: none;
        background: #099F9A;
        color: #fff;
        font-size: 16px;
        transition: all 0.3s ease;
        box-shadow: 0 2px 4px rgba(9, 159, 154, 0.2);
    }

    .icon-button:hover {
        background: #077a75;
        transform: translateY(-1px);
        box-shadow: 0 4px 8px rgba(9, 159, 154, 0.3);
        color: #fff;
    }

    .icon-button:focus {
        outline: none;
        box-shadow: 0 0 0 3px rgba(9, 159, 154, 0.25);
    }

    .icon-button:active {
        transform: translateY(0);
        box-shadow: 0 2px 4px rgba(9, 159, 154, 0.2);
    }

    /* Inline editing row styles */
    .new-artwork-row {
        background-color: #f8f9fa !important;
        border: 2px solid #099F9A !important;
    }

    .new-artwork-row td {
        padding: 8px !important;
        vertical-align: middle !important;
    }

    .drag-drop-area {
        transition: all 0.3s ease;
        position: relative;
    }

    .drag-drop-area:hover {
        border-color: #099F9A !important;
        background-color: #e6f7f7 !important;
    }

    .drag-drop-area.drag-over {
        border-color: #099F9A !important;
        background-color: #e6f7f7 !important;
        transform: scale(1.05);
    }

    .drag-drop-area.has-image {
        border-style: solid !important;
        border-color: #28a745 !important;
        background-color: #d4edda !important;
    }

    .drag-drop-area img {
        width: 100%;
        height: 100%;
        object-fit: cover;
        border-radius: 4px;
    }

    .form-control-sm, .form-select-sm {
        font-size: 0.875rem;
        padding: 0.25rem 0.5rem;
    }

    .save-row-btn, .cancel-row-btn {
        width: 28px;
        height: 28px;
        padding: 0;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .save-row-btn {
        background-color: #28a745;
        border-color: #28a745;
    }

    .save-row-btn:hover {
        background-color: #218838;
        border-color: #1e7e34;
    }

    .cancel-row-btn {
        background-color: #dc3545;
        border-color: #dc3545;
    }

    .cancel-row-btn:hover {
        background-color: #c82333;
        border-color: #bd2130;
    }

    /* Multiple image drop zone */
    .multiple-drop-zone {
        border: 2px dashed #099F9A;
        border-radius: 8px;
        padding: 20px;
        text-align: center;
        background-color: #f8f9fa;
        margin-bottom: 15px;
        transition: all 0.3s ease;
    }

    .multiple-drop-zone:hover {
        background-color: #e6f7f7;
        border-color: #077a75;
    }

    .multiple-drop-zone.drag-over {
        background-color: #d4edda;
        border-color: #28a745;
        transform: scale(1.02);
    }

    .multiple-drop-zone.hidden {
        display: none;
    }
</style>
@endsection



@section('scripts')
<!-- Load DataTables libraries -->
<script type="text/javascript" charset="utf8" src="{{ asset('backend/assets/js/vendor/jquery.dataTables.min.js') }}"></script>
<script type="text/javascript" charset="utf8" src="{{ asset('backend/assets/js/vendor/dataTables.bootstrap4.js') }}"></script>

<!-- Verify DataTables is loaded -->
<script>
console.log('jQuery version:', $.fn.jquery);
console.log('DataTables available:', typeof $.fn.DataTable !== 'undefined');
console.log('DataTable available:', typeof DataTable !== 'undefined');
</script>

<script>
$(document).ready(function() {
    console.log('Initializing DataTable with inline editing');

    // Check if DataTables is available
    if (typeof $.fn.DataTable === 'undefined') {
        console.error('DataTables library not loaded!');
        alert('DataTables library not loaded. Please refresh the page.');
        return;
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
            {
                data: 'company'
            },
            {
                data: 'collection'
            },
            {
                data: 'name',
                render: function(data, type, row) {
                    return `<span class="editable-cell" data-field="name" data-id="${row.id}">${data || ''}</span>`;
                }
            },
            {
                data: 'artist',
                render: function(data, type, row) {
                    return `<span class="editable-cell" data-field="artist" data-id="${row.id}">${data || ''}</span>`;
                }
            },
            {
                data: 'type',
                render: function(data, type, row) {
                    return `<span class="editable-cell" data-field="type" data-id="${row.id}">${data || ''}</span>`;
                }
            },
            {
                data: 'height',
                render: function(data, type, row) {
                    return `<span class="editable-cell" data-field="height" data-id="${row.id}">${data || ''}</span>`;
                }
            },
            {
                data: 'width',
                render: function(data, type, row) {
                    return `<span class="editable-cell" data-field="width" data-id="${row.id}">${data || ''}</span>`;
                }
            },
            {
                data: 'unit',
                render: function(data, type, row) {
                    return `<span class="editable-cell" data-field="unit" data-id="${row.id}" data-type="select">${data || ''}</span>`;
                }
            },
            {
                data: 'created_at',
                render: function(data, type, row) {
                    return data ? new Date(data).toLocaleDateString() : '';
                }
            }
        ],

        order: [10, 'desc'], // Sort by created_at desc
        pageLength: 25,
        lengthMenu: [[10, 25, 50, 100], [10, 25, 50, 100]],
        responsive: true,
        search: {
            caseInsensitive: true
        },
        dom: 'rtip', // Hide default search and length controls, keep pagination
        initComplete: function(settings, json) {
            console.log('DataTable initialization complete');
            console.log('Data received:', json);
        }
    });

    console.log('DataTable created successfully');

    // Initialize Bootstrap tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
        return new bootstrap.Tooltip(tooltipTriggerEl);
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
        table.ajax.reload();
        
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
        
        if (isChecked) {
            selectedRows.add(rowId);
        } else {
            selectedRows.delete(rowId);
        }
        
        updateBulkEditUI();
        updateSelectAllState();
    });

    // Handle select all checkbox
    selectAllCheckbox.on('change', function() {
        var isChecked = $(this).is(':checked');
        $('.row-checkbox').prop('checked', isChecked);
        
        if (isChecked) {
            $('.row-checkbox').each(function() {
                selectedRows.add($(this).data('id'));
            });
        } else {
            selectedRows.clear();
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
                new bootstrap.Tooltip(tooltipTriggerEl);
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
                    table.ajax.reload();
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
        } else {
            // Create input field
            const inputType = (field === 'height' || field === 'width') ? 'number' : 'text';
            input = $(`<input type="${inputType}" class="inline-edit-input" value="${currentValue}">`);
        }

        // Replace cell content with input
        $cell.html(input);
        input.focus();
        input.select();

        // Handle save on blur or enter
        input.on('blur keypress', function(e) {
            if (e.type === 'keypress' && e.which !== 13) return;

            const newValue = $(this).val();
            console.log('Saving new value:', newValue);

            // Save the value
            $.ajax({
                url: `/inventory/${id}`,
                type: 'PUT',
                data: {
                    [field]: newValue,
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    console.log('Update successful');
                    $cell.removeClass('editing').text(newValue);
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
    window.selectCollection = function(collectionId, collectionName, itemCount) {
        window.selectedCollectionId = collectionId;
        
        // Update the dropdown button text
        const dropdownButton = document.querySelector('.dropdown button');
        const buttonContent = dropdownButton.querySelector('.text-start');
        
        if (collectionId) {
            buttonContent.innerHTML = `
                <div class="fw-bold">${collectionName}</div>
                <small class="text-muted">${itemCount}</small>
            `;
        } else {
            buttonContent.innerHTML = `
                <div class="fw-bold">All Collections</div>
                <small class="text-muted">${itemCount}</small>
            `;
        }
        
        // Reload the DataTable with the new filter
        table.ajax.reload();
        
        // Show/hide edit/delete buttons
        const editDeleteContainer = document.querySelector('.mt-2.d-flex.justify-content-center.gap-2');
        if (editDeleteContainer) {
            editDeleteContainer.style.display = collectionId ? 'flex' : 'none';
        }
    };

    // Edit collection function
    window.editCollection = function() {
        const collectionId = window.selectedCollectionId;
        if (!collectionId) {
            alert('Please select a collection to edit.');
            return;
        }
        
        // Get collection data
        const collectionData = @json($collections->keyBy('id'));
        const collection = collectionData[collectionId];
        
        if (!collection) {
            alert('Collection not found.');
            return;
        }
        
        // Show edit modal (you can implement this modal)
        const newName = prompt('Enter new collection name:', collection.name);
        if (newName && newName !== collection.name) {
            // Update collection via AJAX
            $.ajax({
                url: `/inventory/collections/${collectionId}/edit`,
                type: 'PUT',
                data: {
                    collection_name: newName,
                    collection_company_name: '{{ auth()->check() && auth()->user()->company ? auth()->user()->company->name : "Unknown Company" }}',
                    _token: '{{ csrf_token() }}'
                },
                success: function(response) {
                    if (response.success) {
                        alert('Collection updated successfully!');
                        location.reload(); // Reload to update the collections list
                    } else {
                        alert('Error: ' + response.message);
                    }
                },
                error: function(xhr) {
                    alert('Error updating collection: ' + (xhr.responseJSON?.message || 'Unknown error'));
                }
            });
        }
    };

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

    // Add multiple artwork rows
    $('#addMultipleArtworksBtn').on('click', function(e) {
        e.preventDefault();
        addNewArtworkRow();
        addNewArtworkRow();
        showMultipleDropZone();
    });


    function addNewArtworkRow() {
        const template = document.getElementById('newArtworkRowTemplate');
        const newRow = template.content.cloneNode(true);
        const tempId = 'temp_' + Date.now() + '_' + (++newRowCounter);
        
        newRow.querySelector('.new-artwork-row').setAttribute('data-temp-id', tempId);
        
        // Insert at the beginning of tbody
        const tbody = document.querySelector('#inventoryTable tbody');
        tbody.insertBefore(newRow, tbody.firstChild);
        
        // Initialize drag and drop for this row
        initializeRowDragDrop(tempId);
        
        // Focus on title input
        setTimeout(() => {
            const titleInput = document.querySelector(`[data-temp-id="${tempId}"] .title-input`);
            if (titleInput) titleInput.focus();
        }, 100);
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
                
                // Auto-fill title from filename
                const titleInput = row.querySelector('.title-input');
                const filename = file.name.replace(/\.[^/.]+$/, ""); // Remove extension
                titleInput.value = filename;
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
                        
                        // Auto-fill title from filename
                        const titleInput = row.querySelector('.title-input');
                        const filename = file.name.replace(/\.[^/.]+$/, ""); // Remove extension
                        titleInput.value = filename;
                    };
                    reader.readAsDataURL(file);
                }
            });
            
            // Hide the multiple drop zone after processing
            document.getElementById('multipleDropZone').classList.add('hidden');
        }
    }

    // Save row functionality
    $(document).on('click', '.save-row-btn', function() {
        const row = $(this).closest('.new-artwork-row');
        const tempId = row.attr('data-temp-id');
        
        // Collect form data
        const formData = {
            name: row.find('.title-input').val(),
            artist: row.find('.artist-input').val(),
            type: row.find('.type-select').val(),
            artwork_collection_id: row.find('.collection-select').val(),
            description: row.find('.description-input').val(),
            height: row.find('.height-input').val(),
            width: row.find('.width-input').val(),
            unit: row.find('.unit-select').val(),
            _token: '{{ csrf_token() }}'
        };
        
        // Validate required fields
        if (!formData.name || !formData.artwork_collection_id) {
            alert('Please fill in required fields (Title and Collection)');
            return;
        }
        
        // Show loading state
        const saveBtn = $(this);
        saveBtn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
        
        // Handle image upload if present
        const fileInput = row.find('.image-upload-input')[0];
        const formDataObj = new FormData();
        
        Object.keys(formData).forEach(key => {
            formDataObj.append(key, formData[key]);
        });
        
        if (fileInput.files.length > 0) {
            formDataObj.append('image', fileInput.files[0]);
        }
        
        $.ajax({
            url: '{{ route("inventory.store") }}',
            type: 'POST',
            data: formDataObj,
            processData: false,
            contentType: false,
            success: function(response) {
                if (response.success) {
                    // Remove the new row
                    row.remove();
                    // Reload table
                    table.ajax.reload();
                    // Hide multiple drop zone if no more new rows
                    if ($('.new-artwork-row').length === 0) {
                        $('#multipleDropZone').remove();
                    }
                } else {
                    alert('Error: ' + response.message);
                }
            },
            error: function(xhr) {
                alert('Error adding artwork: ' + (xhr.responseJSON?.message || 'Unknown error'));
            },
            complete: function() {
                saveBtn.prop('disabled', false).html('<i class="fas fa-check"></i>');
            }
        });
    });

    // Cancel row functionality
    $(document).on('click', '.cancel-row-btn', function() {
        const row = $(this).closest('.new-artwork-row');
        row.remove();
        
        // Hide multiple drop zone if no more new rows
        if ($('.new-artwork-row').length === 0) {
            $('#multipleDropZone').remove();
        }
    });
});
</script>
@endsection
