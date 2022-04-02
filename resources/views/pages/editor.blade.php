@extends('layouts.master')

@section('content')
    @include('include.partials.collection')
    <button class="btn sidebar__trigger fixed">
        <x-svg.angle-left/>
    </button>
    <div class="surface__sidebar card">
        <div class="card-body">
            <div class="top">
                <div class="left">
                    <h2 class="surface__title">Surface 031 -</h2>
                    <p class="date__time">Last Edited: S.Lythe | 8/1/2021</p>
                </div>
                <div class="right">
                    <button type="button" class="btn">
                        <x-svg.plus width="24" height="24"/>
                        Add
                    </button>
                </div>
            </div>
            <button class="btn sidebar__trigger">
                <x-svg.angles-right color="white"/>
            </button>
            <div class="tetra__accordion">
                @for($i=0; $i<4; $i++)
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
                                    <a href="#" class="icon accordion__trigger">
                                        <x-svg.pen-to-square/>
                                    </a>
                                    <a href="#" class="icon">
                                        <x-svg.trash-can/>
                                    </a>
                                    <a href="#" class="icon">
                                        <x-svg.thumbs-up/>
                                    </a>
                                    <button type="button" class="icon arrow">
                                        <x-svg.angle-up/>
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div class="accordion__body">
                            @for($j=0; $j<3; $j++)
                                <div class="message">
                                    <h6 class="user__name">Matis</h6>
                                    added some of my daughters left over salmon and avocado roll
                                    with a dap of elk sauce on each one.
                                </div>
                            @endfor
                            <div class="input-group type__message">
                                <input
                                    type="text"
                                    class="form-control"
                                    placeholder="Type"
                                />
                                <button type="submit" class="input-group-text">
                                    <x-svg.plus />
                                </button>
                            </div>
                        </div>
                    </div>
                @endfor
            </div>
        </div>
    </div>
    <div class="dashboard mini">
        <div class="image__viewer">
            <img
                src="{{ asset('images/editor.png') }}"
                alt="image"
                class="featured__img"
                width="100%"
                height="auto"
            />
        </div>
    </div>
@endsection
