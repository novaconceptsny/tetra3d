@extends('layouts.redesign')

@section('content')
    <section class="main-page">
        <div class="container-fluid main-intro">
            @include('include.common.greetings')

            <livewire:projects-list/>
        </div>
    </section>
@endsection
