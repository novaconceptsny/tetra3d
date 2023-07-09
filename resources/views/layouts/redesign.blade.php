<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"/>
    <meta http-equiv="X-UA-Compatible" content="IE=edge"/>
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="_token" content="{{ csrf_token() }}">
    <title>{{ config('app.name') }}</title>
    <!-- bootstrap css link  -->
    <link rel="stylesheet" href="{{ asset('css/bootstrap.min.css') }}"/>
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

<main id="site__body" style="margin-top: 46px">
    @yield('content')
    <livewire:modals.base-modal />

    <div class="modal fade" id="tourMapModal" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered modal-dialog-scrollable modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tour Map</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    @if(isset($tour))
                        <livewire:tour-map
                            :tour="$tour"
                            :project="$project ?? null"
                            :shared_tour_id="$shared_tour_id ?? null"
                        />
                    @endif
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>
</main>

<!-- bootstrap script links -->
<script src="{{ asset('js/jquery.min.js') }}"></script>
<script
    src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.10.2/dist/umd/popper.min.js"
    integrity="sha384-7+zCNj/IqJ95wo16oMtfsKbZ9ccEh31eOz1HGyDuCQ6wgnyJNSYdrPa03rtR1zdB"
    crossorigin="anonymous"
></script>
<script src="{{ asset('js/bootstrap.min.js') }}"></script>


<script src="{{ asset('vendor/toastr/toastr.min.js') }}"></script>

<script>
    toastr.options = {
        "positionClass": "toast-bottom-right",
    }
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

<!-- Map script -->
<script>
    function setMapScale() {

        let $floorPlan = $(".floorPlan");
        let $pin = $('.pin');
        $pin.hide();

        let zoneW = $floorPlan.innerWidth();
        let zoneH = $floorPlan.innerHeight();
        let defaultW = $floorPlan.attr('defaultWidth');
        let defaultH = $floorPlan.attr('defaultHeight');

        let scaleW = zoneW / defaultW;
        let scaleH = zoneH / defaultH;

        let scale;
        let screenRatio = zoneW / zoneH;
        let mapRatio = defaultW / defaultH;

        if (screenRatio > mapRatio) {
            scale = scaleH;
        } else {
            scale = scaleW;
        }

        $pin.each(function () {
            let top = $(this).attr('top');
            let left = $(this).attr('left');
            $(this).css('top', (top * scale - 40 + "px"));
            $(this).css('left', (left * scale - 20 + "px"));
        });

        $pin.show();
    }

    $(document).ready(function () {
        setMapScale();
    });

    $(window).resize(function () {
        setMapScale();
    });

    let tourModal = document.getElementById('tourMapModal')
    tourModal.addEventListener('shown.bs.modal', function (event) {
        setMapScale();
    });

    Livewire.on('mapChanged', () => {
        setMapScale();
    });
</script>
</body>
</html>
