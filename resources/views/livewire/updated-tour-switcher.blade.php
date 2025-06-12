<!-- <style>
    .table {
        --bs-table-bg: transparent;
        --bs-table-border-color: #eee;
    }
    .table th {
        font-weight: 500;
        color: #666;
        border-bottom: 2px solid var(--bs-table-border-color);
        padding: 1rem;
    }
    .table td {
        padding: 1rem;
        vertical-align: middle;
        border-bottom: 1px solid var(--bs-table-border-color);
    }
    .btn-icon {
        padding: 0.375rem;
        line-height: 1;
        border: 1px solid #dee2e6;
        background: white;
    }
    .btn-icon:hover {
        background: #f8f9fa;
    }
    .gap-2 {
        gap: 0.5rem !important;
    }
</style> -->

<x-wire-elements-pro::bootstrap.slide-over :content-padding="false" :close-button="false">
    <div class="sidebar-div">
        <div class="sidebar mysidebar">
            <div class="preview">
                <h5 class="sidebar-heading d-flex align-items-start">
                    <span>{{ $project->name }}</span>
                    <!-- @can('update', $project)
                        <a class="fs-6 ms-3" href="{{ route('backend.projects.edit', $project) }}" target="_blank"><i
                                class="fal fa-edit"></i></a>
                    @endcan -->
                </h5>
                <a href="#" wire:slide-over="close" class="x text-decoration-none">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </div>
            <!-- <div class="date">
                <h6>{{ $project->created_at->format('M d, Y') }}</h6>
            </div> -->

            <div class="mb-3" style ="margin-top: 40px">
                <table class="table">
                    <tr>
                        <th>Layouts</th>
                        <th>Content</th>
                        <th class="text-end" style="width: 120px">Actions</th>
                    </tr>

                    @forelse($project->layouts()->orderBy('updated_at', 'desc')->get() as $layout)
                        <tr wire:key="{{ $layout->id }}">
                            <td>
                                <img :src="$wire.tourImages['{{ $layout->assignedTour()->id }}']">
                            </td>
                            <td>
                                <div class="d-flex flex-column justify-content-start gap-2">
                                    <div class="text-center">{{ $layout->name }}</div>
                                    <div class="text-center">Tour: {{ $layout->assignedTour()->name }}</div>
                                    <div class="text-center"> Modified: {{ $layout->updated_at->format('m/d/y') }}</div>
                                </div>
                            </td>
                            <td class="text-end">
                                <div class="d-flex flex-column justify-content-end gap-2">
                                    <div class="d-flex justify-content-end gap-2">
                                        <button class="btn btn-icon btn-sm" 
                                            wire:modal="forms.layout-form, @js(['project' => $project->id, 'layout' => $layout->id])">
                                            <i class="fal fa-edit"></i>
                                        </button>
                                        <button class="btn btn-icon btn-sm" wire:modal="forms.duplicate, @js(['layout' => $layout->id])">
                                            <i class="fa fa-copy"></i>
                                        </button>
                                        <button class="btn btn-icon btn-sm" wire:click="deleteLayout(@js($layout->id))" onclick="return confirm('Are you sure you want to delete this layout?')">
                                            <i class="fa fa-trash"></i>
                                        </button>
                                    </div> 
                                    <a href="{{ route('tours.show', [$layout->tour_id, 'layout_id' => $layout->id]) }}" 
                                        class="btn btn-primary btn-sm">
                                        Enter
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4">
                                <p class="text-muted mb-3">No layouts created yet</p>
                                <button class="btn btn-primary"
                                    wire:modal="forms.layout-form, @js(['project' => $project->id])">
                                    New Layout <i class="fal fa-plus ms-2"></i>
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </table>

                @if($project->layouts->count())
                    <div class="text-start">
                        <button class="btn btn-light btn sm"
                            wire:modal="forms.layout-form, @js(['project' => $project->id])">
                            Create Layout <i class="fal fa-plus ms-2"></i>
                        </button>
                    </div>
                @endif
            </div>

            <div class="collection mt-5">
                <h5 class="d-flex align-items-center">
                    <span>Collections</span>
                    <!-- @can('update', $project)
                        <a class="fs-6 ms-3" href="{{ route('backend.projects.edit', $project) }}" target="_blank"><i
                                class="fal fa-edit"></i></a>
                    @endcan -->
                </h5>
                <div class="sidebar-collection-btn-wrapper">

                    @forelse($project->artworkCollections as $collection)
                        <a href="{{ route('artworks.index', ['collection_id' => $collection->id]) }}" target="_blank"
                            class="col-btn">{{ $collection->name }}</a>
                    @empty
                        <span class="text-center d-block">{{ __('No collections') }}</span>
                    @endforelse
                </div>
            </div>

            <div class="contributor">
                <h5 class="d-flex align-items-center">
                    <span>Contributors</span>
                    <!-- @can('update', $project)
                        <a class="fs-6 ms-3" href="{{ route('backend.projects.edit', $project) }}" target="_blank"><i
                                class="fal fa-edit"></i></a>
                    @endcan -->
                </h5>
                <div class="img-container d-flex">
                    @forelse($project->contributors as $contributor)
                        <div class="name-tip" data-text="{{ $contributor->name }}">
                            <img src="{{ $contributor->avatar_url }}" alt="{{ $contributor->name }}" />
                        </div>
                    @empty
                        <span class="text-center d-block">{{ __('No contributors') }}</span>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-wire-elements-pro::bootstrap.slide-over>
@section('content')
<div class="modal fade" id="duplicat_confirmation" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Save Canvas</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>Save art arrangement as:</p>
                <form>
                    <div class="form-row">
                        <input type="text" class="form-control ml-2 mr-2" placeholder="assignment1" id="file_name">
                        <div class="invalid-feedback ml-3">
                            Invalid file name provided.
                        </div>
                    </div>
                </form>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                <button type="button" class="btn btn-outline-secondary" id="confirm_save_btn">Save Changes
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

