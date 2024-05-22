@extends('layouts.backend')

@section('title')
    <span>{{ "Collector Sync Log" }}</span>
@endsection

@section('content')
   <livewire:collector-tools-report/>
@endsection

@section('styles')
    <link rel="stylesheet" href="{{ asset('vendor/highlight/styles/custom.css') }}">
@endsection

@section('scripts')
    <script src="{{ asset('vendor/highlight/highlight.min.js') }}"></script>
    <script>hljs.highlightAll();</script>
@endsection
