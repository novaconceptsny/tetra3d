@extends('layouts.master')

@section('content')
    <div class="dashboard gallery mini">
        @for($i = 1; $i < 4; $i++)
            <h4 class="font-secondary">
                Version {{ $i }}
            </h4>
            <div class="inner__field">
                <div class="card overflow-hidden">
                    <div class="card-body">
                        <img
                            src="{{ asset('images/card-img.png') }}"
                            alt="image"
                            class="bg__image"
                        />
                        <a href="{{ route('editor') }}" class="add__btn">
                            <span class="plus__icon">
                                <x-svg.plus width="35" height="35"/>
                            </span>
                            Add New Option
                        </a>
                    </div>
                </div>
                @for($j = 1; $j < 6; $j++)
                    <div class="card">
                        <div class="card-img-top">
                            <img
                                src="{{ asset('images/card-img.png') }}"
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
                @endfor
            </div>
            <hr >
        @endfor
    </div>
@endsection
