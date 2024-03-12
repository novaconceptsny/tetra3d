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
