<?php

namespace App\Http\Controllers;

use App\Models\SpotsPosition;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\Models\Sculpture;

class SculptureController extends Controller
{
    //
    public function load(Request $request) {
        $request->validate([
            "layout_id" => 'required',
            "spot_id" => 'required',
        ]);
        
        $data = $request->all();
        $sculpture_datas = Sculpture::where('layout_id', $data['layout_id'])->get();
        $spot_position = SpotsPosition::where('spot_id', $data['spot_id'])->get();

        if ($sculpture_datas->isEmpty()) {
            return response()->json([
                'sculpture_data' => 'no_sculpture',
                'spot_position' => 'no_spot_data'
            ]);
        } elseif($spot_position->isEmpty()) {
            return response()->json([
                'sculpture_data' => 'no_sculpture',
                'spot_position' => 'no_spot_data'
            ]);
        } else {
            return response()->json([
                'sculpture_data' => $sculpture_datas,
                'spot_position' => $spot_position[0],
            ]);
        }
    }
    public function save(Request $request)
    {
        $request->validate([
            "layout_id" => 'required',
            'sculpture_id' => 'required'
        ]);
        $data = $request->all();
        $sculpture_datas = Sculpture::where('layout_id', $data['layout_id'])->where('sculpture_id', $data['sculpture_id'])->get();
        if ($sculpture_datas->isEmpty()) {
            $createdData = Sculpture::create($data);
            return response()->json([
                'response' => $data
            ]);
        } else {
            foreach ($sculpture_datas as $sculpture_data) {
                $sculpture_data->model_id = $data['model_id'];
                $sculpture_data->position_x = $data['position_x'];
                $sculpture_data->position_y = $data['position_y'];
                $sculpture_data->position_z = $data['position_z'];
                $sculpture_data->rotation_x = $data['rotation_x'];
                $sculpture_data->rotation_y = $data['rotation_y'];
                $sculpture_data->rotation_z = $data['rotation_z'];
                $sculpture_data->save();
            }
            return response()->json([
                'request' => $sculpture_datas
            ]);
        }
    }

    public function delete(Request $request)
    {
        $request->validate([
            "layout_id" => 'required',
            'sculpture_id' => 'required'
        ]);
        $data = $request->all();
        $sculpture_datas = Sculpture::where('layout_id', $data['layout_id'])->where('sculpture_id', $data['sculpture_id'])->get();
        if ($sculpture_datas->isEmpty()) {
            return response()->json([
                'response' => 'no data'
            ]);
        } else {
            foreach ($sculpture_datas as $sculpture_data)
                $sculpture_data->delete();
            return response()->json([
                'response' => 'success'
            ]);
        }
    }
}
