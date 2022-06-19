<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8" />
    <title>{{ config('app.name') }}</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta name="csrf-token" content="{{ csrf_token() }}">
	<!-- App favicons -->
	@include('backend.includes.partial.favicon')

    <!-- App css -->
    <link href="{{ asset('backend/assets/css/icons.min.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/assets/css/app.min.css') }}" rel="stylesheet" type="text/css" id="light-style" />
    <link href="{{ asset('backend/assets/css/app-dark.min.css') }}" rel="stylesheet" type="text/css" id="dark-style" />

    <link href="{{ asset('vendor/toastr/toastr.min.css') }}" rel="stylesheet"/>
    <link href="{{ asset('backend/css/icons.css') }}" rel="stylesheet" type="text/css" />
    <link href="{{ asset('backend/css/custom.css') }}" rel="stylesheet" type="text/css" />
    <link rel="stylesheet" href="{{ asset('backend/css/media-library.css') }}">

	<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.4.1/jquery.min.js"></script>
    <script defer src="https://unpkg.com/alpinejs@3.7.1/dist/cdn.min.js"></script>


    @yield('styles')
    <livewire:styles/>

</head>

<body class="loading" data-layout-config='{"leftSideBarTheme":"dark","layoutBoxed":false, "leftSidebarCondensed":{{ Cache::get("leftSidebarCondensed" . Auth::id(), false)  ? "true" : "false" }}, "leftSidebarScrollable":false,"darkMode":false, "showRightSidebarOnStart": true}'>
<div class="wrapper">
    @include('backend.includes.partial.sidebar')

    <div class="content-page">
        <div class="content">
            @include('backend.includes.partial.navbar')
            <div class="container-fluid">
                <div class="row {{ empty($__env->yieldContent('title')) ? 'mt-4' : '' }}">
                    <div class="col-12">
                        <div class="page-title-box">
                            {{--<div class="page-title-right">
                                <ol class="breadcrumb m-0">
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Hyper</a></li>
                                    <li class="breadcrumb-item"><a href="javascript: void(0);">Pages</a></li>
                                    <li class="breadcrumb-item active">Profile</li>
                                </ol>
                            </div>--}}
                            <h4 class="page-title">@yield('title')</h4>
                        </div>
                    </div>

                    <div class="col-12">
                        @yield('content')
                    </div>
                </div>
            </div>
        </div>
        @include('backend.includes.partial.footer')

    </div>

    <livewire:modals.base-modal />
</div>

{{--@include('backend.includes.partial.settings')--}}


<!-- bundle -->
<script src="{{ asset('js/app.js') }}"></script>
<script src="{{ asset('backend/assets/js/vendor.min.js') }}"></script>
<script src="{{ asset('backend/assets/js/app.min.js') }}"></script>

<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>

<script>
    @php($notifications = array('error', 'success', 'warning', 'info'))
    @foreach($notifications as $notification)
        @if(session()->has($notification))
            @php($message = session()->get($notification))
			$.NotificationApp.send("{{ __(ucfirst($notification)) }}","{{ $message }}","top-right","rgba(0,0,0,0.2)","{{ $notification }}");
        @endif
    @endforeach
</script>

@yield('scripts')
<livewire:scripts/>
<script src="{{ asset('js/modals.js') }}"></script>

<script>
    Livewire.on('flashNotification', (message, type = 'success') => {
        toastr[type](message)
    });

    Echo.channel('shell')
        .listen('.newOutput', (e) => {
            let $output = $('#command-output');
            $output.append(e)
            $output.scrollTop($output.prop("scrollHeight"));
        });
</script>

@yield('livewire-scripts')

</body>
</html>
