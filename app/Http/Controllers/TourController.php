<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\Spot;
use App\Models\Tour;
use App\Services\SpotXmlGenerator;
use Illuminate\Http\Request;
use SimpleXMLElement;

class TourController extends Controller
{
    public function index(Project $project)
    {

        /*
         * spots -> tour/360 | one spot is one tour/360
         * canvas_background -> surface image/background | surface
         * canvas_spots -> one 360 have many spots/surfaces
         * spot_version -> latest snapshot of surface |versions of the surface ( different artworks )
         *
         *
         * */

        $request = \request();
        $data = array();

        $spot = Spot::first();

        $tour = Tour::first();
        /*$tourGenerator = new TourGenerator($spot);
        $tourGenerator->createXml();*/

        $data['hlookat'] = $request->get('hlookat', 0);
        $data['vlookat'] = $request->get('vlookat', 0);

        $data['hash'] = 0;
        $data['tracker'] = 0;
        $data['shareType'] = 0;

        $data['spotId'] = 48001;

        $data['spotName'] = 'p4point1';
        $data['surfaceArtworks'] = [
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

        $data['surfaces'] = $spot->surfaces;
        /*foreach ($spot->surfaces as $surface){
            dd($surface->state->url);
        }*/

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

    public function show(Tour $tour)
    {
        $request = \request();

        $data = array();
        $data['tour'] = $tour;
        $data['spot'] = $tour->spots->first();

        $data['hash'] = 0;
        $data['tracker'] = 0;
        $data['shareType'] = 0;
        $data['hlookat'] = $request->get('hlookat', 0);
        $data['vlookat'] = $request->get('vlookat', 0);


        return view('pages.tour', $data);
    }
}
