<!DOCTYPE html>
<html lang="en">
<head>
    <title>Dashboard</title>
    <!--Required meta tag-->
    <meta charset="UTF-8"/>
    <meta name="description" content=""/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta property="og:title" content=""/>
    <meta property="og:type" content=""/>
    <meta property="og:url" content=""/>
    <meta property="og:image" content=""/>
    <meta name="_token" content="{{ csrf_token() }}">
    <!--Fontawesome cdn	-->

    <!-- CSS File -->
    <link rel="stylesheet" href="{{ asset('css/normalize.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
    <link rel="preconnect" href="https://fonts.googleapis.com"/>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin/>
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap"
        rel="stylesheet"
    />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css"
        integrity="sha512-9usAa10IRO0HhonpyAIVpjrylPvoDwiPUiKdWk5t3PyolY1cOd4DSE0Ga+ri4AuTroPR5aQvXU9xC6qOPnzFeg=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="{{ asset('css/style.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/responsive.css') }}"/>
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}"/>
    <link rel="stylesheet" href="{{ asset('backend/css/icons.css') }}"/>
    <link href="{{ asset('vendor/toastr/toastr.min.css') }}" rel="stylesheet"/>

    @yield('styles')
    <livewire:styles/>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
</head>

<body>
<!--Starts Header Area-->
<!--Starts Header Area-->
@if(request()->routeIs('dashboard'))
    @include('include.common.header')
@else
@include('include.common.page-header')
@endif
<!--Ends Header Area-->

<!--Starts Main Area-->
<main id="site__body">
    <section class="universal__wrapper">
        @include('include.common.sidebar')
        @yield('content')
    </section>
    <livewire:modals.base-modal />
</main>
<!--Ends Main Area-->

<!-- JS File -->
<script src="{{ asset('js/modernizr-3.11.2.min.js') }}"></script>
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script src="{{ asset('js/popper.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>
<script src="{{ asset('js/custom.js') }}"></script>

<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>

<script>
    @php($notifications = array('error', 'success', 'warning', 'info'))
    @foreach($notifications as $notification)
        @if(session()->has($notification))
            @php($message = session()->get($notification))
            {{ "toastr." . $notification }}{!! "('" !!}{{ __($message) }}{!! "')" !!}
        @endif
    @endforeach
</script>

<script>
    $(function (){
        let url = window.location.href;

        $('.sidebar .navbar__nav li.nav__item').each(function (){
            let link = $(this).find("a:first-child");

            if(link.attr('href').replace(/\/+$/, '') === url.replace(/\/+$/, '')){
                $(this).addClass('active');
            }
        })

        $('body').on('click', '.clipboard-copy', function ($event){
            let $clipboardContainer = $(this).closest('.clipboard-container');
            let $clipboardText = $clipboardContainer.find('.clipboard-text');
            let textToCopy = $clipboardText.val();

            /*let  $temp = $(`<input value='${textToCopy}'>`);
            $("body").append($temp);
            $temp.val().select();
            document.execCommand("copy");
            alert($temp.val())
            $temp.remove();*/
            $clipboardText.select();
            document.execCommand("copy");
        });
    })
</script>

<livewire:scripts/>
<script src="{{ asset('js/modals.js') }}"></script>
<script>
    Livewire.on('flashNotification', (message, type = 'success') => {
        toastr[type](message)
    });
</script>


@yield('scripts')
</body>
</html>
