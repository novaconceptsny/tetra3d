<div class="card-row projects-card-sidebar-wrapper" :class="{ 'sidebar-visible': sidebar }" x-data="{sidebar: false}">
    <x-loader/>
    <div class="col h-100">
        <div class="inner-row">
            <h5>{{ __('Your Projects') }}</h5>
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
                        <a class="col-btn" href="{{ route('tours.show', [$tour, 'project_id' => $selectedProject->id]) }}">{{ $tour->name }}</a>
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

                <div class="col-img position-relative">
                    @forelse($selectedProject->contributors as $contributor)
                        <img
                            src="{{ $contributor->avatar_url }}"
                            alt="{{ $contributor->name }}"
                            width="50"
                        />
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
