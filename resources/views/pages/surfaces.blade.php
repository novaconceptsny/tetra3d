@extends('layouts.master')

@section('page_actions')
    <x-page-action text="Return to 360 view" :url="route('tours.show', [$tour, 'project_id' => $project->id])"/>
@endsection

@section('content')
    <div class="dashboard gallery mini">
        @foreach($surfaces as $surface)
            <h4 class="font-secondary section__title">
                {{ $surface->name }}
            </h4>
            <div class="inner__field row mb-4">
                <div class="col-md-6 col-lg-3 mb-3 ">
                    <div href="#" class="card overflow-hidden">
                        <a href="#" class="overlay__link"></a>
                        <div class="card-body">
                            <img
                                src="{{ $surface->getFirstMediaUrl('background') }}"
                                alt="image"
                                class="bg__image"
                            />
                            <a href="{{ route('surfaces.show', [$surface, 'project_id' => $project->id, 'new' => 1]) }}" class="add__btn">
                                <span class="plus__icon">
                                    <x-svg.plus width="35" height="35"/>
                                </span>
                                Add New Option
                            </a>
                        </div>
                    </div>
                </div>
                @foreach($surface->states as $state)
                    <div class="col-md-6 col-lg-3 mb-3 ">
                        <div href="#" class="card">
                            <a href="#" class="overlay__link"></a>
                            <div class="card-img-top">
                                <img
                                    src="{{ $state->getFirstMediaUrl('thumbnail') }}"
                                    alt="image"
                                    width="100%"
                                    height="auto"
                                />
                            </div>
                            <div class="card-body">
                                <div class="accordion__item">
                                    <div class="accordion__header">
                                        <div class="left">
                                            <div class="user__details">
                                                <h3 class="username">Matis Centered</h3>
                                                <div class="tag">Nile Berry | 12/11/2021</div>
                                                <div class="profiles__icons">
                                                    @include('include.partials.contributors')
                                                </div>
                                            </div>
                                        </div>
                                        <div class="right">
                                            <div class="surface__items">
                                                <a href="#" class="icon">
                                                    <x-svg.trash-can/>
                                                </a>
                                                <a href="#" class="icon">
                                                    <x-svg.thumbs-up/>
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
            <hr >
        @endforeach
    </div>
@endsection
