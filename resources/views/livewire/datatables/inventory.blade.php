<div class="bg-light-page">
    <div id="show-collections-container" style="display: block;">
        <div class="d-flex">
            <!-- Sidebar -->
            <div class="collections-sidebar" style="width: 280px; min-width: 220px; background: #f8f9fa; border-radius: 12px; margin-right: 24px;">
                <div class="card shadow-sm border-0 rounded-4 p-3" style="background: #fff;">
                    <h5>Collections</h5>
                    <ul class="list-group" id="collectionsContainer">
                        <li class="list-group-item d-flex align-items-center border rounded p-2 mb-2" id="addCollectionBtn">
                            <button class="add-collection-btn" data-bs-toggle="modal" data-bs-target="#addCollectionModal" onclick="handleOpenCollectionModal()">
                                <span class="icon-circle"><i class="fas fa-plus"></i></span>
                                <span class="add-collection-text">Add Collection</span>
                            </button>
                        </li>
                        @foreach($collections as $collection)
                        <li class="list-group-item d-flex align-items-center border rounded p-2 mb-2" data-module="artworks" data-id="${collection.id}">
                            @if($collection->thumbnail_url)
                                <img src="{{ $collection->thumbnail_url }}" alt="" width="40" class="me-2 rounded">
                            @else
                                <i class="fas fa-image collection-icon"></i>
                            @endif
                            <div class="collection-info">
                                <span class="collection-name">{{ $collection->name }}</span>
                                <span class="collection-items">{{ $collection->artworks()->count() }} items</span>
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
                        @endforeach
                        <li class="list-group-item d-flex align-items-center border rounded p-2 mb-2" data-module="artworks">
                            <i class="fas fa-image collection-icon"></i>
                            <div class="collection-info">
                                <span class="collection-name">All</span>
                                <span class="collection-items">0  items</span>
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
                    </ul>
                </div>
            </div>
            <!-- Main Content -->
            <div class="flex-grow-1">
                <div class="card shadow-sm border-0 rounded-4 p-3" style="background: #fff;">
                    <x-loader/>
                    
                    <div class="card-header d-flex flex-column">
                        <div class="d-flex mb-2">
                            <h5 class="me-auto">{{ $heading }}</h5>
                            <div class="float-end">
                                @include('backend.includes.datatable.bulk-delete')
                            </div>
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
                                <button type="button" class="btn btn-light" title="Delete">
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
                <button class="btn btn-primary" id="download-template-btn" onclick="downloadSpreadsheet()">Download spreadsheet template</button>
            </div>
            <div class="row mb-4">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Upload spreadsheet</label>
                    <div class="upload-box" id="spreadsheet-upload">
                        <span>Drag & drop a file here<br>or choose file</span>
                        <input type="file" class="form-control-file" style="display:none;" id="spreadsheetInput">
                    </div>
                </div>
                <div class="col-md-6 mb-3">
                    <label class="form-label">Upload image files</label>
                    <div class="upload-box" id="image-upload">
                        <span>Drag & drop a file here<br>or choose file</span>
                        <input type="file" class="form-control-file" style="display:none;" id="imageInput" multiple>
                    </div>
                </div>
            </div>
            <!-- Artworks Table -->
            <div class="table-responsive mb-3">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Image</th>
                            <th>Collection</th>
                            <th>Title</th>
                            <th>Artist</th>
                            <th>Height (inch)</th>
                            <th>Width (inch)</th>
                            <th>Type</th>
                            <th></th>
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
            <div class="d-flex justify-content-end">
                <button class="btn btn-outline-primary" id="add-artwork-btn" style="display: none;" onclick="handleAddRow()">Add Artwork</button>
                <button class="btn btn-success" id="submit-artworks-btn" onclick="handleSubmitArtworks()">Submit</button>
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
        height: 450px;
        min-height: 450px;
        max-height: 450px;
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

    </style>
</div>

<script src="https://cdn.sheetjs.com/xlsx-0.20.1/package/dist/xlsx.full.min.js"></script>
<script>

    const allCollections = @json($collections);
    const mainContainer = document.getElementById('show-collections-container');
    const uploadContainer = document.getElementById('upload-artwork-container');
    const isSuperAdmin = @json(auth()->user()->role === 'Super admin');

    function handleOpenCollectionModal() {
        $('#addCollectionModal').modal('show');
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

    // Find the "Add" button (the first .btn-light with title="Add")
    function handleOpenUploadArtworks() {
        mainContainer.style.display = 'none';
        uploadContainer.style.display = 'block';
    }

    function backToCollections() {
        uploadContainer.style.display = 'none';
        mainContainer.style.display = 'block'; // or 'block' if flex doesn't work
    }

    // Click on upload box triggers file input
    document.getElementById('spreadsheet-upload').onclick = () => document.getElementById('spreadsheetInput').click();
    document.getElementById('image-upload').onclick = () => document.getElementById('imageInput').click();


    function downloadSpreadsheet() {    
        // Get all rows from the table
        const rows = document.querySelectorAll('#artworkTableBody tr');
        const data = [];

        // Add header row
        data.push([
            'ImageName',
            'Collection',
            'Title',
            'Artist',
            'Height (inch)',
            'Width (inch)',
            'Type'
        ]);

        // Process each row
        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const rowData = [];

            // Skip the last cell (remove button)
            for (let i = 0; i < cells.length - 1; i++) {
                const cell = cells[i];
                
                if (cell.querySelector('img')) {
                }
                // Handle different types of cells
                if (cell.querySelector('select')) {
                    // For collection dropdown
                    const select = cell.querySelector('select');
                    rowData.push(select.value ? select.options[select.selectedIndex].text : '');
                } else if (cell.querySelector('input')) {
                    // For number inputs
                    rowData.push(cell.querySelector('input').value);
                } else if (cell.contentEditable === 'true') {
                    // For editable cells
                    rowData.push(cell.textContent.trim());
                } else if (cell.querySelector('img')) {
                    // For image cells
                    const img = cell.querySelector('img');
                    const filename = img.getAttribute('data-filename') || '';
                    rowData.push(filename);
                } else {
                    rowData.push(cell.textContent.trim());
                }
            }

            data.push(rowData);
        });

        // Create workbook and worksheet
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.aoa_to_sheet(data);

        // Add worksheet to workbook
        XLSX.utils.book_append_sheet(wb, ws, "Artworks");

        // Generate Excel file and trigger download
        XLSX.writeFile(wb, "artworks.xlsx");
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
        row.innerHTML = `
            <td></td>
            <td style="width: 480px;">
                <select class="form-select">
                    <option value="">Select Collection</option>
                    @foreach($collections as $collection)
                        <option value="{{$collection->id}}">{{$collection->name}}</option>
                    @endforeach
                </select>
            </td>
            <td contenteditable="true"></td>
            <td contenteditable="true"></td>
            <td><input type="number" class="form-control" style="width: 100px; min-width: 60px;" /></td>
            <td><input type="number" class="form-control" style="width: 100px; min-width: 60px;" /></td>
            <td contenteditable="true"></td>
            <td><button class="btn btn-danger btn-sm">Remove</button></td>
        `;
        row.querySelector('button').onclick = function() { row.remove(); };
        tbody.appendChild(row);
    }

    function handleSubmitArtworks() {
        const tbody = document.getElementById('artworkTableBody');
        const rows = tbody.querySelectorAll('tr');
        const data = [];

        rows.forEach(row => {
            const cells = row.querySelectorAll('td');
            const rowData = [];

            cells.forEach(cell => {
                rowData.push(cell.textContent.trim());
            });

            data.push(rowData);
        });

        console.log(data, "data");

    }

    // Remove row
    document.querySelectorAll('#artworkTableBody .btn-danger').forEach(btn => {
        btn.onclick = function() { btn.closest('tr').remove(); };
    });


    document.getElementById('imageInput').addEventListener('change', function(event) {
        const files = event.target.files;
        const tbody = document.getElementById('artworkTableBody');

        for (let i = 0; i < files.length; i++) {
            const file = files[i];
            const reader = new FileReader();

            reader.onload = function(e) {
                const row = document.createElement('tr');
                // Get filename from the original file
                const filename = file.name;
                row.innerHTML = `
                    <td><img src="${e.target.result}" data-filename="${filename}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;"></td>
                    <td style="width: 480px;">
                        <select class="form-select">
                            <option value="">Select Collection</option>
                            @foreach($collections as $collection)
                                <option value="{{$collection->id}}">{{$collection->name}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td contenteditable="true"></td>
                    <td contenteditable="true"></td>
                    <td><input type="number" class="form-control" style="width: 100px; min-width: 60px;" /></td>
                    <td><input type="number" class="form-control" style="width: 100px; min-width: 60px;" /></td>
                    <td>
                        <select class="form-select">
                            <option value="">Select Type</option>
                            <option value="Painting">Painting</option>
                            <option value="Sculpture">Sculpture</option>
                        </select>
                    </td>
                    <td><button class="btn btn-danger btn-sm">Remove</button></td>
                `;
                row.querySelector('button').onclick = function() { row.remove(); };
                tbody.appendChild(row);
            };

            reader.readAsDataURL(file);
        }

        event.target.value = '';
    });

    // Add spreadsheet input event listener
    document.getElementById('spreadsheetInput').addEventListener('change', function(event) {
        const file = event.target.files[0];
        if (!file) return;

        const reader = new FileReader();
        reader.onload = function(e) {
            const data = new Uint8Array(e.target.result);
            const workbook = XLSX.read(data, { type: 'array' });
            
            // Get the first worksheet
            const firstSheet = workbook.Sheets[workbook.SheetNames[0]];
            
            // Convert to JSON
            const jsonData = XLSX.utils.sheet_to_json(firstSheet, { header: 1 });
            
            // Skip header row and process data
            const tbody = document.getElementById('artworkTableBody');
            
            // Clear existing rows
            tbody.innerHTML = '';
            
            // Process each row starting from index 1 (skip header)
            for (let i = 1; i < jsonData.length; i++) {
                const row = jsonData[i];
                if (!row || row.length === 0) continue;

                const newRow = document.createElement('tr');
                newRow.innerHTML = `
                    <td><img src="" data-filename="${row[0] || ''}" style="width:40px;height:40px;object-fit:cover;border-radius:6px;"></td>
                    <td style="width: 480px;">
                        <select class="form-select">
                            <option value="">Select Collection</option>
                            @foreach($collections as $collection)
                                <option value="{{$collection->id}}">{{$collection->name}}</option>
                            @endforeach
                        </select>
                    </td>
                    <td contenteditable="true">${row[1] || ''}</td>
                    <td contenteditable="true">${row[2] || ''}</td>
                    <td><input type="number" class="form-control" style="width: 100px; min-width: 60px;" value="${row[3] || ''}" /></td>
                    <td><input type="number" class="form-control" style="width: 100px; min-width: 60px;" value="${row[4] || ''}" /></td>
                    <td>
                        <select class="form-select">
                            <option value="">Select Type</option>
                            <option value="Painting" ${row[5] === 'Painting' ? 'selected' : ''}>Painting</option>
                            <option value="Sculpture" ${row[5] === 'Sculpture' ? 'selected' : ''}>Sculpture</option>
                        </select>
                    </td>
                    <td><button class="btn btn-danger btn-sm">Remove</button></td>
                `;
                
                // Add remove button functionality
                newRow.querySelector('button').onclick = function() { newRow.remove(); };
                
                tbody.appendChild(newRow);
            }
        };
        
        reader.readAsArrayBuffer(file);
        event.target.value = ''; // Reset input
    });
</script>