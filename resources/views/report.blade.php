@extends('layouts.backend')

@section('title')
    <span>{{ "Collector Sync Log" }}</span>
@endsection

@section('content')
    @php
        $content = file_get_contents(storage_path('logs/collector.log'));
    @endphp
    <div class="card"></div>
    <pre style="overflow: unset"><code class="language-accesslog">{{ $content }}</code></pre>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('vendor/highlight/styles/custom.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('vendor/highlight/highlight.min.js') }}"></script>
    <script>hljs.highlightAll();</script>
@endsection
