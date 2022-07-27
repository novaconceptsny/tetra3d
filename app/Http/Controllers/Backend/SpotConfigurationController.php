<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Spot;
use App\Models\Surface;
use App\Services\SpotXmlGenerator;
use Illuminate\Http\Request;

class SpotConfigurationController extends Controller
{
    public function show(Spot $spot)
    {
        if (!file_exists($spot->xml_path)){
            $spot->generateXml();
        }

        $xml = file_get_contents($spot->xml_path);
        return view('backend.spot.configuration.show', compact('xml', 'spot'));
    }

    public function edit(Spot $spot)
    {
        $spot->load('surfaces.media');
        $data = array();
        $data['spot'] = $spot;

        return view('backend.spot.configuration.form', $data);
    }

    public function update(Request $request, Spot $spot)
    {
        $spot->update([
            'xml' => $request->only([
                'view', 'surfaces', 'overlays', 'scale_box', 'navigations', 'quick_actions'
            ])
        ]);

        $this->uploadOverlyImages($request, $spot);
        $this->uploadSurfacesSharedImage($request, $spot);

        $spot->generateXml();

        return redirect()
            ->route('backend.spot-configuration.edit', [
                $spot,
                'section' => $request->section
            ])
            ->with('success', 'Xml updated');
    }

    private function uploadOverlyImages(Request $request, Spot $spot){
        $overlays = $request->overlays ?? [];

        $this->addOverlayAttributes($request, $overlays);

        foreach ($overlays as $index => $overlay){
            if ( ! isset($overlay['image'])) {
                continue;
            }

            // remove old media if exists!
            $spot->getFirstMedia('overlays', [
                'uuid' => $overlay['uuid']
            ])?->delete();

            $spot->addFromMediaLibraryRequest($request->overlays[$index]['image'])
                ->withCustomProperties('uuid')
                ->toMediaCollection('overlays');
        }
    }

    private function uploadSurfacesSharedImage(Request $request, Spot $spot){
        $surfaces = $request->surfaces ?? [];

        $this->addSurfaceAttributes($request, $spot, $surfaces);

        foreach ($surfaces as $surface_id => $surface){
            if ( ! isset($surface['shared_image'])) {
                continue;
            }

            $surfaceModel = Surface::find($surface_id);

            // remove old media if exists!
            $surfaceModel->getFirstMedia('shared', [
                'spot_id' => $spot->id
            ])?->delete();

            $surfaceModel->addFromMediaLibraryRequest($request->surfaces[$surface_id]['shared_image'])
                ->withCustomProperties('spot_id')
                ->toMediaCollection('shared');
        }
    }

    // add uuid to overlay image custom fields
    private function addOverlayAttributes(Request $request, $overlays)
    {
        if (!$overlays){
            return;
        }

        foreach ($overlays as &$overlay){
            if ( ! isset($overlay['image'])) {
                continue;
            }

            $uuid = array_key_first($overlay['image']);

            $overlay['image'][$uuid]['custom_properties'] = array(
                'uuid' => $overlay['uuid']
            );
        }

        $request->merge([
            'overlays' => $overlays
        ]);
    }

    // add spot_id to shared image custom fields
    private function addSurfaceAttributes(Request $request, $spot, $surfaces)
    {
        if (!$surfaces){
            return;
        }

        foreach ($surfaces as &$surface){
            if ( ! isset($surface['shared_image'])) {
                continue;
            }

            $uuid = array_key_first($surface['shared_image']);

            $surface['shared_image'][$uuid]['custom_properties'] = array(
                'spot_id' => $spot->id
            );
        }

        $request->merge([
            'surfaces' => $surfaces
        ]);
    }
}
