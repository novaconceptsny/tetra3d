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

                                    <div class="card-body py-0">
                                        
                                        <div class="table-responsive">
                                            <table id="inventoryTable" class="table table-striped table-bordered" style="width:100%">
                                                <thead>
                                                    <tr>
                                                        <th></th>
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
        ajax: {
            url: '{{ route("inventory.data") }}',
            type: 'GET',
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
                    return '<input type="checkbox" class="form-check-input">';
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
        responsive: true,
        initComplete: function(settings, json) {
            console.log('DataTable initialization complete');
            console.log('Data received:', json);
        }
    });

    console.log('DataTable created successfully');

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
});
</script>
@endsection
