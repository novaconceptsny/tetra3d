@extends('layouts.master')

@section('content')
    <div class="dashboard mini">
        <div class="mb-3">
            <!-- filters !-->
        </div>
        <div class="inner__field">
            <div class="row">
                @foreach($projects as $project)
                    <div class="col-sm-6 col-md-4 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <div class="left">
                                    <h3 class="card__title">{{ $project->name }}</h3>
                                    <small class="date">{{ $project->created_at->format('M d, Y') }}</small>
                                </div>
                                <div class="right">
                                    {{--<span class="btn btn__stage">Active</span>
                                    <div class="dropdown">
                                        <a href="javascript:void(0)" class="dropdown-toggle " id=""
                                           data-bs-toggle="dropdown" aria-expanded="false">
                                            <x-svg.ellipsis-vertical/>
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="">
                                            <li>
                                                --}}{{--<a class="dropdown-item"
                                                   href="{{ route('tours.surfaces', [$project->tour, 'project_id' => $project->id]) }}">
                                                    Surfaces
                                                </a>--}}{{--
                                            </li>
                                        </ul>
                                    </div>--}}
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="card__text">
                                    {{ $project->description }}
                                </p>
                                <p>
                                    @if($project->tours->count() > 1)
                                        <span class="d-block">Select Tour</span>
                                    @endif
                                    @forelse($project->tours as $tour)
                                        <a href="{{ route('tours.show', [$tour, 'project_id' => $project->id]) }}">
                                            <span class="badge bg-info px-2 py-1">
                                                {{ $tour->name }}
                                            </span>
                                        </a>
                                    @empty
                                    <span class="text-center d-block">No tours configured</span>
                                    @endforelse
                                </p>
                                <p>
                                    <span>Collections</span><br>
                                    @forelse($project->artworkCollections as $collection)
                                        <a href="{{ route('artworks.index', ['collection_id' => $collection->id]) }}" target="_blank">
                                            <span class="badge bg-success px-2 py-1">
                                                {{ $collection->name }}
                                            </span>
                                        </a>
                                    @empty
                                        <span class="text-center d-block">No collections selected</span>
                                    @endforelse
                                </p>
                            </div>
                            <div class="card-footer" style="min-height: 47px;">
                                @include('include.partials.contributors')
                                {{--<a href="{{ route('tours.show', [$project->tour, 'project_id' => $project->id]) }}" class="btn next__btn">
                                    <x-svg.angle-right/>
                                </a>--}}
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
