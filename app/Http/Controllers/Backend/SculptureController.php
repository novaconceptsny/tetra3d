<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ValidationRules;
use App\Http\Controllers\Controller;
use App\Models\Artwork;
use App\Models\ArtworkCollection;
use App\Models\SculptureModel;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SculptureController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return view('backend.sculpture.index');
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        $data = array();

        $data['route'] = route('backend.sculptures.store');
        $data['method'] = 'POST';
        $data['artwork_collections'] = ArtworkCollection::all();
        return view('backend.sculpture.form', $data);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(ValidationRules::storeSculpture());
        
        $data = $request->all();
        $sculpture = $request->file('sculpture');
        $sculpture_fileName = $sculpture->getClientOriginalName();
        if ($data['thumbnail-canvas']) {
            $thumbnail= $data['thumbnail-canvas'];
            $thumbnail = str_replace('data:image/png;base64,', '', $thumbnail);
            $thumbnail = str_replace(' ', '+', $thumbnail);
            $thumbnail_fileName ='thumbnail.png';
        } else {
            $thumbnail = $request->file('thumbnail');
            $thumbnail_fileName = $thumbnail->getClientOriginalName();
        }

        $save_data = array('name'=>$data['name'], 
            'artist'=>$data['artist'], 
            'artwork_collection_id'=>(int)$data['artwork_collection_id'],
            'sculpture_url'=>'', 
            'image_url'=>'', 
            'data'=>json_encode($data['data'])
        );
        $createdData = SculptureModel::create($save_data);

        $createdData->sculpture_url = $createdData->id.'_'.$sculpture_fileName;
        $createdData->image_url = $createdData->id.'_'.$thumbnail_fileName;
        $createdData->save();

        $test_sculpture = SculptureModel::where('id', $createdData->id)->get();

        $sculpture->storeAs('public/sculptures', $createdData->sculpture_url);
        if ($data['thumbnail-canvas']) {
            Storage::put('public/sculptures/thumbnails/' . $createdData->image_url, base64_decode($thumbnail));
        } else {
            $thumbnail->storeAs('public/sculptures/thumbnails', $createdData->image_url);
        }
        
        return redirect()->back()->with('success', 'Sculpture created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(SculptureModel $sculptureModel)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        
        $sculptureModel = SculptureModel::where('id', $id)->get();

        $sculptureModel[0]->data = json_decode($sculptureModel[0]->data);

        $artworkCollections = ArtworkCollection::all();

        $data = array();

        $data['route'] = route('backend.sculptures.update', $sculptureModel[0]->id);
        $data['method'] = 'PUT';
        $data['sculpture'] = $sculptureModel[0];
        $data['artwork_collections'] = $artworkCollections;

        return view('backend.sculpture.form', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string  $id)
    {
        $data = $request->all();
        $updatedData = SculptureModel::where('id', $id)->get();

        $sculpture = $request->file('sculpture');
        if ($sculpture) {
            $sculpture_fileName = $sculpture->getClientOriginalName();
            $updatedData[0]->sculpture_url = $updatedData[0]->id.'_'.$sculpture_fileName;
            $sculpture->storeAs('public/sculptures', $updatedData[0]->sculpture_url);
        }
        
        if ($data['thumbnail-canvas']) {
            $thumbnail= $data['thumbnail-canvas'];
            $thumbnail = str_replace('data:image/png;base64,', '', $thumbnail);
            $thumbnail = str_replace(' ', '+', $thumbnail);
            $thumbnail_fileName ='thumbnail.png';
            Storage::put('public/sculptures/thumbnails/' . $updatedData[0]->image_url, base64_decode($thumbnail));
        } else {
            $thumbnail = $request->file('thumbnail');
            if ($thumbnail) {
                $thumbnail_fileName = $thumbnail->getClientOriginalName();
                $updatedData[0]->image_url = $updatedData[0]->id.'_'.$thumbnail_fileName;
                $thumbnail->storeAs('public/sculptures/thumbnails', $updatedData[0]->image_url);
            }
        }

        $updatedData[0]->name = $data['name'];
        $updatedData[0]->artist = $data['artist'];
        $updatedData[0]->artwork_collection_id = $data['artwork_collection_id'];
        $updatedData[0]->data = json_encode($data['data']);
        $updatedData[0]->save();
        
        return view('backend.sculpture.index')->with('success', 'Sculpture updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        $sculptureModel = SculptureModel::where('id', $id)->get();
        $sculptureModel[0]->delete();

        return redirect()->back()->with('success', 'Sculpture deleted successfully');
    }
}
