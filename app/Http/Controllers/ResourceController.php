<?php
namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyTour;
use App\Models\ProjectTour;
use App\Models\Tour;
use App\Models\TutorialVideo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class ResourceController extends Controller
{
    public function index()
    {
        $user = auth()->user();

        if ($user && method_exists($user, 'isSuperAdmin') && $user->isSuperAdmin()) {
            // Super admin: get all companies
            $companies = Company::all();
        } else {
            // Not super admin: get only the user's company
            $companies = Company::where('id', $user->company_id)->get();
        }

        $templateTours = $this->getTemplateTours();
        $tutorialVideos = TutorialVideo::orderBy('order')->get();

        // Optionally, you can remove the old $galleryIsBelongToCompany if not needed
        // $galleryIsBelongToCompany = CompanyTour::where('tour_id', $templateTours[0]->id)->get();

        return view('resource.index', compact('companies', 'templateTours', 'tutorialVideos'));
    }

    /**
     * Get template tours with ownership information for the current user
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTemplateTours()
    {
        $user = auth()->user();
        $templateTours = Tour::withoutGlobalScope('forCurrentCompany')
            ->where('name', 'like', '%Template Gallery%')
            ->get();

        // Add isOwn property for each templateTour
        foreach ($templateTours as $gallery) {
            // Check ownership in both CompanyTour and Tour tables
            $gallery->isOwn = CompanyTour::where('tour_id', $gallery->id)
                ->where('company_id', $user->company_id)
                ->exists() || 
                Tour::where('id', $gallery->id)
                    ->where('company_id', $user->company_id)
                    ->exists();

            // Get assigned companies from both CompanyTour and Tour tables
            $sharedCompanyIds = CompanyTour::where('tour_id', $gallery->id)
                ->pluck('company_id')
                ->toArray();
            
            $mainCompanyIds = Tour::where('id', $gallery->id)
                ->pluck('company_id')
                ->toArray();

            $gallery->mainCompanyIds = $mainCompanyIds;

            if (!empty($mainCompanyIds)) {
                // Get the first main company ID
                $firstMainCompanyId = $mainCompanyIds[0];
                
                // Remove the first main company ID from arrays to avoid duplicates
                $mainCompanyIds = array_slice($mainCompanyIds, 1);
                $sharedCompanyIds = array_diff($sharedCompanyIds, [$firstMainCompanyId]);
                
                // Merge arrays with the first main company ID at the beginning
                $gallery->assigned_company_ids = array_unique(
                    array_merge(
                        [$firstMainCompanyId],
                        $mainCompanyIds,
                        $sharedCompanyIds
                    )
                );
            } else {
                // If no main company IDs, just merge as before
                $gallery->assigned_company_ids = array_unique(
                    array_merge($sharedCompanyIds, $mainCompanyIds)
                );
            }
        }

        return $templateTours;
    }

    public function assignTourToCompanies(Request $request)
    {
        try {
            $request->validate([
                'company_ids'   => 'array',
                'tour_id'       => 'required|exists:tours,id',
            ]);

            $user = auth()->user();
            if($user->isSuperAdmin()) {
                CompanyTour::where('tour_id', $request->tour_id)->delete();
            } else {
                CompanyTour::where('tour_id', $request->tour_id)->where('company_id', $user->company_id)->delete();
            }

            foreach ($request->company_ids as $companyId) {
                // Find the company by id
                $company = Company::where('id', $companyId)->first();

                CompanyTour::create([
                    'company_id' => $company->id,
                    'tour_id'    => $request->tour_id,
                ]);
            }

            // Remove projects that have the tour_id but don't belong to the company_ids
            ProjectTour::where('tour_id', $request->tour_id)
                ->whereNotIn('project_id', function($query) use ($request) {
                    $query->select('id')
                        ->from('projects')
                        ->whereIn('company_id', $request->company_ids);
                })
                ->delete();

            $templateTours = $this->getTemplateTours();

            return response()->json(['success' => true, 'templateTours' => $templateTours]);
        } catch (\Exception $e) {
            \Log::error('assignTourToCompanies error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function removeGallery(Request $request)
    {
        try {
            $request->validate([
                'tour_id' => 'required|exists:tours,id'
            ]);

            $user = auth()->user();

            // Delete related records from company_tours table
            CompanyTour::where('tour_id', $request->tour_id)->delete();

            ProjectTour::where('tour_id', $request->tour_id)
            ->whereIn('project_id', function($query) use ($user) {
                $query->select('id')
                    ->from('projects')
                    ->where('company_id', $user->company_id);
            })
            ->delete();

            // Get updated template tours
            $templateTours = $this->getTemplateTours();

            return response()->json([
                'success' => true,
                'templateTours' => $templateTours,
                'message' => 'Gallery removed successfully'
            ]);

        } catch (\Exception $e) {
            \Log::error('removeGallery error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Upload a tutorial video (super admin only)
     */
    public function uploadVideo(Request $request)
    {
        try {
            $user = auth()->user();
            
            if (!$user || !$user->isSuperAdmin()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized. Only super admins can upload videos.'
                ], 403);
            }

            $request->validate([
                'video' => 'required|file|mimes:mp4,webm,ogg,mov,avi|max:102400', // 100MB max
                'title' => 'required|string|max:255',
            ]);

            // Store video
            $videoPath = $request->file('video')->store('tutorial-videos', 'public');
            $videoUrl = '/storage/' . $videoPath;

            // Get the highest order value
            $maxOrder = TutorialVideo::max('order') ?? 0;

            // Create video record
            $video = TutorialVideo::create([
                'title' => $request->title,
                'video_path' => $videoUrl,
                'order' => $maxOrder + 1,
            ]);

            $tutorialVideos = TutorialVideo::orderBy('order')->get();

            return response()->json([
                'success' => true,
                'message' => 'Video uploaded successfully',
                'video' => $video,
                'videos' => $tutorialVideos
            ]);

        } catch (\Exception $e) {
            \Log::error('uploadVideo error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete a tutorial video (super admin only)
     */
    public function deleteVideo(Request $request, $id)
    {
        try {
            $user = auth()->user();
            
            if (!$user || !$user->isSuperAdmin()) {
                return response()->json([
                    'success' => false,
                    'error' => 'Unauthorized. Only super admins can delete videos.'
                ], 403);
            }

            $video = TutorialVideo::findOrFail($id);

            // Delete video file
            $videoFilePath = str_replace('/storage/', '', $video->video_path);
            if (Storage::disk('public')->exists($videoFilePath)) {
                Storage::disk('public')->delete($videoFilePath);
            }

            // Delete database record
            $video->delete();

            $tutorialVideos = TutorialVideo::orderBy('order')->get();

            return response()->json([
                'success' => true,
                'message' => 'Video deleted successfully',
                'videos' => $tutorialVideos
            ]);

        } catch (\Exception $e) {
            \Log::error('deleteVideo error: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
