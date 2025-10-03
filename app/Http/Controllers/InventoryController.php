<?php
namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\ArtworkCollection;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class InventoryController extends Controller
{

    public function index(Request $request)
    {
        $inventory = Artwork::paginate(25);
        $collections = ArtworkCollection::latest('name')->get();
        $selectedCollection = $request->get('collection_id', '');
        
        return view('inventory.index', compact('inventory', 'collections', 'selectedCollection'));
    }

    public function datatable()
    {
        return view('inventory.datatable');
    }

    public function getData()
    {
        $artworks = Artwork::with('collection', 'company')
            ->select(['id', 'name', 'artist', 'type', 'data', 'original_unit', 'original_value', 'artwork_collection_id', 'company_id', 'created_at'])
            ->get()
            ->map(function ($artwork) {
                $data = $artwork->data ?? [];
                $originalValue = $artwork->original_value ?? [];

                return [
                    'DT_RowId' => 'row_' . $artwork->id,
                    'id' => $artwork->id,
                    'image' => $artwork->image_url ?? '/images/placeholder.jpg',
                    'company' => $artwork->company->name ?? '',
                    'collection' => $artwork->collection->name ?? '',
                    'name' => $artwork->name,
                    'artist' => $artwork->artist,
                    'type' => $artwork->type,
                    'height' => $originalValue['height'] ?? '',
                    'width' => $originalValue['width'] ?? '',
                    'unit' => $originalValue['unit'] ?? 'cm',
                    'created_at' => $artwork->created_at->format('Y-m-d'),
                ];
            });

        return response()->json([
            'data' => $artworks
        ]);
    }

    public function editor(Request $request)
    {
        $action = $request->input('action');

        try {
            switch ($action) {
                case 'create':
                    return $this->editorCreate($request);
                case 'edit':
                    return $this->editorEdit($request);
                case 'remove':
                    return $this->editorRemove($request);
                default:
                    // Return data for DataTables Editor
                    return $this->getData();
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }

    private function editorCreate(Request $request)
    {
        $data = $request->input('data');
        $artworkData = $data[0]; // DataTables Editor sends data as array

        $artwork = new Artwork();
        $artwork->company_id = user()->company_id;
        $artwork->artwork_collection_id = ArtworkCollection::first()->id; // Use first collection
        $artwork->name = $artworkData['name'] ?? '';
        $artwork->artist = $artworkData['artist'] ?? '';
        $artwork->type = $artworkData['type'] ?? '';

        // Handle dimensions
        $originalValue = [
            'height' => $artworkData['height'] ?? '',
            'width' => $artworkData['width'] ?? '',
            'unit' => $artworkData['unit'] ?? 'cm'
        ];
        $artwork->original_value = $originalValue;

        // Convert to inches for data column
        if ($originalValue['unit'] === 'cm' && !empty($originalValue['width']) && !empty($originalValue['height'])) {
            $artwork->data = [
                'width_inch' => round($originalValue['width'] / 2.54, 5),
                'height_inch' => round($originalValue['height'] / 2.54, 5)
            ];
        }

        $artwork->save();

        return response()->json([
            'data' => [[
                'DT_RowId' => 'row_' . $artwork->id,
                'id' => $artwork->id,
                'image' => $artwork->image_url ?? '/images/placeholder.jpg',
                'company' => $artwork->company->name ?? '',
                'collection' => $artwork->collection->name ?? '',
                'name' => $artwork->name,
                'artist' => $artwork->artist,
                'type' => $artwork->type,
                'height' => $originalValue['height'] ?? '',
                'width' => $originalValue['width'] ?? '',
                'unit' => $originalValue['unit'] ?? 'cm',
                'created_at' => $artwork->created_at->format('Y-m-d'),
            ]]
        ]);
    }

    private function editorEdit(Request $request)
    {
        $data = $request->input('data');

        foreach ($data as $rowId => $rowData) {
            $id = str_replace('row_', '', $rowId);
            $artwork = Artwork::findOrFail($id);

            $artwork->name = $rowData['name'] ?? $artwork->name;
            $artwork->artist = $rowData['artist'] ?? $artwork->artist;
            $artwork->type = $rowData['type'] ?? $artwork->type;

            // Handle dimensions
            if (isset($rowData['height']) || isset($rowData['width']) || isset($rowData['unit'])) {
                $originalValue = $artwork->original_value ?? [];
                $originalValue['height'] = $rowData['height'] ?? $originalValue['height'] ?? '';
                $originalValue['width'] = $rowData['width'] ?? $originalValue['width'] ?? '';
                $originalValue['unit'] = $rowData['unit'] ?? $originalValue['unit'] ?? 'cm';
                $artwork->original_value = $originalValue;

                // Convert to inches for data column
                if ($originalValue['unit'] === 'cm' && !empty($originalValue['width']) && !empty($originalValue['height'])) {
                    $artwork->data = [
                        'width_inch' => round($originalValue['width'] / 2.54, 5),
                        'height_inch' => round($originalValue['height'] / 2.54, 5)
                    ];
                }
            }

            $artwork->save();
        }

        return response()->json([
            'data' => []
        ]);
    }

    private function editorRemove(Request $request)
    {
        $data = $request->input('data');

        foreach ($data as $rowId) {
            $id = str_replace('row_', '', $rowId);
            $artwork = Artwork::findOrFail($id);
            $artwork->delete();
        }

        return response()->json([
            'data' => []
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            $artwork = Artwork::findOrFail($id);

            $artwork->name = $request->input('name', $artwork->name);
            $artwork->artist = $request->input('artist', $artwork->artist);
            $artwork->type = $request->input('type', $artwork->type);

            // Handle dimensions
            if ($request->has('height') || $request->has('width') || $request->has('unit')) {
                $originalValue = $artwork->original_value ?? [];
                $originalValue['height'] = $request->input('height', $originalValue['height'] ?? '');
                $originalValue['width'] = $request->input('width', $originalValue['width'] ?? '');
                $originalValue['unit'] = $request->input('unit', $originalValue['unit'] ?? 'cm');
                $artwork->original_value = $originalValue;

                // Convert to inches for data column
                if ($originalValue['unit'] === 'cm' && !empty($originalValue['width']) && !empty($originalValue['height'])) {
                    $data = $artwork->data ?? [];
                    $data['width_inch'] = round($originalValue['width'] / 2.54, 5);
                    $data['height_inch'] = round($originalValue['height'] / 2.54, 5);
                    $artwork->data = $data;
                }
            }

            $artwork->save();

            return response()->json([
                'success' => true,
                'message' => 'Artwork updated successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating artwork: ' . $e->getMessage()
            ], 500);
        }
    }

    private function formatDimensions($originalValue, $data)
    {
        $height = $originalValue['height'] ?? '';
        $width = $originalValue['width'] ?? '';
        $unit = $originalValue['unit'] ?? 'cm';

        if ($height && $width) {
            return "{$height} x {$width} {$unit}";
        }

        return '';
    }

    public function destroy($id)
    {
        try {
            $artwork = Artwork::findOrFail($id);
            $artwork->delete();

            return response()->json([
                'success' => true,
                'message' => 'Artwork deleted successfully'
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting artwork: ' . $e->getMessage()
            ], 500);
        }
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
        try {
            $artwork = new Artwork();
            $artwork->company_id = user()->company_id;
            $artwork->name = $request->input('name', '');
            $artwork->artist = $request->input('artist', '');
            $artwork->type = $request->input('type', '');

            // Handle dimensions
            if ($request->has('height') || $request->has('width') || $request->has('unit')) {
                $originalValue = [
                    'height' => $request->input('height', ''),
                    'width' => $request->input('width', ''),
                    'unit' => $request->input('unit', 'cm')
                ];
                $artwork->original_value = $originalValue;

                // Convert to inches for data column
                if ($originalValue['unit'] === 'cm' && !empty($originalValue['width']) && !empty($originalValue['height'])) {
                    $data = [
                        'width_inch' => round($originalValue['width'] / 2.54, 5),
                        'height_inch' => round($originalValue['height'] / 2.54, 5),
                    ];
                    $artwork->data = $data;
                }
            }

            $artwork->save();

            return response()->json([
                'success' => true,
                'message' => 'Artwork created successfully',
                'data' => [
                    'DT_RowId' => 'row_' . $artwork->id,
                    'id' => $artwork->id,
                    'image' => $artwork->image_url ?? '/images/placeholder.jpg',
                    'company' => $artwork->company->name ?? '',
                    'collection' => $artwork->collection->name ?? '',
                    'name' => $artwork->name,
                    'artist' => $artwork->artist,
                    'type' => $artwork->type,
                    'height' => $artwork->original_value['height'] ?? '',
                    'width' => $artwork->original_value['width'] ?? '',
                    'unit' => $artwork->original_value['unit'] ?? 'cm',
                    'created_at' => $artwork->created_at->format('Y-m-d'),
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating artwork: ' . $e->getMessage()
            ], 500);
        }
    }

    public function show(Artwork $artwork)
    {
        //
    }

    public function edit(Artwork $artwork)
    {
        //
    }


    public function addArtworks(Request $request)
    {

        // Increase PHP execution time and memory limit for large uploads
        ini_set('max_execution_time', 300); // 5 minutes
        // ini_set('memory_limit', '512M'); // 512MB memory limit
        // set_time_limit(300); // 5 minutes timeout

        try {
            $artworkData = json_decode($request->input('artwork_data'), true);

            if (!$artworkData || !is_array($artworkData)) {
                return response()->json(['success' => false, 'message' => 'Invalid data.'], 400);
            }

            // Validate required fields
            foreach ($artworkData as $index => $row) {
                if (empty($row['title'])) {
                    return response()->json(['success' => false, 'message' => "Title is required for artwork #{$index}."], 400);
                }

                if (empty($row['collection_name'])) {
                    return response()->json(['success' => false, 'message' => "Collection is required for artwork #{$index}."], 400);
                }
            }

            $createdCount = 0;
            $errors = [];

            foreach ($artworkData as $index => $row) {
                try {
                    // Create artwork object first
                    $artwork = new Artwork();
                    $artwork->company_id = user()->company_id;

                    // Find collection by name
                    $collection = ArtworkCollection::where('name', $row['collection_name'])->first();
                    if (!$collection) {
                        $errors[] = "Collection '{$row['collection_name']}' not found for artwork #{$index}.";
                        continue;
                    }
                    $artwork->artwork_collection_id = $collection->id;

                    $artwork->name = $row['title'] ?? '';
                    $artwork->artist = $row['artist'] ?? '';
                    $artwork->type = $row['type'] ?? '';
                    $artwork->description = $row['description'] ?? '';
                    $artwork->original_unit = $row['unit'] ?? '';

                    // Set artwork data
                    $artwork->data = [
                        'height_inch' => $row['height'] ?? '',
                        'width_inch' => $row['width'] ?? '',
                        'scale' => $row['scale'] ?? '',
                    ];

                    // Set original_value as JSON object with width, height, and unit
                    $artwork->original_value = [
                        'width' => $row['width'] ?? '',
                        'height' => $row['height'] ?? '',
                        'unit' => $row['unit'] ?? 'cm'
                    ];

                    // Convert to inches if unit is cm and save to data column
                    if (($row['unit'] ?? 'cm') === 'cm' && !empty($row['width']) && !empty($row['height'])) {
                        // Convert cm to inches (1 inch = 2.54 cm)
                        $widthInch = round($row['width'] / 2.54, 5);
                        $heightInch = round($row['height'] / 2.54, 5);

                        $artwork->data = [
                            'width_inch' => $widthInch,
                            'height_inch' => $heightInch,
                            'scale' => $row['scale'] ?? '',
                        ];
                    } else {
                        // If unit is inch or not specified, use values as is
                        $artwork->data = [
                            'width_inch' => $row['width'] ?? '',
                            'height_inch' => $row['height'] ?? '',
                            'scale' => $row['scale'] ?? '',
                        ];
                    }

                    // Save artwork first to get the ID
                    $artwork->save();

                    // Handle image if provided
                    if (!empty($row['image']) && str_starts_with($row['image'], 'data:image')) {
                        try {
                            // Extract base64 data from data URL
                            $base64Data = $row['image'];

                            // Add image to media collection
                            $artwork->addMediaFromBase64($base64Data)
                                ->usingFileName('artwork_' . $artwork->id . '_' . time() . '.jpg')
                                ->usingName($artwork->name)
                                ->toMediaCollection('image');

                            // Refresh model to ensure media is attached
                            $artwork->refresh();

                            // Resize image if dimensions are available
                            if (!empty($artwork->data->width_inch) && !empty($artwork->data->height_inch)) {
                                $artwork->resizeImage();
                            }
                        } catch (\Exception $e) {
                            // Continue without failing the entire operation
                        }
                    }

                    $createdCount++;

                } catch (\Exception $e) {
                    $errors[] = "Error creating artwork #{$index}: " . $e->getMessage();
                }
            }

            $response = [
                'success' => true,
                'message' => "Successfully created {$createdCount} artwork(s).",
                'created_count' => $createdCount
            ];

            if (!empty($errors)) {
                $response['errors'] = $errors;
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        $ids = $request->input('ids', []);
        if (!empty($ids)) {
            Artwork::whereIn('id', $ids)->delete();
            return response()->json(['success' => true]);
        }
        return response()->json(['success' => false], 400);
    }
}
