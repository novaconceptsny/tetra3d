@extends('layouts.master')

@section('content')
    <div class="dashboard mini">
        <div class="image__viewer">
            <img
                src="{{ asset('images/dashboard__bg.png') }}"
                alt="image"
                class="featured__img"
                width="100%"
                height="auto"
            />
        </div>
    </div>
@endsection
