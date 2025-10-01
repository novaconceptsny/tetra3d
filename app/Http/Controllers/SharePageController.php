<?php

namespace App\Http\Controllers;

use App\Models\Layout;
use App\Models\Project;
use App\Models\SharedLayout;
use App\Models\SharedTour;
use Illuminate\Http\Request;

class SharePageController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            $projects = Project::all();

            // Fetch all shared layouts with their related layouts (super admin sees all)
            $sharedLayouts = SharedLayout::with('layout')->orderBy('updated_at', 'desc')->get();
        
        } else {
            $projects = Project::where('company_id', $user->company->id)->get();

            // Fetch shared layouts filtered by user's company
            $sharedLayouts = SharedLayout::with(['layout.project.company'])
                ->whereHas('layout.project', function($query) use ($user) {
                    $query->where('company_id', $user->company->id);
                })
                ->orderBy('updated_at', 'desc')
                ->get();
        }

        // Fetch all layouts and format for JavaScript
        $layouts = Layout::all()->map(function($layout) {
            $layout->thumbnail_url = $layout->assignedTour() ? $layout->assignedTour()->getFirstMediaUrl('thumbnail') : '';
            // Add tour and project information
            $layout->tour_name = $layout->assignedTour() ? $layout->assignedTour()->name : 'N/A';
            $layout->project_name = $layout->project ? $layout->project->name : 'N/A';
            return $layout;
        });

        // Handle pre-fill data from URL parameters
        $prefillData = null;
        if ($request->has('layout_id')) {
            $layout = Layout::with('project')->find($request->layout_id);
            if ($layout) {
                $assignedTour = $layout->assignedTour(); // This is a method call, not a relationship
                $prefillData = [
                    'layout_id' => $layout->id,
                    'layout_name' => $layout->name,
                    'tour_id' => $assignedTour ? $assignedTour->id : null,
                    'tour_name' => $assignedTour ? $assignedTour->name : 'N/A',
                    'project_id' => $layout->project ? $layout->project->id : null,
                    'project_name' => $layout->project ? $layout->project->name : 'N/A',
                    'thumbnail_url' => $assignedTour ? $assignedTour->getFirstMediaUrl('thumbnail') : '',
                ];
            }
        }

        return view('share.index', compact('sharedLayouts', 'layouts', 'projects', 'prefillData'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'layout_id' => 'required|exists:layouts,id',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail_url' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        // Create shared layout first to get the ID
        $sharedLayout = SharedLayout::create([
            'layout_id' => $request->layout_id,
            'title' => $request->title,
            'description' => $request->description,
            'thumbnail_url' => null, // Will be updated after file upload
            'active' => true, // default value
        ]);

        $thumbnailUrl = $request->thumbnail_url;
        
        // Handle file upload
        if ($request->hasFile('thumbnail')) {
            $thumbnailUrl = $this->saveUploadedImage($request->file('thumbnail'), $sharedLayout->id, $request->layout_id);
        }
        // Handle base64 image data
        elseif ($thumbnailUrl && str_starts_with($thumbnailUrl, 'data:image/')) {
            $thumbnailUrl = $this->saveBase64Image($thumbnailUrl, $sharedLayout->id, $request->layout_id);
        }
        // If no thumbnail is provided, set to null
        else {
            $thumbnailUrl = null;
        }

        // Update the shared layout with the thumbnail URL
        $sharedLayout->update(['thumbnail_url' => $thumbnailUrl]);

        return response()->json([
            'success' => true,
            'message' => 'Shared layout created successfully!',
            'data' => $sharedLayout->load('layout')
        ]);
    }

    /**
     * Save base64 image data to file
     */
    private function saveBase64Image($base64Data, $sharedLayoutId, $layoutId)
    {
        try {
            // Create directory if it doesn't exist
            $directory = "storage/media/shared_layouts/{$sharedLayoutId}/{$layoutId}";
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Extract image data from base64
            $imageData = base64_decode(explode(',', $base64Data)[1]);
            
            // Use "thumbnail" as filename and replace if exists
            $filename = 'thumbnail.png';
            $filePath = $directory . '/' . $filename;
            
            // Save the file (this will replace existing file)
            file_put_contents($filePath, $imageData);
            
            // Return the public URL
            return '/storage/media/shared_layouts/' . $sharedLayoutId . '/' . $layoutId . '/' . $filename;
            
        } catch (\Exception $e) {
            \Log::error('Error saving base64 image: ' . $e->getMessage());
            return null;
        }
    }

    public function toggle(Request $request, $id)
    {
        $sharedLayout = SharedLayout::findOrFail($id);
        $newStatus = !$sharedLayout->active; // Toggle the status
        $sharedLayout->update(['active' => $newStatus]);

        $action = $newStatus ? 'enabled' : 'disabled';

        return response()->json([
            'success' => true,
            'message' => "Shared layout {$action} successfully!"
        ]);
    }

    public function edit(Request $request, $id)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'thumbnail_url' => 'nullable|string',
            'thumbnail' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:5120',
        ]);

        $sharedLayout = SharedLayout::findOrFail($id);
        
        $thumbnailUrl = $request->thumbnail_url;
        
        // Handle file upload
        if ($request->hasFile('thumbnail')) {
            $thumbnailUrl = $this->saveUploadedImage($request->file('thumbnail'), $sharedLayout->id, $sharedLayout->layout_id);
        }
        // Handle base64 image data
        elseif ($thumbnailUrl && str_starts_with($thumbnailUrl, 'data:image/')) {
            $thumbnailUrl = $this->saveBase64Image($thumbnailUrl, $sharedLayout->id, $sharedLayout->layout_id);
        }
        // If no new thumbnail is provided, keep the existing one
        else {
            $thumbnailUrl = $sharedLayout->thumbnail_url;
        }

        $sharedLayout->update([
            'title' => $request->title,
            'description' => $request->description,
            'thumbnail_url' => $thumbnailUrl,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Shared layout updated successfully!',
            'data' => $sharedLayout->load('layout')
        ]);
    }

    public function destroy($id)
    {
        $sharedLayout = SharedLayout::findOrFail($id);
        
        // Delete related SharedTour records based on layout_id
        SharedTour::where('shared_layout_id', $id)->delete();
        
        // Delete the storage folder for this shared layout
        $storagePath = "storage/media/shared_layouts/{$sharedLayout->id}";
        if (file_exists($storagePath)) {
            \File::deleteDirectory($storagePath);
        }
        
        $sharedLayout->delete();

        return response()->json([
            'success' => true,
            'message' => 'Shared layout deleted successfully!',
        ]);
    }

    /**
     * Save uploaded image file
     */
    private function saveUploadedImage($file, $sharedLayoutId, $layoutId)
    {
        try {
            // Create directory if it doesn't exist
            $directory = "storage/media/shared_layouts/{$sharedLayoutId}/{$layoutId}";
            if (!file_exists($directory)) {
                mkdir($directory, 0755, true);
            }

            // Get file extension
            $extension = $file->getClientOriginalExtension();
            
            // Use "thumbnail" as filename with original extension
            $filename = 'thumbnail.' . $extension;
            $filePath = $directory . '/' . $filename;
            
            // Remove existing thumbnail file if it exists
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            
            // Save the file
            $file->move($directory, $filename);
            
            // Return the public URL
            return '/storage/media/shared_layouts/' . $sharedLayoutId . '/' . $layoutId . '/' . $filename;
            
        } catch (\Exception $e) {
            \Log::error('Error saving uploaded image: ' . $e->getMessage());
            return null;
        }
    }
}
