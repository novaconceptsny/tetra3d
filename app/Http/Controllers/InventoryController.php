<?php
namespace App\Http\Controllers;

use App\Models\Artwork;
use App\Models\ArtworkCollection;
use App\Models\Company;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use ZipArchive;

class InventoryController extends Controller
{

    public function index(Request $request)
    {
        $inventory          = Artwork::orderBy('updated_at', 'desc')->paginate(25);
        
        // Filter collections by company for non-super admin users
        if (auth()->user()->isSuperAdmin()) {
            $collections = ArtworkCollection::latest('name')->get();
        } else {
            $collections = ArtworkCollection::where('company_id', auth()->user()->company_id)
                ->latest('name')->get();
        }
        
        $companies          = Company::latest('name')->get();
        $selectedCollection = $request->get('collection_id', '');

        return view('inventory.index', compact('inventory', 'collections', 'selectedCollection', 'companies'));
    }

    public function datatable()
    {
        return view('inventory.datatable');
    }

    public function getData(Request $request)
    {
        try {
            // DataTables server-side processing parameters
            $draw        = $request->input('draw');
            $start       = $request->input('start', 0);
            $length      = $request->input('length', 10);
            $searchValue = $request->input('search.value', '');
            $orderColumn = $request->input('order.0.column', user()->isSuperAdmin() ? 10 : 9); // Default to description column
            $orderDir    = $request->input('order.0.dir', 'desc');

        // Column mapping for ordering
        $columns = [
            0  => 'id',         // Checkbox column
            1  => 'image',      // Image column (not orderable)
            2  => 'company',    // Company (only for super admin)
            3  => 'collection', // Collection
            4  => 'name',       // Name
            5  => 'artist',     // Artist
            6  => 'type',       // Type
            7  => 'height',     // Height
            8  => 'width',      // Width
            9  => 'unit',       // Unit
            10 => 'description', // Description
        ];

        // Adjust column mapping if user is not super admin
        if (! user()->isSuperAdmin()) {
            $columns = [
                0 => 'id',         // Checkbox column
                1 => 'image',      // Image column (not orderable)
                2 => 'collection', // Collection
                3 => 'name',       // Name
                4 => 'artist',     // Artist
                5 => 'type',       // Type
                6 => 'height',     // Height
                7 => 'width',      // Width
                8 => 'unit',       // Unit
                9 => 'description', // Description
            ];
        }

        $orderBy = $columns[$orderColumn] ?? 'updated_at';

        // Base query
        $query = Artwork::with('collection', 'company')
            ->select(['artworks.id', 'artworks.name', 'artworks.artist', 'artworks.type', 'artworks.description', 'artworks.data', 'artworks.original_unit', 'artworks.original_value', 'artworks.artwork_collection_id', 'artworks.company_id', 'artworks.created_at', 'artworks.updated_at']);

        // Filter by collection if provided
        if ($request->has('collection_id') && $request->collection_id) {
            $query->where('artworks.artwork_collection_id', $request->collection_id);
        }

        // Apply search filter
        if (! empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('artworks.name', 'like', "%{$searchValue}%")
                    ->orWhere('artworks.artist', 'like', "%{$searchValue}%")
                    ->orWhere('artworks.type', 'like', "%{$searchValue}%")
                    ->orWhereHas('collection', function ($subQuery) use ($searchValue) {
                        $subQuery->where('name', 'like', "%{$searchValue}%");
                    })
                    ->orWhereHas('company', function ($subQuery) use ($searchValue) {
                        $subQuery->where('name', 'like', "%{$searchValue}%");
                    });
            });
        }

        // Get total records count (before pagination)
        $totalRecords = $query->count();

        // Apply ordering
        if ($orderBy === 'company') {
            $query->leftJoin('companies', 'artworks.company_id', '=', 'companies.id')
                ->orderBy('companies.name', $orderDir);
        } elseif ($orderBy === 'collection') {
            $query->leftJoin('artwork_collections', 'artworks.artwork_collection_id', '=', 'artwork_collections.id')
                ->orderBy('artwork_collections.name', $orderDir);
        } else {
            $query->orderBy($orderBy, $orderDir);
        }

        // Apply pagination
        $artworks = $query->skip($start)->take($length)->get();

        // Transform data for DataTables
        $data = $artworks->map(function ($artwork) {
            $originalValue = $artwork->original_value ?? [];
            $rowData       = [
                'DT_RowId'   => 'row_' . $artwork->id,
                'id'         => $artwork->id,
                'image'      => $artwork->image_url ?? '/images/placeholder.jpg',
                'collection' => $artwork->collection->name ?? '',
                'name'       => $artwork->name,
                'artist'     => $artwork->artist,
                'type'       => $artwork->type,
                'height'     => $originalValue['height'] ?? '',
                'width'      => $originalValue['width'] ?? '',
                'unit'       => $originalValue['unit'] ?? 'cm',
                'description' => $artwork->description ?? '',
                'created_at' => $artwork->created_at->format('Y-m-d'),
            ];

            // Only include company data for super admin users
            if (user()->isSuperAdmin()) {
                $rowData['company'] = $artwork->company->name ?? '';
            }

            return $rowData;
        });

        return response()->json([
            'draw'            => intval($draw),
            'recordsTotal'    => $totalRecords,
            'recordsFiltered' => $totalRecords, // Same as total since we're not doing separate filtered count
            'data'            => $data,
        ]);
        
        } catch (\Exception $e) {
            \Log::error('DataTables getData error: ' . $e->getMessage(), [
                'request_data' => $request->all(),
                'trace' => $e->getTraceAsString()
            ]);
            
            return response()->json([
                'draw'            => intval($request->input('draw', 1)),
                'recordsTotal'    => 0,
                'recordsFiltered' => 0,
                'data'            => [],
                'error'           => 'Error loading data: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function export(Request $request)
    {
        ini_set('max_execution_time', 600);
        ini_set('memory_limit', '1024M');

        $collectionId = $request->input('collection_id');
        $searchValue  = $request->input('q');

        // Build artworks query similar to getData()
        $query = \App\Models\Artwork::with('collection', 'company', 'media')
            ->select(['artworks.id', 'artworks.name', 'artworks.artist', 'artworks.type', 'artworks.description', 'artworks.data', 'artworks.original_unit', 'artworks.original_value', 'artworks.artwork_collection_id', 'artworks.company_id', 'artworks.created_at']);

        if ($collectionId) {
            $query->where('artworks.artwork_collection_id', $collectionId);
        }

        if (!empty($searchValue)) {
            $query->where(function ($q) use ($searchValue) {
                $q->where('artworks.name', 'like', "%{$searchValue}%")
                    ->orWhere('artworks.artist', 'like', "%{$searchValue}%")
                    ->orWhere('artworks.type', 'like', "%{$searchValue}%")
                    ->orWhereHas('collection', function ($subQuery) use ($searchValue) {
                        $subQuery->where('name', 'like', "%{$searchValue}%");
                    })
                    ->orWhereHas('company', function ($subQuery) use ($searchValue) {
                        $subQuery->where('name', 'like', "%{$searchValue}%");
                    });
            });
        }

        // For non-super admin, scope to their company
        if (! user()->isSuperAdmin()) {
            $query->where('artworks.company_id', user()->company_id);
        }

        $artworks = $query->orderBy('artworks.updated_at', 'desc')->get();

        // Prepare temp paths
        $timestamp = now()->format('Ymd_His');
        $baseName = 'inventory_export_' . $timestamp;
        $tempDir = storage_path('app/exports');
        if (! is_dir($tempDir)) {
            @mkdir($tempDir, 0775, true);
        }

        $csvPath = $tempDir . DIRECTORY_SEPARATOR . $baseName . '.csv';
        $zipPath = $tempDir . DIRECTORY_SEPARATOR . $baseName . '.zip';

        // Write CSV
        $csv = fopen($csvPath, 'w');
        // Header
        fputcsv($csv, [
            'ID', 'Company', 'Collection', 'Name', 'Artist', 'Type', 'Height', 'Width', 'Unit', 'Description', 'Image File'
        ]);

        // Collect image files to add in zip
        $filesToZip = [];

        foreach ($artworks as $artwork) {
            $originalValue = (array) ($artwork->original_value ?? []);
            $height = $originalValue['height'] ?? '';
            $width  = $originalValue['width'] ?? '';
            $unit   = $originalValue['unit'] ?? ($artwork->original_unit ?: 'cm');

            $imageFileName = '';
            $media = $artwork->getFirstMedia('image');
            if ($media) {
                $sourcePath = $media->getPath();
                if ($sourcePath && file_exists($sourcePath)) {
                    // Build a readable file name
                    $safeName = Str::slug($artwork->name ?: ('artwork-' . $artwork->id));
                    $imageFileName = $safeName . '-' . $artwork->id . '.' . pathinfo($sourcePath, PATHINFO_EXTENSION);
                    $filesToZip[] = ['path' => $sourcePath, 'name' => 'images/' . $imageFileName];
                }
            }

            fputcsv($csv, [
                $artwork->id,
                $artwork->company->name ?? '',
                $artwork->collection->name ?? '',
                $artwork->name,
                $artwork->artist,
                $artwork->type,
                $height,
                $width,
                $unit,
                $artwork->description,
                $imageFileName,
            ]);
        }

        fclose($csv);

        // Create ZIP
        $zip = new ZipArchive();
        if ($zip->open($zipPath, ZipArchive::CREATE | ZipArchive::OVERWRITE) !== true) {
            @unlink($csvPath);
            return response()->json(['success' => false, 'message' => 'Unable to create ZIP file'], 500);
        }

        // Add CSV
        $zip->addFile($csvPath, $baseName . '.csv');

        // Add images
        foreach ($filesToZip as $file) {
            $zip->addFile($file['path'], $file['name']);
        }

        $zip->close();

        // Remove the standalone CSV after adding to ZIP
        @unlink($csvPath);

        return response()->download($zipPath)->deleteFileAfterSend(true);
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
                    return $this->getData($request);
            }
        } catch (\Exception $e) {
            return response()->json([
                'error' => $e->getMessage(),
            ], 500);
        }
    }

    private function editorCreate(Request $request)
    {
        $data        = $request->input('data');
        $artworkData = $data[0]; // DataTables Editor sends data as array

        $artwork                        = new Artwork();
        $artwork->company_id            = user()->company_id;
        $artwork->artwork_collection_id = ArtworkCollection::first()->id; // Use first collection
        $artwork->name                  = $artworkData['name'] ?? '';
        $artwork->artist                = $artworkData['artist'] ?? '';
        $artwork->type                  = $artworkData['type'] ?? '';

        // Handle dimensions
        $originalValue = [
            'height' => $artworkData['height'] ?? '',
            'width'  => $artworkData['width'] ?? '',
            'unit'   => $artworkData['unit'] ?? 'cm',
        ];
        $artwork->original_value = $originalValue;

        // Convert to inches for data column
        if ($originalValue['unit'] === 'cm' && ! empty($originalValue['width']) && ! empty($originalValue['height'])) {
            $artwork->data = [
                'width_inch'  => round($originalValue['width'] / 2.54, 5),
                'height_inch' => round($originalValue['height'] / 2.54, 5),
            ];
        }

        $artwork->save();

        $rowData = [
            'DT_RowId'   => 'row_' . $artwork->id,
            'id'         => $artwork->id,
            'image'      => $artwork->image_url ?? '/images/placeholder.jpg',
            'collection' => $artwork->collection->name ?? '',
            'name'       => $artwork->name,
            'artist'     => $artwork->artist,
            'type'       => $artwork->type,
            'height'     => $originalValue['height'] ?? '',
            'width'      => $originalValue['width'] ?? '',
            'unit'       => $originalValue['unit'] ?? 'cm',
            'created_at' => $artwork->created_at->format('Y-m-d'),
        ];

        // Only include company data for super admin users
        if (user()->isSuperAdmin()) {
            $rowData['company'] = $artwork->company->name ?? '';
        }

        return response()->json([
            'data' => [$rowData],
        ]);
    }

    private function editorEdit(Request $request)
    {
        $data = $request->input('data');

        foreach ($data as $rowId => $rowData) {
            $id      = str_replace('row_', '', $rowId);
            $artwork = Artwork::findOrFail($id);

            $artwork->name   = $rowData['name'] ?? $artwork->name;
            $artwork->artist = $rowData['artist'] ?? $artwork->artist;
            $artwork->type   = $rowData['type'] ?? $artwork->type;

            // Handle dimensions
            if (isset($rowData['height']) || isset($rowData['width']) || isset($rowData['unit'])) {
                $originalValue           = $artwork->original_value ?? [];
                $originalValue['height'] = $rowData['height'] ?? $originalValue['height'] ?? '';
                $originalValue['width']  = $rowData['width'] ?? $originalValue['width'] ?? '';
                $originalValue['unit']   = $rowData['unit'] ?? $originalValue['unit'] ?? 'cm';
                $artwork->original_value = $originalValue;

                // Convert to inches for data column
                if ($originalValue['unit'] === 'cm' && ! empty($originalValue['width']) && ! empty($originalValue['height'])) {
                    $artwork->data = [
                        'width_inch'  => round($originalValue['width'] / 2.54, 5),
                        'height_inch' => round($originalValue['height'] / 2.54, 5),
                    ];
                }
            }

            $artwork->save();
        }

        return response()->json([
            'data' => [],
        ]);
    }

    private function editorRemove(Request $request)
    {
        $data = $request->input('data');

        foreach ($data as $rowId) {
            $id      = str_replace('row_', '', $rowId);
            $artwork = Artwork::findOrFail($id);
            $artwork->delete();
        }

        return response()->json([
            'data' => [],
        ]);
    }

    public function update(Request $request, $id)
    {
        try {
            // Try to find as artwork first, then sculpture
            $model = Artwork::find($id);
            if (!$model) {
                $model = \App\Models\SculptureModel::findOrFail($id);
            }

            $model->name        = $request->input('name', $model->name);
            $model->artist      = $request->input('artist', $model->artist);
            $model->type        = $request->input('type', $model->type);
            $model->description = $request->input('description', $model->description);
            
            // Handle collection update
            if ($request->has('artwork_collection_id')) {
                $model->artwork_collection_id = $request->input('artwork_collection_id');
            }

            // Handle company update
            if ($request->has('company_id')) {
                $model->company_id = $request->input('company_id');
            }

            // Handle dimensions
            if ($request->has('height') || $request->has('width') || $request->has('unit')) {
                $originalValue           = $model->original_value ?? [];
                $originalValue['height'] = $request->input('height', $originalValue['height'] ?? '');
                $originalValue['width']  = $request->input('width', $originalValue['width'] ?? '');
                $originalValue['unit']   = $request->input('unit', $originalValue['unit'] ?? 'cm');
                $model->original_value = $originalValue;

                // Convert string values to float before using round()
                $widthFloat = !empty($originalValue['width']) ? (float) $originalValue['width'] : 0;
                $heightFloat = !empty($originalValue['height']) ? (float) $originalValue['height'] : 0;
                
                $data                = $model->data ?? [];

                $data['width_inch'] = round($widthFloat, 5);
                $data['height_inch'] = round($heightFloat, 5);

                // Convert to inches for data column
                if ($originalValue['unit'] === 'cm' && ! empty($originalValue['width']) && ! empty($originalValue['height'])) {
                    $data['width_inch']  = round($originalValue['width'] / 2.54, 5);
                    $data['height_inch'] = round($originalValue['height'] / 2.54, 5);
                }
                $model->data       = $data;
            }

            $model->save();

            return response()->json([
                'success' => true,
                'message' => 'Item updated successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating item: ' . $e->getMessage(),
            ], 500);
        }
    }

    private function formatDimensions($originalValue, $data)
    {
        $height = $originalValue['height'] ?? '';
        $width  = $originalValue['width'] ?? '';
        $unit   = $originalValue['unit'] ?? 'cm';

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
                'message' => 'Artwork deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error deleting artwork: ' . $e->getMessage(),
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
            (Company::where('name', $request->collection_company_name)->first()?->id ?? user()->company_id) :
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

    public function editCollection(Request $request, $id)
    {
        try {
            $collection = ArtworkCollection::findOrFail($id);

            $request->validate([
                'collection_name'         => 'required|string|max:255',
                'collection_company_name' => 'required|string|max:255',
                'collection_thumbnail'    => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
            ]);

            $collection->name       = $request->collection_name;
            $collection->company_id = user()->isAdmin() ?
            (Company::where('name', $request->collection_company_name)->first()?->id ?? user()->company_id) :
            user()->company_id;

            if ($request->hasFile('collection_thumbnail')) {
                $thumbnail                 = $request->file('collection_thumbnail');
                $path                      = $thumbnail->storeAs('media/collections', $thumbnail->getClientOriginalName(), 'public');
                $collection->thumbnail_url = Storage::url($path);
            }

            $collection->save();

            return response()->json([
                'success'    => true,
                'message'    => 'Collection updated successfully',
                'collection' => [
                    'id' => $collection->id,
                    'name' => $collection->name,
                    'thumbnail_url' => $collection->thumbnail_url,
                    'item_count' => $collection->artworks()->count(),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function deleteCollection($id)
    {
        try {
            $collection = ArtworkCollection::findOrFail($id);

            // Check if collection has artworks
            if ($collection->artworks()->count() > 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot delete collection that contains artworks. Please move or delete artworks first.',
                ], 400);
            }

            $collection->delete();

            return response()->json([
                'success' => true,
                'message' => 'Collection deleted successfully',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 500);
        }
    }

    public function getCollectionsByCompany(Request $request)
    {
        try {
            $companyId = $request->input('company_id');
            
            if (!$companyId) {
                return response()->json([
                    'success' => false,
                    'message' => 'Company ID is required',
                ], 400);
            }

            // For super admin, get collections for the selected company
            if (auth()->user()->isSuperAdmin()) {
                $collections = ArtworkCollection::where('company_id', $companyId)
                    ->latest('name')
                    ->get();
            } else {
                // For non-super admin, only get collections for their own company
                $collections = ArtworkCollection::where('company_id', auth()->user()->company_id)
                    ->latest('name')
                    ->get();
            }

            return response()->json([
                'success' => true,
                'collections' => $collections,
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
            $artwork             = new Artwork();
            $artwork->company_id = user()->company_id;
            $artwork->name       = $request->input('name', '');
            $artwork->artist     = $request->input('artist', '');
            $artwork->type       = $request->input('type', '');

            // Handle dimensions
            if ($request->has('height') || $request->has('width') || $request->has('unit')) {
                $originalValue = [
                    'height' => $request->input('height', ''),
                    'width'  => $request->input('width', ''),
                    'unit'   => $request->input('unit', 'cm'),
                ];
                $artwork->original_value = $originalValue;

                // Convert to inches for data column
                if ($originalValue['unit'] === 'cm' && ! empty($originalValue['width']) && ! empty($originalValue['height'])) {
                    $data = [
                        'width_inch'  => round($originalValue['width'] / 2.54, 5),
                        'height_inch' => round($originalValue['height'] / 2.54, 5),
                    ];
                    $artwork->data = $data;
                }
            }

            $artwork->save();

            $rowData = [
                'DT_RowId'   => 'row_' . $artwork->id,
                'id'         => $artwork->id,
                'image'      => $artwork->image_url ?? '/images/placeholder.jpg',
                'collection' => $artwork->collection->name ?? '',
                'name'       => $artwork->name,
                'artist'     => $artwork->artist,
                'type'       => $artwork->type,
                'height'     => $artwork->original_value['height'] ?? '',
                'width'      => $artwork->original_value['width'] ?? '',
                'unit'       => $artwork->original_value['unit'] ?? 'cm',
                'created_at' => $artwork->created_at->format('Y-m-d'),
            ];

            // Only include company data for super admin users
            if (user()->isSuperAdmin()) {
                $rowData['company'] = $artwork->company->name ?? '';
            }

            return response()->json([
                'success' => true,
                'message' => 'Artwork created successfully',
                'data'    => $rowData,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating artwork: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkStore(Request $request)
    {
        try {
            $items = $request->input('items', []);

            if (empty($items) || ! is_array($items)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items to save.',
                ], 400);
            }

            $savedCount = 0;
            $errors     = [];

            foreach ($items as $index => $itemData) {
                try {
                    // Validate required fields
                    if (empty($itemData['name'])) {
                        $errors[] = "Title is required for item #" . ($index + 1);
                        continue;
                    }

                    if (empty($itemData['artwork_collection_id'])) {
                        $errors[] = "Collection is required for item #" . ($index + 1);
                        continue;
                    }

                    $artwork             = new Artwork();
                    $artwork->company_id = user()->isSuperAdmin() && ! empty($itemData['company_id'])
                        ? $itemData['company_id']
                        : user()->company_id;
                    $artwork->name                  = $itemData['name'];
                    $artwork->artist                = $itemData['artist'] ?? '';
                    $artwork->type                  = $itemData['type'] ?? '';
                    $artwork->artwork_collection_id = $itemData['artwork_collection_id'];

                    // Handle dimensions
                    if (! empty($itemData['height']) || ! empty($itemData['width']) || ! empty($itemData['unit'])) {
                        $originalValue = [
                            'height' => $itemData['height'] ?? '',
                            'width'  => $itemData['width'] ?? '',
                            'unit'   => $itemData['unit'] ?? 'cm',
                        ];
                        $artwork->original_value = $originalValue;

                        // Convert string values to float before using round()
                        $widthFloat = !empty($originalValue['width']) ? (float) $originalValue['width'] : 0;
                        $heightFloat = !empty($originalValue['height']) ? (float) $originalValue['height'] : 0;
                        
                        $data = [
                            'width_inch'  => round($widthFloat, 5),
                            'height_inch' => round($heightFloat, 5),
                            'scale'       => '1',
                        ];

                        // Convert to inches for data column
                        if ($originalValue['unit'] === 'cm' && ! empty($originalValue['width']) && ! empty($originalValue['height'])) {
                            $data = [
                                'width_inch'  => round($widthFloat / 2.54, 5),
                                'height_inch' => round($heightFloat / 2.54, 5),
                                'scale'       => '1',
                            ];
                        }
                        $artwork->data = $data;
                    }

                    $artwork->save();

                    // Handle image upload if present
                    if (!empty($itemData['image']) && str_starts_with($itemData['image'], 'data:image')) {
                        try {
                            // Extract base64 data from data URL
                            $base64Data = $itemData['image'];

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
                            \Log::warning('Failed to save image for artwork ' . $artwork->id . ': ' . $e->getMessage());
                        }
                    }

                    $savedCount++;

                } catch (\Exception $e) {
                    $errors[] = "Error saving item #" . ($index + 1) . ": " . $e->getMessage();
                }
            }

            if ($savedCount > 0) {
                return response()->json([
                    'success' => true,
                    'message' => "Successfully saved {$savedCount} item(s).",
                    'saved_count' => $savedCount,
                    'errors'      => $errors,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => 'No items were saved. ' . implode(' ', $errors),
                ], 400);
            }

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error processing bulk save: ' . $e->getMessage(),
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

            if (! $artworkData || ! is_array($artworkData)) {
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
            $errors       = [];

            foreach ($artworkData as $index => $row) {
                try {
                    // Create artwork object first
                    $artwork             = new Artwork();
                    // $artwork->company_id = user()->company_id;

                    $company = Company::where('name', $row['company_name'])->first();
                    if (! $company) {
                        $errors[] = "Company '{$row['company_name']}' not found for artwork #{$index}.";
                        continue;
                    }
                    $artwork->company_id = $company->id;
                    
                    // Find collection by name
                    $collection = ArtworkCollection::where('name', $row['collection_name'])->first();
                    if (! $collection) {
                        $errors[] = "Collection '{$row['collection_name']}' not found for artwork #{$index}.";
                        continue;
                    }
                    $artwork->artwork_collection_id = $collection->id;

                    $artwork->name          = $row['title'] ?? '';
                    $artwork->artist        = $row['artist'] ?? '';
                    $artwork->type          = $row['type'] ?? '';
                    $artwork->description   = $row['description'] ?? '';
                    $artwork->original_unit = $row['unit'] ?? '';

                    // Set artwork data
                    $artwork->data = [
                        'height_inch' => $row['height'] ?? '',
                        'width_inch'  => $row['width'] ?? '',
                        'scale'       => $row['scale'] ?? '',
                    ];

                    // Set original_value as JSON object with width, height, and unit
                    $artwork->original_value = [
                        'width'  => $row['width'] ?? '',
                        'height' => $row['height'] ?? '',
                        'unit'   => $row['unit'] ?? 'cm',
                    ];

                    // Convert to inches if unit is cm and save to data column
                    if (($row['unit'] ?? 'cm') === 'cm' && ! empty($row['width']) && ! empty($row['height'])) {
                        // Convert cm to inches (1 inch = 2.54 cm)
                        $widthInch  = round($row['width'] / 2.54, 5);
                        $heightInch = round($row['height'] / 2.54, 5);

                        $artwork->data = [
                            'width_inch'  => $widthInch,
                            'height_inch' => $heightInch,
                            'scale'       => $row['scale'] ?? '',
                        ];
                    } else {
                        // If unit is inch or not specified, use values as is
                        $artwork->data = [
                            'width_inch'  => $row['width'] ?? '',
                            'height_inch' => $row['height'] ?? '',
                            'scale'       => $row['scale'] ?? '',
                        ];
                    }

                    // Save artwork first to get the ID
                    $artwork->save();

                    // Handle image if provided
                    if (! empty($row['image']) && str_starts_with($row['image'], 'data:image')) {
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
                            if (! empty($artwork->data->width_inch) && ! empty($artwork->data->height_inch)) {
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
                'created_count' => $createdCount,
            ];

            if (! empty($errors)) {
                $response['errors'] = $errors;
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function bulkDelete(Request $request)
    {
        try {
            // Debug: Log the request data
            \Log::info('Bulk delete request data:', $request->all());
            
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items selected for deletion',
                ], 400);
            }

            $deletedCount = 0;
            $errors = [];
            
            foreach ($ids as $id) {
                try {
                    $artwork = Artwork::find($id);
                    if ($artwork) {
                        $artwork->delete();
                        $deletedCount++;
                    } else {
                        $errors[] = "Artwork with ID {$id} not found";
                    }
                } catch (\Exception $e) {
                    $errors[] = "Error deleting artwork ID {$id}: " . $e->getMessage();
                }
            }

            $response = [
                'success' => true,
                'message' => "Successfully deleted {$deletedCount} item(s)",
                'deleted_count' => $deletedCount,
            ];

            if (!empty($errors)) {
                $response['errors'] = $errors;
            }

            return response()->json($response);

        } catch (\Exception $e) {
            \Log::error('Bulk delete error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error deleting items: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkCopy(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items selected for copying',
                ], 400);
            }

            $copiedCount = 0;
            $errors = [];

            foreach ($ids as $id) {
                try {
                    $originalArtwork = Artwork::find($id);
                    if (!$originalArtwork) {
                        $errors[] = "Artwork with ID {$id} not found";
                        continue;
                    }

                    // Create a copy of the artwork
                    $newArtwork = new Artwork();
                    $newArtwork->company_id = $originalArtwork->company_id;
                    $newArtwork->artwork_collection_id = $originalArtwork->artwork_collection_id;
                    $newArtwork->name = $originalArtwork->name . ' (Copy)';
                    $newArtwork->artist = $originalArtwork->artist;
                    $newArtwork->type = $originalArtwork->type;
                    $newArtwork->description = $originalArtwork->description;
                    $newArtwork->original_value = $originalArtwork->original_value;
                    $newArtwork->data = $originalArtwork->data;
                    $newArtwork->original_unit = $originalArtwork->original_unit;

                    $newArtwork->save();

                    // Copy media files if they exist
                    if ($originalArtwork->hasMedia('image')) {
                        $originalMedia = $originalArtwork->getFirstMedia('image');
                        if ($originalMedia) {
                            // Prefer copying from the filesystem path to avoid URL/download issues
                            $sourcePath = $originalMedia->getPath();
                            if ($sourcePath && file_exists($sourcePath)) {
                                $newArtwork->addMedia($sourcePath)
                                    ->preservingOriginal()
                                    ->usingName($newArtwork->name)
                                    ->toMediaCollection('image');
                            } else {
                                // Fallback: try via URL if local path is unavailable (e.g., remote disks)
                                $sourceUrl = $originalMedia->getUrl();
                                if (!empty($sourceUrl)) {
                                    $newArtwork->addMediaFromUrl($sourceUrl)
                                        ->usingName($newArtwork->name)
                                        ->toMediaCollection('image');
                                }
                            }
                        }
                    }

                    $copiedCount++;

                } catch (\Exception $e) {
                    $errors[] = "Error copying artwork ID {$id}: " . $e->getMessage();
                }
            }

            $response = [
                'success' => true,
                'message' => "Successfully copied {$copiedCount} item(s)",
                'copied_count' => $copiedCount,
            ];

            if (!empty($errors)) {
                $response['errors'] = $errors;
            }

            return response()->json($response);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error copying items: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function bulkUpdate(Request $request)
    {
        try {
            $ids = $request->input('ids', []);

            if (empty($ids)) {
                return response()->json([
                    'success' => false,
                    'message' => 'No items selected for update',
                ], 400);
            }

            $updateData   = [];
            $updatedCount = 0;

            // Prepare update data based on what fields are provided
            if ($request->has('name') && ! empty($request->input('name'))) {
                $updateData['name'] = $request->input('name');
            }

            if ($request->has('artist') && ! empty($request->input('artist'))) {
                $updateData['artist'] = $request->input('artist');
            }

            if ($request->has('type') && ! empty($request->input('type'))) {
                $updateData['type'] = $request->input('type');
            }

            if ($request->has('description') && ! empty($request->input('description'))) {
                $updateData['description'] = $request->input('description');
            }

            if ($request->has('collection') && ! empty($request->input('collection'))) {
                $updateData['artwork_collection_id'] = $request->input('collection');
            }

            // Handle dimensions update
            $dimensionsUpdate = [];
            if ($request->has('height') && ! empty($request->input('height'))) {
                $dimensionsUpdate['height'] = $request->input('height');
            }
            if ($request->has('width') && ! empty($request->input('width'))) {
                $dimensionsUpdate['width'] = $request->input('width');
            }
            if ($request->has('unit') && ! empty($request->input('unit'))) {
                $dimensionsUpdate['unit'] = $request->input('unit');
            }

            // Update artworks
            foreach ($ids as $id) {
                $artwork = Artwork::find($id);
                if (! $artwork) {
                    continue;
                }

                // Update basic fields
                if (! empty($updateData)) {
                    $artwork->update($updateData);
                }

                // Update dimensions if provided
                if (! empty($dimensionsUpdate)) {
                    $originalValue = $artwork->original_value ?? [];

                    // Update dimensions
                    if (isset($dimensionsUpdate['height'])) {
                        $originalValue['height'] = $dimensionsUpdate['height'];
                    }
                    if (isset($dimensionsUpdate['width'])) {
                        $originalValue['width'] = $dimensionsUpdate['width'];
                    }
                    if (isset($dimensionsUpdate['unit'])) {
                        $originalValue['unit'] = $dimensionsUpdate['unit'];
                    }

                    $artwork->original_value = $originalValue;

                    $data = $artwork->data ?? [];

                    $data['width_inch'] = round($originalValue['width'], 5);
                    $data['height_inch'] = round($originalValue['height'], 5);

                    // Convert to inches for data column if unit is cm
                    if (isset($originalValue['unit']) && $originalValue['unit'] === 'cm' &&
                        ! empty($originalValue['width']) && ! empty($originalValue['height'])) {
                        $data['width_inch'] = round($originalValue['width'] / 2.54, 5);
                        $data['height_inch'] = round($originalValue['height'] / 2.54, 5);
                    }

                    $artwork->data = $data;

                    $artwork->save();
                }

                $updatedCount++;
            }

            return response()->json([
                'success' => true,
                'message' => "Successfully updated {$updatedCount} item(s)",
                'updated_count' => $updatedCount,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error updating items: ' . $e->getMessage(),
            ], 500);
        }
    }
}
