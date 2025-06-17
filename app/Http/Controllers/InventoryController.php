<?php
namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\ArtworkCollection;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = Artwork::paginate(25);
        return view('inventory.index', compact('inventory'));
    }

    public function addCollection(Request $request)
    {
        try {
            $request->validate([
                'collection_name'         => 'required|string|max:255',
                'collection_company_name' => 'required|string|max:255',
                'collection_thumbnail'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $collection             = new ArtworkCollection();
            $thumbnail              = $request->file('collection_thumbnail');
            $collection->name       = $request->collection_name;
            $collection->company_id = user()->isAdmin() ?
            Company::where('name', $request->collection_company_name)->first()->id :
            user()->company_id;

            if ($request->hasFile('collection_thumbnail')) {
                $path                      = $thumbnail->storeAs('media/collections', $thumbnail->getClientOriginalName(), 'public');
                $collection->thumbnail_url = Storage::url($path);
            }

            $collection->save();

            return response()->json([
                'success'    => true,
                'message'    => 'Collection added successfully',
                'collection' => $collection,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Artwork $artwork)
    {
        //
    }

    public function edit(Artwork $artwork)
    {
        //
    }

    public function update(Request $request, Artwork $artwork)
    {
        //
    }

    public function destroy(Artwork $artwork)
    {
        //
    }

    public function addArtworks(Request $request)
    {
        try {
            $artworkData = json_decode($request->input('artwork_data'), true);

            if (!$artworkData || !is_array($artworkData)) {
                return response()->json(['success' => false, 'message' => 'Invalid data.'], 400);
            }

            foreach ($artworkData as $row) {

                if($row['image'] != null) {
                    $path = $row['image'];
                    $artwork->image_url = Storage::url($path);
                }
                // You may want to validate each row here
                $artwork = new Artwork();
                $artwork->company_id = user()->company_id;
                $artwork->artwork_collection_id = ArtworkCollection::where('name', $row['collection_name'])->first()->id;
                $artwork->name = $row['title'] ?? '';
                $artwork->artist = $row['artist'] ?? '';
                $artwork->type = $row['type'] ?? '';
                $artwork->image_url = $row['image'] ?? '';
                $artwork->data = [
                    'width' => $row['width'] ?? '',
                    'height' => $row['height'] ?? '',
                    'scale' => $row['scale'] ?? '',
                ];
                $artwork->save();
            }

            return response()->json(['success' => true, 'message' => 'Artworks added successfully']);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
