<div class="card-row projects-card-sidebar-wrapper">
    <x-loader/>
    <div class="col h-100">
        <div class="inner-row">
            <div class="row d-flex align-items-center justify-content-between">
                <h5>{{ __('Exhibitions') }}</h5>
                <div class="sorted-btn">
                    <span>Sort by:</span>
                    <div class="input-group custom-select">

                        <select wire:model.live="sortBy" class="form-select c-select"
                                aria-label="Default select example">
                            <option value="name">Name A to Z</option>
                            <option value="created_at">Recently added</option>
                            <option value="updated_at">Recently edited</option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row project-cards-wrapper">
                @foreach($projects as $project)
                    <div class="card-col" wire:key="{{ $project->id }}">
                        <div class="c-card card ">
                            <div class="card-head">
                                <div class="card-header mb-2 border-0">
                                    <h6>{{ $project->name }}</h6>
                                    <p>Created: <span>{{ $project->created_at->format('M d, Y') }}</span></p>
                                </div>

                            </div>
                            <div class="card-text">
                                <div class="c-line"></div>
                                <div class="text">
                                    <p>{{ $project->layouts_count }} {{ str('Layout')->plural($project->layouts_count) }}</p>
                                    <p>{{ $project->artwork_collections_count }} {{ str('Collection')->plural($project->artwork_collections_count) }}</p>
                                </div>
                            </div>

                            <div class="card-footer">
                                <div class="card-imgs">
                                    <div class="images-container">
                                        @forelse($project->contributors->take(4) as $contributor)
                                                <div class="img-div bg-white" data-text="{{ $contributor->name }}">
                                                    <img src="{{ $contributor->avatar_url }}" alt=""/>
                                                </div>
                                                @empty
                                            <p>No Contributors Yet</p>
                                        @endforelse

                                        @if($project->contributors->count() > 4)
                                            <div class="more-btn">
                                                <i class="fas fa-plus"></i>
                                                <span>{{ $project->contributors->count() - 4 }}</span>
                                            </div>
                                            <div class="contributors-list">
                                                @foreach($project->contributors->skip(4) as $contributor)
                                                    <div class="contributor-name">{{ $contributor->name }}</div>
                                                @endforeach
                                            </div>
                                        @endif

                                    </div>
                                </div>
                                <div class="link-div">
                                    <a href="javascript:void(0)"
                                       wire:slide-over="tour-switcher, @js(['project' => $project->id])"
                                    >Enter
                                        {{--                                        <div><i class="fa-solid fa-chevron-right"></i></div>--}}
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>
