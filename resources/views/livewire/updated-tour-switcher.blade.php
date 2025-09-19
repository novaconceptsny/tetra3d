
<div>
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
                    <div>
                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <h5 class="m-0 fw-normal">Layouts</h5>
                            <a href="#" class="new-layout-button" wire:modal="forms.layout-form, @js(['project' => $project->id])">+ New Layout</a>


                        </div>
                        <div class="d-flex flex-column gap-3">
                            @forelse($project->layouts()->orderBy('updated_at', 'desc')->get() as $layout)
                                <div class="layout-card d-flex align-items-start">
                                    <div class="layout-card-container">
                                        <div class="layout-favorite">
                                            <i class="fa{{ $layout->is_favorite ? 's' : 'r' }} fa-star layout-star" 
                                               wire:click="toggleFavorite({{ $layout->id }})"
                                               style="color: {{ $layout->is_favorite ? '#099F9A' : '' }};"></i>
                                        </div>
                                        <div class="layout-preview">
                                            <img :src="$wire.tourImages['{{ $layout->assignedTour()->id }}']">
                                        </div>
                                    </div>
                                    <div class="layout-info-container">
                                        <div class="layout-info">
                                                <div class="layout-title">{{ $layout->name }}</div>
                                                <div class="layout-meta">Tour: {{ $layout->assignedTour()->name }}</div>
                                                <div class="layout-meta">Modified: {{ $layout->updated_at->format('m/d/y') }}</div>
                                        </div>
                                        <div class="layout-actions">
                                            <div class="d-flex justify-content-end" style="gap: 0.5px;">
                                                <button class="btn btn-icon btn-sm" wire:modal="forms.layout-form, @js(['project' => $project->id, 'layout' => $layout->id])">
                                                    <i class="fal fa-edit"></i>
                                                </button>
                                                <button class="btn btn-icon btn-sm" wire:modal="forms.duplicate, @js(['layout' => $layout->id])">
                                                    <i class="fa fa-copy"></i>
                                                </button>
                                                <button class="btn btn-icon btn-sm" type="button" wire:click="deleteLayout({{ $layout->id }})">
                                                    <i class="fa fa-trash"></i>
                                                </button>
                                            </div>
                                            <a href="{{ route('tours.show', [$layout->tour_id, 'layout_id' => $layout->id]) }}" class="btn-enter">
                                                <!-- <i class="fa fa-arrow-right"></i> -->
                                                Enter
                                            </a>
                                        </div>
                                    </div>

                                </div>
                            @empty
                                <div class="text-center p-4">
                                    <p class="mb-3">No layouts created yet</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <div class="collection mt-5">
                    <h5 class="d-flex align-items-center fw-normal">
                        <span>Collections</span>
                        <!-- @can('update', $project)
                            <a class="fs-6 ms-3" href="{{ route('backend.projects.edit', $project) }}" target="_blank"><i
                                    class="fal fa-edit"></i></a>
                        @endcan -->
                    </h5>
                    <div class="sidebar-collection-btn-wrapper">

                        @forelse($project->artworkCollections as $collection)
                            <a href="{{ route('artworks.index', ['collection_id' => $collection->id]) }}" target="_blank"
                                class="col-btn rounded light-grey-box">{{ $collection->name }}</a>
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

    <style>
        .layout-card {
            border: 1px solid #e0e0e0;
            border-radius: 8px;
            padding: 1rem;
            background: white;
            transition: all 0.2s ease;
        }

        .layout-card:hover {
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .layout-card-container {
            display: flex;
            gap: 1rem;
        }
        .layout-info-container {
            display: flex;
            align-items: center;
            justify-content: space-between;
            width: 100%;
            padding: 20px;
        }
        .layout-favorite {
            position: relative;
            display: flex;
            align-items: top;
            justify-content: start;
            width: 10px;
            height: 10px;
        }
        .layout-preview {
            width: 210px;
            height: 120px;
            border: 1px solid #e0e0e0;
            border-radius: 4px;
            overflow: hidden;
            position: relative;
            padding: 10px;
        }

        .layout-preview img {
            width: 100%;
            height: 100%;
            object-fit: contain;
            object-position: center;
        }

        /* Responsive adjustments for smaller screens */
        @media (max-width: 768px) {
            .layout-preview {
                width: 100%;
                max-width: 180px;
                height: 100px;
            }
            
            .layout-card-container {
                flex-direction: column;
                gap: 0.5rem;
            }
            
            .layout-info-container {
                padding: 10px;
            }
        }

        @media (max-width: 480px) {
            .layout-preview {
                width: 100%;
                max-width: 150px;
                height: 80px;
            }
        }

        .layout-star {
            position: absolute;
            top: 8px;
            left: 8px;
            color: #4a90e2;
            font-size: 16px;
            cursor: pointer;
            transition: transform 0.2s ease;
        }

        .layout-star:hover {
            transform: scale(1.1);
        }

        .far.layout-star {
            color: #666;
        }

        .fas.layout-star {
            color:rgb(11, 27, 248);
        }

        .layout-info {
            margin-left: 1rem;
        }

        .layout-title {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 0.5rem;
            color: #666;
        }

        .layout-meta {
            font-size: 16px;
            color: #666;
        }

        .layout-actions {
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
            align-items: flex-end;
        }

        .btn-enter {
            display: flex;
            align-items: center;
            justify-content: center;
            background: transparent;
            color: #099F9A;
            padding: 0.5rem;
            border-radius: 4px;
            text-decoration: none;
            border: 1px solid #099F9A;
            height: 32px;
            transition: all 0.2s ease;
        }

        .btn-enter:hover {
            background: #099F9A;
            color: white;
        }

        .new-layout-button {
            color: #099F9A;
            text-decoration: none;
            font-weight: 500;
        }
        
        .new-layout-button:hover {
            color: #099F9A;
        }

        /* Collection box styling */
        .light-grey-box {
            background-color: #f5f5f5;
            border: 1px solid #e0e0e0;
            padding: 0.75rem 1rem;
            color: #333;
            text-decoration: none;
            display: inline-block;
            margin-bottom: 0.5rem;
            transition: all 0.2s ease;
        }
        
        .light-grey-box:hover {
            background-color: #e8e8e8;
            color: #333;
        }
        
        /* Action button hover colors */
        .btn-icon:hover {
            color: #099F9A !important;
        }
        
        /* Hide contributors section */
        .contributor {
            display: none;
        }
    </style>

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
</div>
