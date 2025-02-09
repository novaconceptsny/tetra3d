<?php

namespace App\Http\Controllers\Backend;

use App\Helpers\ValidationRules;
use App\Http\Controllers\Controller;
use App\Models\Activity;
use App\Models\Artwork;
use App\Models\ArtworkCollection;
use App\Models\SculptureModel;
use App\Models\Project;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SculptureController extends Controller
{
    public function __construct()
    {
        $this->authorizeResource(SculptureModel::class, 'sculptureModel');
    }

    public function index()
    {
        return view('backend.sculpture.index');
    }

    public function create()
    {
        $companies = Company::all();
        $artwork_collections = ArtworkCollection::all();
        
        return view('backend.sculpture.form', [
            'route' => route('backend.sculptures.store'),
            'method' => 'POST',
            'companies' => $companies,
            'artwork_collections' => $artwork_collections,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate(ValidationRules::storeSculpture());

        $sculptureModel = SculptureModel::create($request->only([
            'company_id',
            'name',
            'artist',
            'type',
            'data',
            'artwork_collection_id'
        ]));

        error_log($sculptureModel);

        $sculptureModel->addFromMediaLibraryRequest($request->sculpture)
            ->toMediaCollection('sculpture');

        $sculptureModel->addFromMediaLibraryRequest($request->interaction)
            ->toMediaCollection('interaction');

        $sculptureModel->addFromMediaLibraryRequest($request->thumbnail)
            ->toMediaCollection('thumbnail');

        $sculptureModel->refresh();

        
        Activity::create([
            'user_id' => auth()->user()->id,
            'company_id' => $sculptureModel->company_id,
            'activity' => "Sculpture '{$sculptureModel->name}' Created",
        ]);

        return redirect()->back()->with('success', 'Sculpture created successfully');
    }

    /**
     * Display the specified resource.
     */
    public function show(SculptureModel $sculpture)
    {

    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(SculptureModel $sculpture)
    {
        Activity::create([
            'user_id' => auth()->id(),
            'activity' => "Sculpture '{$sculpture->name}' Edited",
        ]);

        $data = array();

        $data['route'] = route('backend.sculptures.update', $sculpture);
        $data['method'] = 'PUT';
        $data['sculpture'] = $sculpture;
        $data['artwork_collections'] = ArtworkCollection::all();
        $data['companies'] = Company::all();

        return view('backend.sculpture.form', $data);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, SculptureModel $sculpture)
    {
        $request->validate(ValidationRules::updateSculpture());

        $sculpture->update($request->only([
            'company_id',
            'name',
            'artist',
            'type',
            'data',
            'artwork_collection_id'
        ]));

        $sculpture->addFromMediaLibraryRequest($request->sculpture->sculpture)
            ->toMediaCollection('sculpture');

        $sculpture->addFromMediaLibraryRequest($request->sculpture->interaction)
            ->toMediaCollection('interaction');

        $sculpture->addFromMediaLibraryRequest($request->sculpture->thumbnail)
            ->toMediaCollection('thumbnail');

        $sculpture->refresh();

        Activity::create([
            'user_id' => auth()->id(),
            'activity' => "Sculpture '{$sculpture->name}' Updated",
        ]);

        return view('backend.sculpture.index')->with('success', 'Sculpture updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(SculptureModel $sculpture)
    {
        $sculpture->delete();

        Activity::create([
            'user_id' => auth()->id(),
            'activity' => "Sculpture '{$sculpture->name}' Deleted",
        ]);

        return redirect()->back()->with('success', 'Sculpture deleted successfully');
    }
}
