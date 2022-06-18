@extends('layouts.backend')

@section('content')
    <div class="card">
        <div class="card-header">
            <h5 class="card-title">{{ "Spot {$spot->id} Configuration" }}</h5>
        </div>
        <div class="card-body">
            <pre style="overflow: unset"><code class="language-xml">{{ $xml }}</code></pre>
        </div>
    </div>
@endsection

@section('styles')
    {{--<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.5.1/styles/atom-one.min.css">--}}
    <link rel="stylesheet" href="{{ asset('vendor/highlight/styles/atom-one-light.min.css') }}">
    <link rel="stylesheet" href="{{ asset('vendor/highlight/styles/custom.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('vendor/highlight/highlight.min.js') }}"></script>
    <script>hljs.highlightAll();</script>
@endsection
