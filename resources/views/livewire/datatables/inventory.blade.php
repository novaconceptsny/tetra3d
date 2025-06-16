<div class="bg-light-page">
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
                        @if($collection->image_url)
                            <img src="{{ $collection->image_url }}" alt="" width="40" class="me-2 rounded">
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
                        <div class="btn-group ms-auto" role="group" aria-label="Artwork Actions">
                            <button type="button" class="btn btn-light" title="Add">
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
                    <button type="button" class="btn btn-save-collection" id="saveCollectionBtn">Save</button>
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

    </style>
</div>

<script>

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

</script>