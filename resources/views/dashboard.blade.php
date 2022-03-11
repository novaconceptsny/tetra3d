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
                @for($i = 1; $i < 15; $i++)
                    @php($is_archive = in_array($i, [3,7, 8]))
                    <div class="col-sm-6 col-md-4 mb-3">
                        <div class="card">
                            <div class="card-header">
                                <div class="left">
                                    <h3 class="card__title">Pablo Picasso Show</h3>
                                    <small class="date">May 4th, 2021</small>
                                </div>
                                <div class="right">
                                    <span class="btn btn__stage {{ $is_archive ? 'archive' : '' }}">Active</span>
                                    <div class="dropdown">
                                        <a href="javascript:void(0)" class="dropdown-toggle " id="" data-bs-toggle="dropdown" aria-expanded="false">
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
                                <a href="#" class="btn next__btn">
                                    <svg
                                        width="7"
                                        height="12"
                                        viewBox="0 0 7 12"
                                        fill="none"
                                        xmlns="http://www.w3.org/2000/svg"
                                    >
                                        <path
                                            d="M1.70044 10.6311L6.17489 6.09303L1.70044 1.55496"
                                            stroke="white"
                                            stroke-width="1.5"
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                        />
                                    </svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
@endsection
