<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Spot;
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
        $data = array();
        $data['spot'] = $spot;

        return view('backend.spot.configuration.form', $data);
    }

    public function update(Request $request, Spot $spot)
    {
        $spot->update([
            'xml' => $request->only([
                'view', 'surfaces', 'overlays', 'scale_box', 'navigations'
            ])
        ]);

        $spot->generateXml();

        return redirect()->back()->with('success', 'Xml updated');
    }
}