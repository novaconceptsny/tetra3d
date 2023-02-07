<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <title>{{ config('app.name') }}</title>
    <!-- bootstrap css link  -->
    <link
        href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css"
        rel="stylesheet"
        integrity="sha384-1BmE4kWBq78iYhFldvKuhfTAU6auU8tT94WrHftjDbrCEXSU1oBoqyl2QvZ6jIW3"
        crossorigin="anonymous"
    />
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.2.1/css/all.min.css"
        integrity="sha512-MV7K8+y+gLIBoVD59lQIYicR65iaqukzvf/nwasF0nqhPay5w/9lJmVM2hMDcnK1OnMGCdVK+iQrJ7lzPJQd1w=="
        crossorigin="anonymous"
        referrerpolicy="no-referrer"
    />
    <!-- own css file  -->
    <link rel="stylesheet" href="{{ asset('css/custom.css') }}"/>

    <link rel="stylesheet" href="{{ asset('redesign/css/style.css') }}"/>
    <link rel="stylesheet" href="{{ asset('backend/css/icons.css') }}"/>
    <!-- font awesome cdn links -->

    <link href="{{ asset('vendor/toastr/toastr.min.css') }}" rel="stylesheet"/>

    @yield('styles')
    <livewire:styles/>

    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

</head>
<body>

@include('include.common.header')

<main id="site__body">
    @yield('content')
    <livewire:modals.base-modal />
</main>

<!-- bootstrap script links -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script
    src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"
    integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB"
    crossorigin="anonymous"
></script>
<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.min.js"
    integrity="sha384-QJHtvGhmr9XOIpI6YVutG+2QOK9T+ZnN4kzFN1RtK3zEFEIsxhlmWl5/YESvpZ13"
    crossorigin="anonymous"
></script>


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

        $('#navbarNav .nav-item').each(function (){
            let $link = $(this).find("a:first-child");

            if($link.attr('href').replace(/\/+$/, '') === url.replace(/\/+$/, '')){
                $link.addClass('active');
            }
        })

        $('body').on('click', '.clipboard-copy', function ($event){
            let $clipboardContainer = $(this).closest('.clipboard-container');
            let $clipboardText = $clipboardContainer.find('.clipboard-text');
            let textToCopy = $clipboardText.val();
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
