<div class="bg-light-page">
    <div id="show-collections-container" style="display: block;">
        <div class="d-flex">
            <!-- Sidebar -->
            <div class="collections-sidebar" style="width: 280px; min-width: 220px; background: #f8f9fa; border-radius: 12px; margin-right: 24px;">
                <div class="card shadow-sm border-0 rounded-4 p-3" style="background: #fff;">
                    <div class="d-flex align-items-center justify-content-between mb-3">
                        <h5 class="mb-0">Collections</h5>
                        <button class="btn btn-link p-0" data-bs-toggle="modal" data-bs-target="#addCollectionModal" onclick="handleOpenCollectionModal()" style="width: 32px; height: 32px; border-radius: 50%; background: #007bff; display: flex; align-items: center; justify-content: center;">
                            <i class="fas fa-plus text-white" style="font-size: 14px;"></i>
                        </button>
                    </div>
                    <div class="collections-dropdown">
                        <div class="dropdown w-100">
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
                                                <small class="text-muted">3645 items</small>
                                            @endif
                                        @else
                                            <div class="fw-bold">All Collections</div>
                                            <small class="text-muted">3645 items</small>
                                        @endif
                                    </div>
                                </div>
                                <i class="fas fa-chevron-down text-muted"></i>
                            </button>
                            <ul class="dropdown-menu w-100" style="max-height: 500px; overflow-y: auto; min-height: 200px; border: 1px solid #dee2e6; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                                <li>
                                    <a class="dropdown-item d-flex align-items-center p-2" href="#" onclick="selectCollection('', 'All Collections', '3645 items')" style="border-bottom: 1px solid #f8f9fa;">
                                        <i class="fas fa-image me-3" style="color: #6c757d; font-size: 16px;"></i>
                                        <div>
                                            <div class="fw-bold">All Collections</div>
                                            <small class="text-muted">3645 items</small>
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

                    <div class="card-header d-flex flex-column">
                        <div class="d-flex mb-2">
                            <h5 class="me-auto">{{ $heading }}</h5>
                            @if(user()->isAdmin())
                            <div class="float-end">
                                @include('backend.includes.datatable.bulk-delete')
                            </div>
                            @endif
                        </div>
                        <!-- Filters Start -->
                        <div class="d-flex align-items-center">
                            <div class="d-flex flex-grow-1">
                                @include('backend.includes.datatable.search')
                                <div class="me-1">
                                    <select wire:model.live="selectedCollection" class="form-control rounded-0">
                                        <option value="">All Collections</option>
                                        @foreach($collections as $collection)
                                            <option value="{{$collection->id}}">{{$collection->name}}</option>
                                        @endforeach
                                    </select>
                                </div>

                                @if(isset($columns['company_name']))
                                    <div class="me-1">
                                        <select wire:model.live="selectedCompany" class="form-control rounded-0">
                                            <option value="">All Companies</option>
                                            @foreach($companies as $company)
                                                <option value="{{$company->id}}">{{$company->name}}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                @endif
                                {{--<div class="me-1">
                                    <select wire:model.live="selectedArtist" class="form-control">
                                        <option value="">All Artists</option>
                                        @foreach($artists as $artist)
                                            <option value="{{$artist}}">{{$artist}}</option>
                                        @endforeach
                                    </select>
                                </div>--}}
                                <!-- @include('backend.includes.datatable.reset-filters') -->
                            </div>

                            @if(!user()->isAdmin())
                            <div class="btn-group ms-auto" role="group" aria-label="Artwork Actions">
                                <button type="button" class="btn btn-light" title="Add" onclick="handleOpenUploadArtworks()">
                                    <i class="fas fa-plus"></i>
                                </button>
                                <button type="button" class="btn btn-light" title="Copy">
                                    <i class="fas fa-copy"></i>
                                </button>
                                <button type="button" class="btn btn-light" title="Swap">
                                    <i class="fas fa-exchange-alt"></i>
                                </button>
                                <button type="button" class="btn btn-light" title="Delete" id="bulkDeleteBtn"  data-bs-toggle="modal" data-bs-target="#confirmDeleteModal">
                                    <i class="fas fa-trash"></i>
                                </button>
                            </div>
                            @endif
                        </div>

                        @if($selectedRows && user()->can('bulkUpdate', \App\Models\Artwork::class))
                            <div class="d-flex mt-2 justify-content-end">
                                <div class="me-1 ">
                                    <label for="">Move to Collection</label>
                                    <select wire:model.live="targetCollection" class="form-control  rounded-0 border-black">
                                        <option value="">Select Collection</option>
                                        @foreach($collections as $collection)
                                            <option value="{{$collection->id}}">{{$collection->name}}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="align-self-end ms-2">
                                    <button class="btn btn-primary {{ !$targetCollection ? 'disabled' : '' }}" wire:click="updateCollection">{{ __('Move') }}</button>
                                </div>
                            </div>
                        @endif
                        @include('backend.includes.datatable.toggle-columns')
                    </div>

                    <div class="card-body py-0">
                        <div class="mb-3 scrollbar table-responsive" x-data="{artworkImage: null}">
                            <table class="table table-borderless align-middle mb-0">
                                @include('backend.includes.datatable.header')
                                <tbody>
                                @foreach($rows as $row)
                                    <tr class="dt-row">
                                        @include('backend.includes.datatable.bulk-selection')

                                        <!-- pre columns !-->
                                        <td>
                                            <img src="{{ $row->image_url }}" alt="" style="width: 40px; height: 40px; object-fit: cover; border-radius: 6px;">
                                        </td>

                                        @include('backend.includes.datatable.content')

                                        <td>
                                            @include('backend.includes.datatable.actions')
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                            <div class="modal fade" id="artworkImage" tabindex="-1" >
                                <div class="modal-dialog modal-lg modal-dialog-centered modal-dialog-scrollable">
                                    <div class="modal-content">
                                        <div class="modal-header">
                                            <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                                        </div>
                                        <div class="modal-body text-center">
                                            <img :src="artworkImage">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                    @include('backend.includes.datatable.footer')
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
                        <span id="spreadsheet-filename" style="display: none; font-weight: bold; color: #28a745;"></span>
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
                        <span id="image-filename" style="display: none; font-weight: bold; color: #28a745;"></span>
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

                <table class="table align-middle">
                    <thead style="background-color: #f8f9fa;">
                        <tr>
                            <th style="color: black; font-weight: 500;">Image</th>
                            <th style="color: black; font-weight: 500; width: 300px;">
                            Collection
                                <select id="masterCollectionHeader" class="form-select" style="width: auto; display: inline-block; margin-left: 8px;">
                                    @foreach($collections as $collection)
                                        <option value="{{$collection->id}}">{{$collection->name}}</option>
                                    @endforeach
                                </select>
                            </th>
                            <th style="color: black; font-weight: 500;">Title</th>
                            <th style="color: black; font-weight: 500;">Artist</th>
                            <th style="color: black; font-weight: 500;">Height</th>
                            <th style="color: black; font-weight: 500;">Width</th>
                            <th style="color: black; font-weight: 500; width : 300px">
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
                            <div class="d-flex align-items-center">
                                <label for="collectionThumbnail" class="thumbnail-upload border rounded d-flex flex-column align-items-center justify-content-center" style="width: 80px; height: 100px; cursor: pointer;">
                                    <span id="thumbnailText">Click to add image</span>
                                    <input type="file" id="collectionThumbnail" name="thumbnail" accept="image/*" style="display: none;">
                                </label>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer d-flex justify-content-end">
                    <button type="button" class="btn btn-save-collection" id="saveCollectionBtn" onclick="handleSaveCollection()">Save</button>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="confirmDeleteModal" tabindex="-1" aria-labelledby="confirmDeleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-body text-center">
                    Are you sure you want to perform this action?
                </div>
                <div class="modal-footer justify-content-center">
                    <button type="button" class="btn btn-danger" id="confirmDeleteYes" onclick="handleDeleteArtworks()">Yes</button>
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">No</button>
                </div>
            </div>
        </div>
    </div>

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

    <style>
    .bg-light-page {
        background: #f7f9fb;
        min-height: 100vh;
    }

    .table tbody tr:hover {
        background: #f1f3f7;
    }

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
        background-color: #a9cff5;
        border-radius: 50%;
        margin-right: 10px;
    }

    .icon-circle i {
        font-size: 14px;
        color: #000;
    }

    /* Collection List Styling */
    .list-group-item {
        border: none !important;
        padding: 10px 0;
        font-size: 16px;
        font-weight: 600;
        display: flex;
        align-items: center;
    }

    .list-group-item:hover{
        background-color: #f8f9fa;
    }

    .collection-icon {
        font-size: 20px;
        margin-right: 10px;
        color: #000;
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


    .list-group{
        margin-top: 20px;
        /* max-height: 250px;
        overflow: hidden; */
    }

    .list-group-item i.fa-ellipsis-v {
        font-size: 14px;
        color: #6c757d;
        position: absolute;
        top: 9px;
        right: 9px;
    }
 }

    .btn-group .btn {
        margin-right: 4px;
        border-radius: 6px !important;
        border: 1px solid #e0e0e0;
    }
    .btn-group .btn:last-child {
        margin-right: 0;
    }

    .thumbnail-upload {
        background: #f8f9fa;
        color: #888;
        font-size: 14px;
        text-align: center;
        transition: background 0.2s;
    }
    .thumbnail-upload:hover {
        background: #e9ecef;
    }

    .add-collection-modal-dialog {
        max-width: 800px;
        width: 800px;
    }

    .add-collection-modal-dialog .modal-content {
        height: 500px   ;
        min-height: 500px;
        max-height: 500px;
        overflow: auto;
    }

    .btn-save-collection {
        background: #2453e3;
        color: #fff;
        border-radius: 8px;
        min-width: 120px;
        padding: 8px 32px;
        border: none;
        box-shadow: 0 2px 6px rgba(36, 83, 227, 0.08);
        font-weight: 500;
        font-size: 16px;
        transition: background 0.2s, box-shadow 0.2s;
    }
    .btn-save-collection:hover, .btn-save-collection:focus {
        background: #1a3fa6;
        color: #fff;
        box-shadow: 0 4px 12px rgba(36, 83, 227, 0.15);
    }

    .upload-box {
        border: 2px dashed #b0b8c1;
        border-radius: 12px;
        background: #f7f9fb;
        min-height: 120px;
        display: flex;
        align-items: center;
        justify-content: center;
        cursor: pointer;
        text-align: center;
        font-size: 16px;
        color: #6c757d;
        transition: border-color 0.2s, background 0.2s;
        position: relative;
    }
    .upload-box:hover {
        border-color: #2453e3;
        background: #e9f0fb;
    }
    .upload-box.dragover {
        border-color: #007bff;
        background: #f8f9fa;
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0, 123, 255, 0.15);
    }
    .upload-box input[type="file"] {
        position: absolute;
        width: 100%;
        height: 100%;
        opacity: 0;
        cursor: pointer;
        left: 0;
        top: 0;
    }
    .table td[contenteditable="true"] {
        background: #f7f9fb;
        border-radius: 4px;
        outline: none;
        min-width: 80px;
    }

    .table td[contenteditable="true"]:empty:before {
        content: attr(data-placeholder);
        color: #6c757d;
        font-style: italic;
    }

    .table td[contenteditable="true"]:focus:empty:before {
        color: #adb5bd;
    }

    .btn-warning {
        background-color: #ffc107;
        color: #000;
        font-weight: bold;
    }

    /* Upload Progress Styles */
    .progress-container {
        width: 200px;
        height: 8px;
        background: #e9ecef;
        border-radius: 4px;
        overflow: hidden;
        margin: 10px auto;
    }

    .progress-bar-upload {
        height: 100%;
        background: #28a745;
        width: 0%;
        transition: width 0.3s ease;
        border-radius: 4px;
    }

    .progress-text {
        font-size: 14px;
        color: #6c757d;
        display: block;
        margin-top: 5px;
    }

    #spreadsheet-filename {
        font-size: 14px;
        word-break: break-word;
        max-width: 100%;
        text-align: center;
    }

    .artwork-progress-label {
        position: absolute;
        left: 50%;
        top: 0;
        transform: translateX(-50%);
        color: #222;
        font-weight: 500;
        line-height: 20px;
    }

    .artwork-progress-inner {
        background: #28a745;
        height: 100%;
        width: 0%;
        border-radius: 8px;
        transition: width 0.3s ease;
    }

    /* Highlight empty cells */
    .empty-cell {
        background-color: #ffe6e6 !important;
        border: 1px solid #ffcccc !important;
    }

    .empty-cell:focus {
        background-color: #ffe6e6 !important;
        border: 1px solid #007bff !important;
    }

    /* Submit Progress Modal Styles */
    #submitProgressModal .modal-content {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.2);
    }

    #submitProgressModal .modal-body {
        padding: 2rem;
    }

    #submitProgressModal .spinner-border {
        color: #2453e3 !important;
    }

    #submitProgressModal .progress {
        background-color: #e9ecef;
        border-radius: 10px;
        overflow: hidden;
    }

    #submitProgressModal .progress-bar {
        background: linear-gradient(90deg, #2453e3, #4a6cf7);
        border-radius: 10px;
    }

    #submitProgressModal h5 {
        color: #333;
        font-weight: 600;
    }

    #submitProgressModal p {
        font-size: 14px;
        color: #6c757d;
    }

    #submitProgressModal[data-bs-backdrop="static"] {
        background-color: rgba(0, 0, 0, 0.5);
    }

    /* Disabled submit button styles */
    #submit-artworks-btn:disabled {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #fff !important;
        opacity: 0.6;
        cursor: not-allowed;
    }

    #submit-artworks-btn:disabled:hover {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #fff !important;
        opacity: 0.6;
    }

    /* Mouse down visual feedback for submit button */
    #submit-artworks-btn:active {
        transform: translateY(1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
    }

    #submit-artworks-btn.mouse-down {
        transform: translateY(1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        background-color: #198754 !important;
        border-color: #198754 !important;
    }

    /* Disabled generate artwork button styles */
    #generate-artwork-btn:disabled {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #fff !important;
        opacity: 0.6;
        cursor: not-allowed;
    }

    #generate-artwork-btn:disabled:hover {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #fff !important;
        opacity: 0.6;
    }

    /* Hide Unit column temporarily */
    /* .table thead th:nth-child(7),
    .table tbody tr td:nth-child(7) {
        display: none !important;
    } */

    /* Pagination Controls Styles */
    #pagination-controls {
        background: #f8f9fa;
        border-radius: 8px;
        padding: 15px 20px;
        border: 1px solid #e9ecef;
    }

    .pagination-info {
        font-size: 14px;
        color: #6c757d;
    }

    .pagination-info span {
        font-weight: 500;
    }

    .pagination-buttons .btn {
        border-radius: 6px;
        font-size: 14px;
        padding: 6px 12px;
    }

    .pagination-buttons .btn:disabled {
        opacity: 0.5;
        cursor: not-allowed;
    }

    /* Uploaded page indicator */
    #submit-artworks-btn.btn-secondary {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #fff !important;
        opacity: 0.8;
        cursor: not-allowed;
    }

    #submit-artworks-btn.btn-secondary:hover {
        background-color: #6c757d !important;
        border-color: #6c757d !important;
        color: #fff !important;
        opacity: 0.8;
    }

    /* Page number buttons */
    .page-number-btn {
        margin: 0 2px;
        min-width: 35px;
        height: 35px;
        border-radius: 6px;
        font-size: 14px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .page-number-btn:hover {
        transform: translateY(-1px);
        box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }

    .page-number-btn.btn-primary {
        background-color: #2453e3;
        border-color: #2453e3;
        color: white;
    }

    .page-number-btn.btn-primary:hover {
        background-color: #1a3fa6;
        border-color: #1a3fa6;
        color: white;
    }

    .page-number-btn.btn-outline-secondary {
        border-color: #dee2e6;
        color: #6c757d;
    }

    .page-number-btn.btn-outline-secondary:hover {
        background-color: #e9ecef;
        border-color: #adb5bd;
        color: #495057;
    }

    /* Ellipsis styling */
    .page-numbers .text-muted {
        font-size: 14px;
        font-weight: 500;
        color: #6c757d;
    }

    /* Countdown Timer Styles */
    .countdown-container {
        font-size: 14px;
        margin-bottom: 10px;
    }

    #countdown-timer {
        font-size: 18px;
        font-weight: 700;
        color: #2453e3;
    }

    #countdown-progress {
        background: linear-gradient(90deg, #2453e3, #4a6cf7);
        border-radius: 2px;
        transition: width 0.3s ease;
    }

    /* Countdown animation for urgency */
    @keyframes countdownPulse {
        0% { opacity: 1; }
        50% { opacity: 0.7; }
        100% { opacity: 1; }
    }

    #countdown-timer.warning {
        color: #ffc107;
        animation: countdownPulse 1s infinite;
    }

    #countdown-timer.danger {
        color: #dc3545;
        animation: countdownPulse 0.5s infinite;
    }
    </style>
</div>

<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<script>

    const allCollections = @json($collections);

    const mainContainer = document.getElementById('show-collections-container');
    const uploadContainer = document.getElementById('upload-artwork-container');
    const isSuperAdmin = @json(auth()->user()->role === 'Super admin');

    const spreadUploadText = document.getElementById('spreadsheet-upload-text');
    const spreadProgress = document.getElementById('spreadsheet-progress');
    const spreadFilename = document.getElementById('spreadsheet-filename');

    const imageUploadText = document.getElementById('image-upload-text');
    const imageProgress = document.getElementById('image-progress');
    const imageFilename = document.getElementById('image-filename');

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

    // Helper function to get property value from multiple possible property names
    function getProperty(obj, propertyNames) {
        for (let propName of propertyNames) {
            if (obj[propName] !== undefined && obj[propName] !== null && obj[propName] !== '') {
                return obj[propName];
            }
        }
        return null;
    }

    document.addEventListener('DOMContentLoaded', function () {
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

        // Prevent default drag and drop behavior on the entire document
        document.addEventListener('dragover', function(e) {
            e.preventDefault();
        });

        document.addEventListener('drop', function(e) {
            e.preventDefault();
        });

        const deleteBtn = document.getElementById('bulkDeleteBtn');
        const checkboxes = document.querySelectorAll('.bulk-select-checkbox'); // Update selector as needed

        function updateDeleteBtnState() {
            const anyChecked = Array.from(checkboxes).some(cb => cb.checked);
            deleteBtn.disabled = !anyChecked;
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateDeleteBtnState);
        });

        updateDeleteBtnState(); // Initial state
        updateSubmitButtonState(); // Initial submit button state
        updateGenerateArtworkButtonState(); // Initial generate button state

        // Add mouse down feedback for submit button
        const submitBtn = document.getElementById('submit-artworks-btn');
        if (submitBtn) {
            submitBtn.addEventListener('mousedown', function() {
                if (!this.disabled) {
                    this.classList.add('mouse-down');
                }
            });

            submitBtn.addEventListener('mouseup', function() {
                this.classList.remove('mouse-down');
            });

            submitBtn.addEventListener('mouseleave', function() {
                this.classList.remove('mouse-down');
            });
        }
    });

    function handleOpenCollectionModal() {
        $('#addCollectionModal').modal('show');
    }

    function selectCollection(collectionId, collectionName, itemCount) {
        // Update the Livewire model - this will automatically update the button display
        @this.set('selectedCollection', collectionId);
    }

    function editCollection() {
        const selectedCollectionId = @this.selectedCollection;
        if (selectedCollectionId) {
            // Find the collection data
            const collection = allCollections.find(c => c.id == selectedCollectionId);
            if (collection) {
                // Populate the edit modal with collection data
                document.getElementById('collectionName').value = collection.name;
                document.getElementById('collectionCompany').value = collection.company_id || '';
                // You can add more fields as needed
                
                // Show the edit modal
                $('#addCollectionModal').modal('show');
            }
        }
    }

    function deleteCollection() {
        const selectedCollectionId = @this.selectedCollection;
        if (selectedCollectionId) {
            if (confirm('Are you sure you want to delete this collection? This action cannot be undone.')) {
                // Call Livewire method to delete collection
                @this.call('deleteCollection', selectedCollectionId);
                
                // Reset to "All Collections" after deletion
                @this.set('selectedCollection', '');
            }
        }
    }

    function updateCollectionDisplay() {
        // This function is called when the select dropdown changes
        // The Livewire model is already updated via wire:model.live
        // You can add any additional display updates here if needed
    }

    document.getElementById('collectionThumbnail').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('thumbnailText').innerHTML = `<img src="${e.target.result}" style="max-width: 100%; max-height: 80px;" />`;
            };
            reader.readAsDataURL(file);
        }
    });

    // Ensure upload container is hidden by default
    uploadContainer.style.display = 'none';

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
            ['Filename', 'Collection', 'Title', 'Artist', 'Height', 'Width', 'Unit', 'Description', 'Type']
        ];

        const rows = document.querySelectorAll('#artworkTableBody tr');
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            if (cells.length < 9) return;

            const rowData = [];
            const img = cells[0].querySelector('img');
            rowData.push(img ? img.getAttribute('data-filename') || '' : '');

            // 2. Collection
            const collectionSelect = cells[1].querySelector('select');
            rowData.push(collectionSelect && collectionSelect.value ? collectionSelect.options[collectionSelect.selectedIndex].text : '');

            rowData.push(cells[2].textContent.trim());
            rowData.push(cells[3].textContent.trim());
            rowData.push(cells[4].querySelector('input').value);
            rowData.push(cells[5].querySelector('input').value);
            const unitSelect = cells[6].querySelector('select');
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
        formData.append('collection_thumbnail', collectionThumbnail);

        const token = document.querySelector('meta[name="csrf-token"]').content;

        fetch('/inventory/collections/add', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': token,
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Collection added successfully');
                window.location.reload();
            } else {
                alert('Error: ' + (data.message || 'Could not add collection.'));
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error adding collection.');
        });
    }

    // Add Artwork button (add a new editable row)
    function handleAddRow() {
        const tbody = document.getElementById('artworkTableBody');
        const row = document.createElement('tr');
        const uniqueId = 'artwork-image-' + Date.now() + '-' + Math.floor(Math.random() * 10000);
        const masterUnitDropdown = document.getElementById('masterUnit');
        const masterCollectionDropdown = document.getElementById('masterCollectionHeader') || document.getElementById('masterCollectionStandalone');

        row.innerHTML = `
            <td>
                <div class="upload-box artwork-image-upload" style="width: 60px; height: 60px; min-height: 0; padding: 0; font-size: 12px; cursor: pointer;">
                    <span class="artwork-image-upload-text">Select or drag file</span>
                    <input type="file" accept=".png,.jpg,.jpeg" style="display:none;" id="${uniqueId}">
                </div>
            </td>
            <td style="width: 480px;">
                <select class="form-select artwork-collection-select ${!masterCollectionDropdown || !masterCollectionDropdown.value ? 'empty-cell' : ''}">
                    <option value="">Select Collection</option>
                    @foreach($collections as $collection)
                        <option value="{{$collection->id}}" ${masterCollectionDropdown && masterCollectionDropdown.value == {{$collection->id}} ? 'selected' : ''}>{{$collection->name}}</option>
                    @endforeach
                </select>
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
                const collectionSelect = cells[1].querySelector('select');

                const collectionName = collectionSelect.options[collectionSelect.selectedIndex].text;
                const unitSelect = cells[6].querySelector('select');
                const unitValue = unitSelect ? unitSelect.value : '';

                const rowData = {
                    collection_name: collectionName,
                    title: cells[2].textContent.trim(),
                    artist: cells[3].textContent.trim(),
                    height: cells[4].querySelector('input').value,
                    width: cells[5].querySelector('input').value,
                    description: cells[7].textContent.trim(),
                    type: cells[8].textContent.trim(),
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
                <td style="width: 480px;">
                    <select class="form-select artwork-collection-select">
                        @foreach($collections as $collection)
                            <option value="{{$collection->id}}" ${artwork.collectionId == {{$collection->id}} ? 'selected' : ''}>{{$collection->name}}</option>
                        @endforeach
                    </select>
                </td>
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
</script>
