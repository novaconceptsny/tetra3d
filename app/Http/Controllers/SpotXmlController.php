<?php

namespace App\Http\Controllers;

use App\Models\Spot;
use App\Services\SpotXmlGenerator;
use Illuminate\Http\Request;

class SpotXmlController extends Controller
{
    public function edit(Spot $spot)
    {
        $data = array();
        $data['spot'] = $spot;

        return view('backend.spot.xml.form', $data);
    }

    public function update(Request $request, Spot $spot)
    {
        $spot->update([
            'xml' => $request->only([
                'view', 'surfaces', 'overlays'
            ])
        ]);

        $xmlGenerator = new SpotXmlGenerator($spot);

        return redirect()->back()->with('success', 'Xml updated');
    }
}
