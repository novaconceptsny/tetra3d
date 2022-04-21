@extends('layouts.master')

@section('page_actions')
    <x-page-action id="save_btn" title="{{ __('Save') }}"/>
    <x-page-action id="save_as_btn" title="{{ __('Save As') }}"/>
    <x-page-action id="remove_btn" title="{{ __('Remove') }}"/>
    <x-page-action id="remove_btn" title="{{ __('Return to 360 view') }}"/>
@endsection

@section('content')
    @include('include.partials.collection')
    <button class="btn sidebar__trigger fixed">
        <x-svg.angle-right/>
    </button>
    <x-surface-versions/>
    <div class="dashboard mini">
        <div class="d-flex fs-5 mb-2">
            <p class="room_name mb-0">s4002 > Test</p>
            <p class="ml-2" id="assignment_title">Test</p>
        </div>
        <div class="image__viewer main_content">
            {{--<img
                src="{{ asset('images/editor.png') }}"
                alt="image"
                class="featured__img"
                width="100%"
                height="auto"
            />--}}
            <canvas id="artwork_canvas" style="z-index: 100;"></canvas>
        </div>
    </div>

    <button type="button" id="crop_btn" class="btn btn-outline-info" data-toggle="tooltip" data-placement="top"
            style="z-index:3000;position:fixed;left:500px;top:100px;width:50px;height:50px;" title="Crop">
        <i class="fas fa-crop-alt" style="font-size:20px"></i>
    </button>
@endsection

@section('scripts')
    <script>
        var userId = 1;

        var spotId = 48001;
        var canvasId = 4002;
        var vlookat = 0;
        var hlookat = 0;
        var canvasDBArr = null;
        var latestState = null;
        var versionId = 240;
        var artworkArr = null;
        var scaleArr = null;
        var locationId = 4;
        var artgroupId = 5;
        var defaultScaleArr = [];
        var artworkTotalNum = null;
        scaleArr = {
            "5164929531": 17,
            "4232247633": 15,
            "5437133087": 13,
            "6250843832": 26,
            "4644797966": 13,
            "6618485058": 6,
            "480835599": 16,
            "5160331591": 9,
            "7795708843": 12,
            "2387641039": 34,
            "817822794": 50,
            "2730025858": 23,
            "1101320499": 31,
            "2296393967": 10,
            "1085307669": 50,
            "8408547380": 8,
            "7424762563": 16,
            "4600906143": 3,
            "6064155169": 50,
            "6949994241": 38,
            "6540028402": 16,
            "5470100101": 20,
            "337701975": 10,
            "7178218364": 24,
            "8495476305": 10,
            "9510480219": 10,
            "7919618544": 14,
            "9905154877": 15,
            "3299148121": 14,
            "1837026498": 10,
            "8213679539": 41,
            "9892903547": 25,
            "1262226944": 5,
            "9607508672": 10,
            "6608495911": 25,
            "9487606783": 8,
            "1429580512": 10,
            "5798927787": 25,
            "6220606": 8,
            "2699423999": 5,
            "9966780251": 26,
            "7341754850": 28,
            "1545413471": 125,
            "2857854653": 11,
            "1818662037": 170,
            "6294976644": 40,
            "9973648336": 18,
            "7732408201": 50,
            "6194838207": 83,
            "3641190884": 16,
            "8569465718": 50,
            "4163492030": 50,
            "293242329": 70,
            "3520463842": 70,
            "4989695931": 13,
            "3779837866": 181,
            "2496489945": 23,
            "7159528256": 225,
            "5670045950": 125,
            "9211899600": 10,
            "1174686494": 27,
            "965768725": 11,
            "2206387687": 12,
            "7331446590": 50,
            "7775382336": 11,
            "4442968345": 21,
            "5022844585": 6,
            "9836597915": 38,
            "9570170040": 9,
            "6321229771": 16,
            "614047123": 12,
            "8032362163": 7,
            "329975755": 22,
            "5069564798": 50,
            "2998350968": 33,
            "4577741449": 33,
            "3916891608": 14,
            "4520251352": 12,
            "5635613943": 21,
            "4839266256": 52,
            "7202760503": 36,
            "938481267": 50,
            "9828072104": 10,
            "8239514595": 7,
            "9096848262": 7,
            "3651763328": 14,
            "7696819766": 11,
            "2755148551": 6,
            "109107613": 7,
            "2436341523": 52,
            "7834218856": 12,
            "6906352170": 15,
            "10402403": 7,
            "5698634182": 6,
            "9377162241": 9,
            "3182687371": 5,
            "4305663389": 8,
            "6351045729": 21,
            "196691626": 15,
            "7122753661": 11,
            "4520156218": 20,
            "3747885450": 13,
            "7179236817": 3,
            "2002987348": 5,
            "9266760993": 11,
            "1074968903": 7,
            "2000886340": 6,
            "691484916": 12,
            "5561699185": 11,
            "3604396163": 9,
            "3897153920": 15,
            "5679039634": 5,
            "7950913550": 4,
            "1183439222": 12,
            "8424246098": 27,
            "7953189050": 22,
            "621093458": 12,
            "8945823401": 50,
            "508829839": 18,
            "7758336348": 13,
            "5668011834": 75,
            "7985650942": 12,
            "8116051887": 13,
            "7469719768": 5,
            "9072616561": 23,
            "3594982060": 7,
            "9546925224": 16,
            "1056027026": 15,
            "8875755439": 8,
            "3014946522": 5,
            "7199701467": 7,
            "5264218327": 9,
            "9360629677": 5,
            "686320518": 26,
            "7788400759": 12,
            "5187933875": 41,
            "1174730429": 40,
            "4466640507": 14,
            "7050871370": 21,
            "2998943326": 39,
            "5835042433": 47,
            "568076834": 21,
            "1053695381": 10,
            "4365338019": 50,
            "5930815659": 19,
            "8965016410": 50,
            "9681378019": 20,
            "9824674672": 50,
            "9099095525": 28,
            "3363413888": 10,
            "5572107926": 12,
            "5883610041": 40,
            "6776881365": 4,
            "6683163163": 5,
            "7552016429": 24,
            "266085050": 52,
            "9214093475": 30,
            "9336011794": 13,
            "2326104210": 17,
            "8149654998": 14,
            "2472802905": 100,
            "624044321": 10,
            "6419136904": 10,
            "226359680": 12,
            "7262564280": 5,
            "1353823230": 16,
            "4415636155": 15,
            "5951422931": 28,
            "5899405975": 10,
            "3541261906": 16,
            "935912409": 6,
            "284469666": 4,
            "3435030780": 35,
            "937855972": 13,
            "9355925772": 27,
            "8229421773": 12,
            "5496376236": 11,
            "1464067881": 25,
            "7046607575": 10,
            "3937576995": 12,
            "8554048356": 90,
            "8310653970": 55,
            "2634998835": 35,
            "5400979512": 7,
            "6123610113": 10,
            "2687882000": 16,
            "9948215415": 11,
            "3642376303": 12,
            "4046780963": 20,
            "7826824823": 66,
            "4455181431": 20,
            "9352812249": 20,
            "9346413777": 20,
            "5213260071": 20,
            "3301076741": 20,
            "2700633062": 20,
            "9193334665": 13,
            "9368016220": 13,
            "164713872318": 8,
            "164713872897": 39,
            "164713873152": 58,
            "164713873828": 58,
            "164713874881": 58,
            "164713875675": 31,
            "164713876281": 27,
            "164713876842": 41,
            "164713877398": 27,
            "164713878028": 14,
            "164713878282": 13,
            "164713878349": 35,
            "164713879273": 23,
            "164713880062": 46,
            "164713880558": 46
        };

        artworkArr = [{
            "assignment_id": 222,
            "artwork_id": 164713873152,
            "version_id": 240,
            "top_position": 252,
            "left_position": 282,
            "crop_data": "null",
            "created_at": "2022-03-13 07:32:18",
            "updated_at": "2022-03-13 07:32:18",
            "override_scale": 58,
            "id": 203,
            "object_id": 164713873152,
            "title": "Damascene Athan #1",
            "artist": "Mohamad Hafez",
            "type": "Painting",
            "image_url": "https:\/\/tetra-gallery.s3.amazonaws.com\/artgroup_5\/164713873152.png?1650491474",
            "thumbnail_url": "https:\/\/tetra-gallery.s3.amazonaws.com\/artgroup_thumbnail_5\/164713873152.png",
            "scale": 58,
            "width_inch": 24,
            "height_inch": 34,
            "no_dimension": 0
        }, {
            "assignment_id": 223,
            "artwork_id": 164713873828,
            "version_id": 240,
            "top_position": 877,
            "left_position": 749,
            "crop_data": "null",
            "created_at": "2022-03-13 07:32:28",
            "updated_at": "2022-03-13 07:32:28",
            "override_scale": 58,
            "id": 204,
            "object_id": 164713873828,
            "title": "Damascene Athan #6",
            "artist": "Mohamad Hafez",
            "type": "Painting",
            "image_url": "https:\/\/tetra-gallery.s3.amazonaws.com\/artgroup_5\/164713873828.png?1650491474",
            "thumbnail_url": "https:\/\/tetra-gallery.s3.amazonaws.com\/artgroup_thumbnail_5\/164713873828.png",
            "scale": 58,
            "width_inch": 24,
            "height_inch": 34,
            "no_dimension": 0
        }, {
            "assignment_id": 224,
            "artwork_id": 164713877398,
            "version_id": 240,
            "top_position": 1320,
            "left_position": 1039,
            "crop_data": "null",
            "created_at": "2022-03-13 07:33:00",
            "updated_at": "2022-03-13 07:33:00",
            "override_scale": 27,
            "id": 209,
            "object_id": 164713877398,
            "title": "inmysleeplesssolitudetonight, portrait of the girls",
            "artist": "David Antonio Cruz",
            "type": "Painting",
            "image_url": "https:\/\/tetra-gallery.s3.amazonaws.com\/artgroup_5\/164713877398.png?1650491474",
            "thumbnail_url": "https:\/\/tetra-gallery.s3.amazonaws.com\/artgroup_thumbnail_5\/164713877398.png",
            "scale": 27,
            "width_inch": 74,
            "height_inch": 48,
            "no_dimension": 0
        }, {
            "assignment_id": 225,
            "artwork_id": 164713878028,
            "version_id": 240,
            "top_position": 276,
            "left_position": 1199,
            "crop_data": "null",
            "created_at": "2022-03-13 07:33:02",
            "updated_at": "2022-03-13 07:33:02",
            "override_scale": 14,
            "id": 210,
            "object_id": 164713878028,
            "title": "Sirveintes y Escaleras \/ Servants and Ladders",
            "artist": "Guillermo Galindo",
            "type": "Painting",
            "image_url": "https:\/\/tetra-gallery.s3.amazonaws.com\/artgroup_5\/164713878028.png?1650491474",
            "thumbnail_url": "https:\/\/tetra-gallery.s3.amazonaws.com\/artgroup_thumbnail_5\/164713878028.png",
            "scale": 14,
            "width_inch": 24,
            "height_inch": 134,
            "no_dimension": 0
        }, {
            "assignment_id": 226,
            "artwork_id": 164713878349,
            "version_id": 240,
            "top_position": 308,
            "left_position": 706,
            "crop_data": "null",
            "created_at": "2022-03-13 07:33:12",
            "updated_at": "2022-03-13 07:33:12",
            "override_scale": 35,
            "id": 212,
            "object_id": 164713878349,
            "title": "Meet To Sleep",
            "artist": "Jasmeen Patheja",
            "type": "Painting",
            "image_url": "https:\/\/tetra-gallery.s3.amazonaws.com\/artgroup_5\/164713878349.png?1650491474",
            "thumbnail_url": "https:\/\/tetra-gallery.s3.amazonaws.com\/artgroup_thumbnail_5\/164713878349.png",
            "scale": 35,
            "width_inch": 56,
            "height_inch": 56,
            "no_dimension": 0
        }, {
            "assignment_id": 227,
            "artwork_id": 164713879273,
            "version_id": 240,
            "top_position": 1168,
            "left_position": 266,
            "crop_data": "null",
            "created_at": "2022-03-13 07:33:20",
            "updated_at": "2022-03-13 07:33:20",
            "override_scale": 22.230291777355422,
            "id": 213,
            "object_id": 164713879273,
            "title": "Demeter's Morning",
            "artist": "Nona Faustine",
            "type": "Painting",
            "image_url": "https:\/\/tetra-gallery.s3.amazonaws.com\/artgroup_5\/164713879273.png?1650491474",
            "thumbnail_url": "https:\/\/tetra-gallery.s3.amazonaws.com\/artgroup_thumbnail_5\/164713879273.png",
            "scale": 23,
            "width_inch": 84,
            "height_inch": 56,
            "no_dimension": 0
        }, {
            "assignment_id": 228,
            "artwork_id": 164713880062,
            "version_id": 240,
            "top_position": 704,
            "left_position": 384,
            "crop_data": "null",
            "created_at": "2022-03-13 07:33:25",
            "updated_at": "2022-03-13 07:33:25",
            "override_scale": 46,
            "id": 214,
            "object_id": 164713880062,
            "title": "Umma's Tongue- molten at 6000\u00c2\u00b0 degrees",
            "artist": "Hannah Bronte",
            "type": "Painting",
            "image_url": "https:\/\/tetra-gallery.s3.amazonaws.com\/artgroup_5\/164713880062.png?1650491474",
            "thumbnail_url": "https:\/\/tetra-gallery.s3.amazonaws.com\/artgroup_thumbnail_5\/164713880062.png",
            "scale": 46,
            "width_inch": 43,
            "height_inch": 24,
            "no_dimension": 0
        }];
        locationId = 4;
        vlookat = 0;
        hlookat = -90;
        canvasDBArr = {
            "background_url": "\/canvas\/s4002.jpg",
            "spot_name": "na",
            "overlay_url": null,
            "bound_box_top": 187,
            "bound_box_left": 205,
            "bound_box_height": 1500,
            "bound_box_width": 1310,
            "img_width": 1644,
            "img_height": 1810,
            "hotspot_width_px": null,
            "actual_width_inch": 208
        }; //這裡其實應該是一個json。代表單一surface的data而已
        latestState = {
            "isOverlap": false,
            "background": "/canvas/s4002.jpg",
            "defaultScale": 0.07087794951865337,
            "savedVersion": true,
            "actualWidthInch": 208,
            "assignedArtwork": [{
                "title": "Damascene Athan #1",
                "imgUrl": "https://tetra-gallery.s3.amazonaws.com/artgroup_5/164713873152.png?1647456777",
                "cropData": null,
                "artworkId": 164713873152,
                "topPosition": 252,
                "leftPosition": 282,
                "overrideScale": 58
            }, {
                "title": "Damascene Athan #6",
                "imgUrl": "https://tetra-gallery.s3.amazonaws.com/artgroup_5/164713873828.png?1647456777",
                "cropData": null,
                "artworkId": 164713873828,
                "topPosition": 877,
                "leftPosition": 749,
                "overrideScale": 58
            }, {
                "title": "inmysleeplesssolitudetonight, portrait of the girls",
                "imgUrl": "https://tetra-gallery.s3.amazonaws.com/artgroup_5/164713877398.png?1647456777",
                "cropData": null,
                "artworkId": 164713877398,
                "topPosition": 1320,
                "leftPosition": 1039,
                "overrideScale": 27
            }, {
                "title": "Sirveintes y Escaleras / Servants and Ladders",
                "imgUrl": "https://tetra-gallery.s3.amazonaws.com/artgroup_5/164713878028.png?1647456777",
                "cropData": null,
                "artworkId": 164713878028,
                "topPosition": 276,
                "leftPosition": 1199,
                "overrideScale": 14
            }, {
                "title": "Meet To Sleep",
                "imgUrl": "https://tetra-gallery.s3.amazonaws.com/artgroup_5/164713878349.png?1647456777",
                "cropData": null,
                "artworkId": 164713878349,
                "topPosition": 308,
                "leftPosition": 706,
                "overrideScale": 35
            }, {
                "title": "Demeter's Morning",
                "imgUrl": "https://tetra-gallery.s3.amazonaws.com/artgroup_5/164713879273.png?1647456777",
                "cropData": null,
                "artworkId": 164713879273,
                "topPosition": 1168,
                "leftPosition": 266,
                "overrideScale": 22.230291777355426
            }, {
                "title": "Umma's Tongue- molten at 6000Â° degrees",
                "imgUrl": "https://tetra-gallery.s3.amazonaws.com/artgroup_5/164713880062.png",
                "cropData": null,
                "artworkId": "164713880062",
                "topPosition": 704,
                "leftPosition": 384,
                "overrideScale": null
            }],
            "modifiedVersion": {"addedArtwork": [], "removedArtwork": [], "modifiedArtwork": []},
            "recentSelection": null,
            "currentVersionData": "{\"client\":1,\"version_id\":240,\"version_name\":\"test\",\"background_id\":4002,\"assigned_artwork\":[{\"title\":\"Damascene Athan #1\",\"imgUrl\":\"https:\\/\\/tetra-gallery.s3.amazonaws.com\\/artgroup_5\\/164713873152.png?1647456777\",\"artworkId\":164713873152,\"leftPosition\":282,\"topPosition\":252,\"cropData\":null,\"overrideScale\":58},{\"title\":\"Damascene Athan #6\",\"imgUrl\":\"https:\\/\\/tetra-gallery.s3.amazonaws.com\\/artgroup_5\\/164713873828.png?1647456777\",\"artworkId\":164713873828,\"leftPosition\":749,\"topPosition\":877,\"cropData\":null,\"overrideScale\":58},{\"title\":\"inmysleeplesssolitudetonight, portrait of the girls\",\"imgUrl\":\"https:\\/\\/tetra-gallery.s3.amazonaws.com\\/artgroup_5\\/164713877398.png?1647456777\",\"artworkId\":164713877398,\"leftPosition\":1039,\"topPosition\":1320,\"cropData\":null,\"overrideScale\":27},{\"title\":\"Sirveintes y Escaleras \\/ Servants and Ladders\",\"imgUrl\":\"https:\\/\\/tetra-gallery.s3.amazonaws.com\\/artgroup_5\\/164713878028.png?1647456777\",\"artworkId\":164713878028,\"leftPosition\":1199,\"topPosition\":276,\"cropData\":null,\"overrideScale\":14},{\"title\":\"Meet To Sleep\",\"imgUrl\":\"https:\\/\\/tetra-gallery.s3.amazonaws.com\\/artgroup_5\\/164713878349.png?1647456777\",\"artworkId\":164713878349,\"leftPosition\":706,\"topPosition\":308,\"cropData\":null,\"overrideScale\":35},{\"title\":\"Demeter's Morning\",\"imgUrl\":\"https:\\/\\/tetra-gallery.s3.amazonaws.com\\/artgroup_5\\/164713879273.png?1647456777\",\"artworkId\":164713879273,\"leftPosition\":266,\"topPosition\":1168,\"cropData\":null,\"overrideScale\":22.230291777355422},{\"title\":\"Umma's Tongue- molten at 6000\\u00b0 degrees\",\"imgUrl\":\"https:\\/\\/tetra-gallery.s3.amazonaws.com\\/artgroup_5\\/164713880062.png\",\"artworkId\":\"164713880062\",\"leftPosition\":384,\"topPosition\":704,\"cropData\":null,\"overrideScale\":null}]}"
        };
        artworkTotalNum = 15;

    </script>
    <script type="module" src="{{ asset('canvas/crop_functions.js') }}"></script>
    <script type="module" src="{{ asset('canvas/artwork_assignment.js') }}"></script>
    {{--<script type="module" src="{{ asset('canvas/canvas.js') }}"></script>--}}
    <script type="text/javascript" src="{{ asset('js/fabric.min.js') }}"></script>
    <script type="module" src="{{ asset('canvas/twbs-pagination/jquery.twbsPagination.min.js') }}"></script>

@endsection
