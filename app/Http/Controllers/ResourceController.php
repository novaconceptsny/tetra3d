<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Company;
use App\Models\Tour;
use App\Models\CompanyTour;
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

        $templateTours = Tour::where('name', 'like', '%Template Gallery%')->get();

        // Add isOwn property for each templateTour
        foreach ($templateTours as $gallery) {
            $gallery->isOwn = CompanyTour::where('tour_id', $gallery->id)
                ->where('company_id', $user->company_id)
                ->exists();
        }

        // Optionally, you can remove the old $galleryIsBelongToCompany if not needed
        // $galleryIsBelongToCompany = CompanyTour::where('tour_id', $templateTours[0]->id)->get();

        return view('resource.index', compact('companies', 'templateTours'));
    }

    public function assignTourToCompanies(Request $request)
    {
        try {
            $request->validate([
                'company_names' => 'required|array',
                'tour_id' => 'required|exists:tours,id',
            ]);

            foreach ($request->company_names as $companyName) {
                // Find the company by name
                $company = Company::where('name', $companyName)->first();

                if (!$company) {
                    // Optionally, you can skip or return an error
                    continue;
                }

                // Check if the relation already exists to avoid duplicates
                $exists = CompanyTour::where('company_id', $company->id)
                    ->where('tour_id', $request->tour_id)
                    ->exists();

                if (!$exists) {
                    CompanyTour::create([
                        'company_id' => $company->id,
                        'tour_id' => $request->tour_id,
                    ]);
                }
            }

            return response()->json(['success' => true]);
        } catch (\Exception $e) {
            \Log::error('assignTourToCompanies error: ' . $e->getMessage());
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}
