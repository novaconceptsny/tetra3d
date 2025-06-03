<?php
namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\CompanyTour;
use App\Models\Tour;
use Illuminate\Http\Request;

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

        // Optionally, you can remove the old $galleryIsBelongToCompany if not needed
        // $galleryIsBelongToCompany = CompanyTour::where('tour_id', $templateTours[0]->id)->get();

        return view('resource.index', compact('companies', 'templateTours'));
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
                'company_names' => 'array',
                'tour_id'       => 'required|exists:tours,id',
            ]);

            CompanyTour::where('tour_id', $request->tour_id)->delete();

            foreach ($request->company_names as $companyName) {
                // Find the company by name
                $company = Company::where('name', $companyName)->first();

                if (! $company) {
                    // Optionally, you can skip or return an error
                    continue;
                }

                CompanyTour::create([
                    'company_id' => $company->id,
                    'tour_id'    => $request->tour_id,
                ]);
            }

            $templateTours = $this->getTemplateTours();

            return response()->json(['success' => true, 'templateTours' => $templateTours]);
        } catch (\Exception $e) {
            \Log::error('assignTourToCompanies error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
