<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ValidationRules;
use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Spot;
use App\Models\Tour;
use Illuminate\Http\Request;

class SpotController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Spot::class, 'spot');
    }

    public function index(Tour $tour)
    {
        $data = array();

        $data['spots'] = $tour->spots()->withCount('surfaces')->get();
        $data['tour'] = $tour;

        return view('backend.spot.index', $data);
    }

    public function create(Tour $tour)
    {
        $data = array();
        $data['route'] = route('backend.tours.spots.store', $tour);
        $data['tour'] = $tour;

        return view('backend.spot.form', $data);
    }

    public function store(Request $request, Tour $tour)
    {
        $request->validate(ValidationRules::storeSpot());

        $request->merge([
            'company_id' => $tour->company_id
        ]);

        $spot = $tour->spots()->create($request->only([
            'name', 'display_name','company_id'
        ]));

        $spot->surfaces()->sync($request->surfaces);

        $spot
            ->addFromMediaLibraryRequest($request->image_360)
            ->toMediaCollection('image_360');

        return redirect()->back()->with('success', 'Spot created successfully');
    }

    public function show(Spot $spot)
    {
        //
    }

    public function edit(Spot $spot)
    {
        $data = array();
        $data['spot'] = $spot;
        $data['method'] = 'put';
        $data['route'] = route('backend.spots.update', $spot);
        $data['tour'] = $spot->tour;

        return view('backend.spot.form', $data);
    }

    public function update(Request $request, Spot $spot, Tour $tour)
    {
        $request->validate(ValidationRules::updateSpot());

        // Get the previous value of display_name
        $previousDisplayName = $spot->getOriginal('display_name');

        $spot->update($request->only([
            'name',
            'display_name'
        ]));

        $spot->surfaces()->sync($request->surfaces);

        $spot
            ->addFromMediaLibraryRequest($request->image_360)
            ->toMediaCollection('image_360');


        print_r($tour->id);
        $this->updateTourXMLFiles('app/public/tours/'.$tour->id, $spot->name,  $spot->display_name, $previousDisplayName);

        return redirect()->route('backend.tours.spots.index', $spot->tour)->with('success', 'Spot updated successfully');
    }

    public function destroy(Spot $spot)
    {
        $spot->delete();
        return redirect()->back()->with('success', 'Spot deleted successfully');
    }



    private function updateTourXMLFiles($dir, $name, $display_name, $previousDisplayName) {
        $dh = opendir(storage_path($dir));

        while (($file = readdir($dh)) !==false) {
            if ($file != '.'&& $file != '..') {
                $fullpath = $dir.'/'.$file;

                if (is_dir(storage_path($fullpath))) {
                    $this->updateTourXMLFiles($fullpath, $name,$display_name, $previousDisplayName);
                } else {
                    if ($file == 'tour.xml') {
                        $this->updateTourXML($fullpath, $name,$display_name, $previousDisplayName);
                    }
                }
            }
        }

        closedir($dh);
    }
    private function updateTourXML($file_path,$name, $display_name, $previousDisplayName) {

        $file_contents = file_get_contents(storage_path($file_path));
        $check_file = strrpos($file_contents, '<!-- add the custom ThreeJS plugin -->');

        if ($check_file) {

        } else {
            if($previousDisplayName != null){
                $file_contents = str_replace((string)$previousDisplayName, (string)$display_name, $file_contents);
                $file_contents = str_replace((string)$name, (string)$display_name, $file_contents);
                file_put_contents(storage_path($file_path), $file_contents);
            }else{
                $file_contents = str_replace((string)$name, (string)$display_name, $file_contents);
                file_put_contents(storage_path($file_path), $file_contents);
            }
        }
    }
}
