<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ValidationRules;
use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\ArtworkCollection;
use Illuminate\Http\Request;

class ArtworkController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(Artwork::class, 'artwork');
    }

    public function index()
    {
        return view('backend.artwork.index');
    }

    public function create()
    {
        $data = array();

        $data['route'] = route('backend.artworks.store');
        $data['artwork_collections'] = ArtworkCollection::all();
        return view('backend.artwork.form', $data);
    }

    public function store(Request $request)
    {
        $request->validate(ValidationRules::storeArtwork());

        $artwork = Artwork::create($request->only([
            'name',
            'artist',
            'type',
            'description',
            'data',
            'artwork_collection_id',
            'original_unit',
        ]));

        $artwork->original_value = [
            'width' => $request->data['width_inch'],
            'height' => $request->data['height_inch'],
            'unit' => $request->original_unit
        ];

         // Convert to inches if unit is cm and save to data column
         if (($request->original_unit ?? 'cm') === 'cm' && !empty($request->data['width_inch']) && !empty($request->data['height_inch'])) {
            // Convert cm to inches (1 inch = 2.54 cm)
            $widthInch = round($request->data['width_inch'] / 2.54, 5);
            $heightInch = round($request->data['height_inch'] / 2.54, 5);
            
            $artwork->data = [
                'width_inch' => $widthInch,
                'height_inch' => $heightInch,
                'scale' => $request->data['scale'] ?? '',
            ];
        } 

        $artwork->save();

        $artwork->addFromMediaLibraryRequest($request->image)
            ->toMediaCollection('image');

        // refresh model, to ensure the media is attached!
        $artwork->refresh();

        $artwork->resizeImage();

        return redirect()->back()->with('success', 'Artwork created successfully');
    }

    public function show(Artwork $artwork)
    {
        //
    }

    public function edit(Artwork $artwork)
    {

        error_log($artwork);
        $data = array();

        $data['route'] = route('backend.artworks.update', $artwork);
        $data['method'] = 'put';
        $data['artwork'] = $artwork;
        $data['artwork_collections'] = ArtworkCollection::all();

        return view('backend.artwork.form', $data);
    }

    public function update(Request $request, Artwork $artwork)
    {
        $request->validate(ValidationRules::updateArtwork());

        $artwork->update($request->only([
            'name',
            'artist',
            'type',
            'description',
            'data',
            'artwork_collection_id',
            'original_unit',
        ]));

        $artwork->original_value = [
            'width' => $request->data['width_inch'],
            'height' => $request->data['height_inch'],
            'unit' => $artwork->original_unit
        ];

        // Convert to inches if unit is cm and save to data column
        if (($artwork->original_unit ?? 'cm') === 'cm' && !empty($request->data['width_inch']) && !empty($request->data['height_inch'])) {
            // Convert cm to inches (1 inch = 2.54 cm)
            $widthInch = round($request->data['width_inch'] / 2.54, 5);
            $heightInch = round($request->data['height_inch'] / 2.54, 5);   
            
            $artwork->data = [
                'width_inch' => $widthInch,
                'height_inch' => $heightInch,
                'scale' => $request->data['scale'] ?? '',
            ];
        }       
        
        $artwork->save();

        $artwork->addFromMediaLibraryRequest($request->image)
            ->toMediaCollection('image');

        // refresh model, to ensure the media is attached!
        $artwork->refresh();

        $artwork->resizeImage();

        return redirect()->back()->with('success', 'Artwork updated successfully');
    }

    public function destroy(Artwork $artwork)
    {
        $artwork->delete();

        return redirect()->back()->with('success', 'Artwork deleted successfully');
    }
}
