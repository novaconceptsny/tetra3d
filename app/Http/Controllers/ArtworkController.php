<?php

namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;

class ArtworkController extends Controller
{
    public function getArtworks(Request $request, Project $project)
    {
        $search = $request->get('search', '');
        $collectionId = $request->get('collection_id', '');
        $page = $request->get('page', 1);
        $perPage = 25;

        $query = $project->artworks();

        if ($search) {
            $query->whereAnyColumnLike($search, ['artist', 'name']);
        }

        if ($collectionId) {
            $query->where('artworks.artwork_collection_id', $collectionId);
        }

        $artworks = $query->with('media')->paginate($perPage, ['*'], 'page', $page);
        $collections = $project->artworkCollections;
        $projectUnit = $project->unit ?? 'imperial';

        // Transform artworks to include converted dimensions
        $artworksData = $artworks->items();
        foreach ($artworksData as $artwork) {
            $artwork->converted_dimensions = $artwork->getConvertedDimensions($projectUnit);
        }

        return response()->json([
            'artworks' => $artworksData,
            'pagination' => [
                'current_page' => $artworks->currentPage(),
                'last_page' => $artworks->lastPage(),
                'per_page' => $artworks->perPage(),
                'total' => $artworks->total(),
                'from' => $artworks->firstItem(),
                'to' => $artworks->lastItem(),
                'has_more_pages' => $artworks->hasMorePages(),
            ],
            'collections' => $collections,
            'project_unit' => $projectUnit
        ]);
    }
}
