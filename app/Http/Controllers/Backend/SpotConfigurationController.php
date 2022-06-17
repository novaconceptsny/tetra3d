<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use App\Models\Spot;
use App\Services\SpotXmlGenerator;
use Illuminate\Http\Request;

class SpotConfigurationController extends Controller
{
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
                'view', 'surfaces', 'overlays'
            ])
        ]);

        $xmlGenerator = new SpotXmlGenerator($spot);
        $xmlGenerator->createXml();

        return redirect()->back()->with('success', 'Xml updated');
    }
}
