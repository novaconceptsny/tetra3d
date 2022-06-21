@extends('layouts.master')

@section('content')
    <div class="dashboard mini">
        <div class="mb-3">
            <select class="form-select">
                <option selected>All</option>
                <option value="1">One</option>
                <option value="2">Two</option>
                <option value="3">Three</option>
            </select>
        </div>
        <div class="inner__field">
            <div class="row">
                @foreach($projects as $project)
                    <div class="col-sm-6 col-md-4 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <div class="left">
                                    <h3 class="card__title">{{ $project->name }}</h3>
                                    <small class="date">May 4th, 2021</small>
                                </div>
                                <div class="right">
                                    <span class="btn btn__stage">Active</span>
                                    <div class="dropdown">
                                        <a href="javascript:void(0)" class="dropdown-toggle " id=""
                                           data-bs-toggle="dropdown" aria-expanded="false">
                                            <x-svg.ellipsis-vertical/>
                                        </a>
                                        <ul class="dropdown-menu" aria-labelledby="">
                                            <li><a class="dropdown-item" href="#">Action</a></li>
                                            <li><a class="dropdown-item" href="#">Another action</a></li>
                                            <li><a class="dropdown-item" href="#">Something else here</a></li>
                                        </ul>
                                    </div>
                                </div>
                            </div>
                            <div class="card-body">
                                <p class="card__text">
                                    Lorem ipsum dolor sit amet, consectetur adipiscing elit.
                                </p>
                            </div>
                            <div class="card-footer">
                                @include('include.partials.contributors')
                                <a href="{{ route('tours.show', $project->tour) }}" class="btn next__btn">
                                    <x-svg.angle-right/>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
@endsection
