@extends('layouts.backend')

@section('title')
    <span>{{ "Spot {$spot->friendly_name} Configuration" }}</span>
    <span class="ms-2"><a href="{{ route('backend.spot-configuration.edit', $spot) }}"><i class="fal fa-pencil"></i></a></span>
@endsection

@section('content')
    <div class="card"></div>
    <pre style="overflow: unset"><code class="language-xml">{{ $xml }}</code></pre>
@endsection

@section('styles')
    {{--<link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/highlight.js/11.5.1/styles/atom-one.min.css">--}}
    {{--<link rel="stylesheet" href="{{ asset('vendor/highlight/styles/atom-one-light.min.css') }}">--}}
    <link rel="stylesheet" href="{{ asset('vendor/highlight/styles/custom.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('vendor/highlight/highlight.min.js') }}"></script>
    <script>hljs.highlightAll();</script>
@endsection
