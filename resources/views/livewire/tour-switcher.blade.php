<x-wire-elements-pro::bootstrap.slide-over :content-padding="false" :close-button="false">
    <div class="sidebar-div">
        <div class="sidebar mysidebar">
            <div class="preview">
                <h5 class="sidebar-heading d-flex align-items-start">
                    <span>{{ $project->name }}</span>
                    @can('update', $project)
                        <a class="fs-6 ms-3" href="{{ route('backend.projects.edit', $project) }}" target="_blank"><i
                                class="fal fa-edit"></i></a>
                    @endcan
                </h5>
                <a href="#" wire:slide-over="close" class="x text-decoration-none">
                    <i class="fa-solid fa-xmark"></i>
                </a>
            </div>
            <div class="date">
                <h6>{{ $project->created_at->format('M d, Y') }}</h6>
            </div>

            <div class="mb-3">
                <table class="table">
                    <tr>
                        <th>Name</th>
                        <th>Configuration</th>
                        <th>Created By</th>
                        <th>Created At</th>
                        <th></th>
                    </tr>

                    @forelse($project->layouts()->latest()->get() as $layout)
                        <tr wire:key="{{ $layout->id }}">
                            <td class="layout-table-title">
                                <span>{{ $layout->name }}</span>
                                <a class="edit-btn ms-1 text-info text-decoration-none" href="#"
                                    wire:modal="forms.layout-form, @js(['project' => $project->id, 'layout' => $layout->id])">
                                    <i class="fal fa-edit"></i>
                                </a>
                            </td>
                            <td>{{ $layout->tour->name }}</td>
                            <td>
                                <span>{{ $layout->user->name }}</span><br>
                            </td>
                            <td>
                                <span>{{ $layout->created_at->format('M d, Y H:i') }}</span>
                            </td>
                            <td>
                                <button class="text-dark tour-show" href="#" wire:click="duplicateLayout({{ $layout->id }})"><i class="fa fa-files-o"></i></button>
                            </td>
                            <td>
                                <button class="text-dark tour-show"
                                    onClick="window.location.href='{{ route('tours.show', [$layout->tour_id, 'layout_id' => $layout->id]) }}'">
                                    <i class="fal fa-sign-in"></i>
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="pb-5 text-capitalize text-center">
                                <h6 class="mb-3">No layout created</h6>
                                <button class="btn btn-light btn sm"
                                    wire:modal="forms.layout-form, @js(['project' => $project->id])">
                                    Create Layout <i class="fal fa-plus ms-2"></i>
                                </button>
                            </td>
                        </tr>
                    @endforelse
                </table>

                @if($project->layouts->count())
                    <div class="text-end">
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
                    @can('update', $project)
                        <a class="fs-6 ms-3" href="{{ route('backend.projects.edit', $project) }}" target="_blank"><i
                                class="fal fa-edit"></i></a>
                    @endcan
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
                    @can('update', $project)
                        <a class="fs-6 ms-3" href="{{ route('backend.projects.edit', $project) }}" target="_blank"><i
                                class="fal fa-edit"></i></a>
                    @endcan
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

@section('scripts')
<script type="module">
    function duplicateLayout(layoutId) {
        console.log("duplicateLayout")
        Livewire.emit('duplicateLayout', layoutId)
    }
</script>
@endsection