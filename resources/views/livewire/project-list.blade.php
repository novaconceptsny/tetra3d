<div class="card-row projects-card-sidebar-wrapper" :class="{ 'sidebar-visible': sidebar }" x-data="{sidebar: false}">
    <x-loader/>
    <div class="col h-100">
        <div class="inner-row">
            <div class="row d-flex align-items-center justify-content-between">
                <h5>{{ __('Your Projects') }}</h5>
                <div class="sorted-btn">
                    <span>Sort by:</span>
                    <div class="input-group">
                        <select wire:model="sortBy">
                            <option value="name">Name A to Z</option>
                            <option value="created_at">Recently added</option>
                            <option value="updated_at">Recently edited</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row project-cards-wrapper">
                @foreach($projects as $project)
                    <div class="col-sm-6 col-xl-4 col-xxl-3 card-col">
                        <div class="c-card card ">
                            <div class="card-header mb-2">{{ $project->name }}</div>
                            <div class="card-text">
                                <div class="c-line"></div>
                                <div class="text">
                                    <p>{{ $project->tours_count }} {{ str('Tour')->plural($project->tours_count) }}</p>
                                    <p>{{ $project->artwork_collections_count }} {{ str('Artwork Collection')->plural($project->artwork_collections_count) }}</p>
                                </div>
                            </div>
                            <div class="card-body">
                                <div class="card-imgs">
                                    <div class="images-container">
                                        @forelse($project->contributors as $contributor)
                                            <div class="img-div" data-text="{{ $contributor->name }}">
                                                <img src="{{ $contributor->avatar_url }}" alt=""/>
                                            </div>
                                        @empty
                                            <p>No Contributors Yet</p>
                                        @endforelse
                                    </div>
                                </div>
                            </div>
                            <div class="card-footer">
                                <p>Created: <span>{{ $project->created_at->format('M d, Y') }}</span></p>
                                <div class="link-div">
                                    <a href="javascript:void(0)"
                                       wire:click="selectProject({{$project->id}})" @click="sidebar = true">View more
                                        <div><i class="fa-solid fa-chevron-right"></i></div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="sidebar-div" x-show="sidebar">
        <div class="sidebar mysidebar" @click.away="sidebar = false" @keydown.window.escape="sidebar = false">
            <div wire:loading.remove wire:target="selectProject">
                @if($selectedProject)
                    <div class="preview">
                        <h5 class="sidebar-heading">{{ $selectedProject->name }}</h5>
                        <a href="#" @click="sidebar = false" class="x text-decoration-none">
                            <i class="fa-solid fa-xmark"></i>
                        </a>
                    </div>
                    <div class="date">
                        <h6>{{ $selectedProject->created_at->format('M d, Y') }}</h6>
                    </div>
                    <div class="select-tours">
                        <h5>Select Tours</h5>
                    </div>
                    @if($selectedTour && $selectedTour->getFirstMediaUrl('thumbnail'))
                        <div class="sidebar-main-img mx-0">
                            <img src="{{ $selectedTour->getFirstMediaUrl('thumbnail') }}" alt="preview-img"/>
                        </div>
                    @endif

                    <div class="select-box">
                        <div class="input-group mt-4 border-1">
                            <select wire:model="selectedTourId" wire:change="selectTour" class="form-control">
                                @foreach($selectedProject->tours as $tour)
                                    <option value="{{ $tour->id}}">{{ $tour->name }}</option>
                                @endforeach
                            </select>
                            @if($selectedTourId)
                                <a href="{{ route('tours.show', [$selectedTourId, 'project_id' => $selectedProject->id]) }}"
                                   class="input-group-text bg-white text-decoration-none">Enter
                                </a>
                            @endif
                        </div>
                    </div>

                    <div class="collection">
                        <h5>Artwork Collection</h5>
                        @forelse($selectedProject->artworkCollections as $collection)
                            <a href="{{ route('artworks.index', ['collection_id' => $collection->id]) }}"
                               target="_blank"
                               class="col-btn">{{ $collection->name }}</a>
                        @empty
                            <span class="text-center d-block">{{ __('No collections') }}</span>
                        @endforelse
                    </div>
                    <div class="contributor">
                        <h5>Contributors</h5>
                        <div class="img-container d-flex">
                            @forelse($selectedProject->contributors as $contributor)
                                <div class="name-tip" data-text="{{ $contributor->name }}">
                                    <img src="{{ $contributor->avatar_url }}" alt="{{ $contributor->name }}"/>
                                </div>
                            @empty
                                <span class="text-center d-block">{{ __('No contributors') }}</span>
                            @endforelse
                        </div>
                    </div>
                @endif
            </div>
            <h5 wire:loading wire:target="selectProject">Loading Project...</h5>
        </div>
    </div>
</div>
