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
            @for($i = 1; $i < 15; $i++)
                @php($is_archive = in_array($i, [3,7, 8]))
                <div class="card">
                    <div class="card-header">
                        <div class="left">
                            <h3 class="card__title">Pablo Picasso Show</h3>
                            <small class="date">May 4th, 2021</small>
                        </div>
                        <div class="right">
                            <span class="btn btn__stage {{ $is_archive ? 'archive' : '' }}">Active</span>
                            <a href="#" class="dot__icon dot_1">
                                <x-svg.ellipsis-vertical/>
                            </a>
                            <div class="drop__down drop_1">
                                <ul>
                                    <li><a href="#">item-1</a></li>
                                    <li><a href="#">item-2</a></li>
                                    <li><a href="#">item-3</a></li>
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
            @endfor
        </div>
    </div>
@endsection
