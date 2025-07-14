<?php

namespace App\Http\Controllers;

use App\Models\Artwork;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function index()
    {
        $inventory = Artwork::paginate(25);
        return view('inventory.index', compact('inventory'));
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
