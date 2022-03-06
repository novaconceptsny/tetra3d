@extends('layouts.master')

@section('content')
    <div class="dashboard gallery mini">
        <div class="inner__field p-4">
            @for($i = 1; $i < 15; $i++)
                <a href="{{ route('editor') }}" class="card">
                    <div class="card-img-top">
                        <img
                            src="{{ asset('images/card-img.png') }}"
                            alt="image"
                            width="100%"
                            height="auto"
                        />
                        <button class="add__btn">
                            <span class="plus__icon">
                                <x-svg.plus width="35" height="35"/>
                            </span>
                            Add New Option
                        </button>
                    </div>
                    <div class="card-body">
                        <div class="top">
                            <h3 class="gallery__title">Matise Centered</h3>
                            <div class="like">
                                <x-svg.thumbs-up/>
                                3x
                            </div>
                        </div>
                        <p class="created__by">Created by: Nile Berry</p>
                        <p class="date__time">Last Modified: 04:32:18 12/11/2021</p>
                    </div>
                </a>
            @endfor
        </div>
    </div>
@endsection
