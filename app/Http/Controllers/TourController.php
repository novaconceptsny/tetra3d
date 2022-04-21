<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class TourController extends Controller
{
    public function index(Project $project)
    {
        $request = \request();
        $data = array();

        $data['hlookat'] = $request->get('hlookat', 0);
        $data['vlookat'] = $request->get('vlookat', 0);

        $data['hash'] = 0;
        $data['tracker'] = 0;
        $data['shareType'] = 0;

        $data['spotId'] = 48001;

        $data['spotName'] = 'p4point1';
        $data['backgroundImgArr'] = [
            0 => [
                'id' => 4001,
                'url' => 'storage//clients/4001/1648857482_test_hotspot.png',
            ],
            1 => [
                'id' => 4002,
                'url' => 'storage//clients/4002/1647456874_test_hotspot.png',
            ],
            2 => [
                'id' => 4003,
                'url' => 'storage//clients/4003/1647296044_test_hotspot.png',
            ],
            3 => [
                'id' => 4006,
                'url' => '/assets/version_icons/missingCanvas.png',
            ],
            4 => [
                'id' => 4007,
                'url' => 'storage//clients/4007/1647295905_test_hotspot.png',
            ],
            5 => [
                'id' => 4008,
                'url' => 'storage//clients/4008/1647295980_test_hotspot.png',
            ],
        ];

        $krpanoPath = 'krpano/tours/p4/p48001';

        $data['xmlPath'] = $krpanoPath.'/pano.xml';

        $data['locationId'] = 4;
        $data['clientDB'] = null;


        $data['mapDBs'] = null;


        $data['mapIdNames'] = [
            401 => "Config A"
        ];


        $data['mapSpots'] = null;


        return view('pages.tour', $data);
    }
}
