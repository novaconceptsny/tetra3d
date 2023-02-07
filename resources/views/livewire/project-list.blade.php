<div class="card-row projects-card-sidebar-wrapper" :class="{ 'sidebar-visible': sidebar }" x-data="{sidebar: false}">
    <x-loader/>
    <div class="col h-100">
        <div class="inner-row">
            <div class="row d-flex align-items-center justify-content-between">
                <h5>{{ __('Your Projects') }}</h5>
                <a href="#" class="sorted-btn"><svg width="34" height="34" viewBox="0 0 34 34" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M4.25 15.5833V4.25H15.5833V15.5833H4.25ZM4.25 29.75V18.4167H15.5833V29.75H4.25ZM18.4167 15.5833V4.25H29.75V15.5833H18.4167ZM18.4167 29.75V18.4167H29.75V29.75H18.4167ZM7.08333 12.75H12.75V7.08333H7.08333V12.75ZM21.25 12.75H26.9167V7.08333H21.25V12.75ZM21.25 26.9167H26.9167V21.25H21.25V26.9167ZM7.08333 26.9167H12.75V21.25H7.08333V26.9167Z" fill="#222436"/>
                    </svg>
                    Sorted By Dates</a>
            </div>
            <div class="row project-cards-wrapper">
                @foreach($projects as $project)
                    <div class="col-sm-6 col-xl-4 col-xxl-3 card-col">
                        <div class="card" wire:click="selectProject({{$project->id}})" @click="sidebar = true">
                            <div class="card-img">
                                <img src="{{ $project->getFirstMediaUrl('thumbnail') }}" alt=""/>
                            </div>
                            <div class="card-body">
                                <div class="card-title">{{ $project->name }}</div>
                                <p>{{ $project->created_at->format('M d, Y') }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    <div class="sidebar mysidebar">
        @if($selectedProject)
            <div class="preview">
                <h5>{{ $selectedProject?->name }}</h5>
                <a href="#" class="x text-decoration-none" @click="sidebar = false">
                    <i class="fal fa-times"></i>
                </a>
            </div>
            @if($selectedProject->getFirstMediaUrl('thumbnail'))
                <img src="{{ $selectedProject->getFirstMediaUrl('thumbnail') }}" alt="{{$selectedProject->name}}"/>
            @endif
            <div class="date">
                <h6>{{ $selectedProject->created_at->format('M d, Y') }}</h6>
            </div>
            <div class="tours">
                <h5>{{ __('Select Tours') }}</h5>
                <div class="dbl">
                    @forelse($selectedProject->tours as $tour)
                        <a class="col-btn"
                           href="{{ route('tours.show', [$tour, 'project_id' => $selectedProject->id]) }}">{{ $tour->name }}</a>
                    @empty
                        <span class="text-center d-block">No tours configured</span>
                    @endforelse
                </div>
            </div>
            <div class="collection">
                <h5>{{ __('Collections') }}</h5>
                @forelse($selectedProject->artworkCollections as $collection)
                    <a href="{{ route('artworks.index', ['collection_id' => $collection->id]) }}"
                       class="col-btn">{{ $collection->name }}</a>
                @empty
                    <span class="text-center d-block">{{ __('No collections selected') }}</span>
                @endforelse

                <div class="img-container d-flex">
                    @forelse($selectedProject->contributors as $contributor)
                        <div class="name-tip" data-text="{{ $contributor->name }}">
                            <img
                                src="{{ $contributor->avatar_url }}"
                                alt="{{ $contributor->name }}"
                                width="50"
                            />
                        </div>
                    @empty
                        <span class="text-center d-block">{{ __('No contributors') }}</span>
                    @endforelse
                </div>
            </div>
        @else
            <h5>Loading Project...</h5>
        @endif
    </div>
</div>
