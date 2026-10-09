<?php

namespace App\Http\Controllers;

use App\Helpers\ValidationRules;
use App\Models\ArtworkCollection;
use App\Models\Company;
use App\Models\SculptureModel;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * Sculpture management from the Inventory page (front-end).
 *
 * - Company admins: create / edit / delete sculptures of their own company only.
 * - Super admins: same pages, any company.
 * The backend (/backend/sculptures) stays super-admin only (SculptureModelPolicy).
 *
 * Access is controlled by the "manage-inventory-sculptures" gate on the route group (routes/web.php).
 * SculptureModel uses the HasCompany trait, so for non-super-admins every query (including route
 * model binding) is already limited to the user's company; ensureCanManage() double-checks it.
 */
class InventorySculptureController extends Controller
{
    public function index(Request $request)
    {
        $collectionId = $request->get('collection_id');

        $sculptures = SculptureModel::query()
            ->with(['collection', 'company', 'media'])
            ->when($collectionId, fn ($query) => $query->where('artwork_collection_id', $collectionId))
            ->latest('updated_at')
            ->get();

        return view('inventory.sculptures.index', [
            'sculptures'   => $sculptures,
            'collections'  => $this->collectionsForUser(),
            'collectionId' => $collectionId,
            'showCompany'  => user()->isSuperAdmin(),
        ]);
    }

    public function create()
    {
        return view('backend.sculpture.form', $this->formData(null) + [
            'route'  => route('inventory.sculptures.store'),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request)
    {
        $request->validate($this->rules());

        $companyId = $this->companyIdFor($request);
        $this->ensureCollectionBelongsToCompany($request->input('artwork_collection_id'), $companyId);

        $sculpture = SculptureModel::create(array_merge(
            $request->only(['name', 'artist', 'type', 'data', 'artwork_collection_id']),
            ['company_id' => $companyId]
        ));

        $sculpture->addFromMediaLibraryRequest($request->sculpture)->toMediaCollection('sculpture');
        $sculpture->addFromMediaLibraryRequest($request->interaction)->toMediaCollection('interaction');
        $sculpture->addFromMediaLibraryRequest($request->thumbnail)->toMediaCollection('thumbnail');

        return redirect()->route('inventory.sculptures.index')
            ->with('success', 'Sculpture created successfully');
    }

    public function edit(SculptureModel $sculpture)
    {
        $this->ensureCanManage($sculpture);

        return view('backend.sculpture.form', $this->formData($sculpture) + [
            'route'  => route('inventory.sculptures.update', $sculpture),
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, SculptureModel $sculpture)
    {
        $this->ensureCanManage($sculpture);

        $request->validate(ValidationRules::updateSculpture() + [
            'artwork_collection_id' => 'required',
        ]);

        $companyId = $this->companyIdFor($request, $sculpture);
        $this->ensureCollectionBelongsToCompany($request->input('artwork_collection_id'), $companyId);

        $sculpture->update(array_merge(
            $request->only(['name', 'artist', 'type', 'data', 'artwork_collection_id']),
            ['company_id' => $companyId]
        ));

        foreach (['sculpture', 'interaction', 'thumbnail'] as $collection) {
            if ($request->filled($collection)) {
                $sculpture->addFromMediaLibraryRequest($request->input($collection))->toMediaCollection($collection);
            }
        }

        return redirect()->route('inventory.sculptures.index')
            ->with('success', 'Sculpture updated successfully');
    }

    public function destroy(SculptureModel $sculpture)
    {
        $this->ensureCanManage($sculpture);

        $sculpture->delete();

        return redirect()->route('inventory.sculptures.index')
            ->with('success', 'Sculpture deleted successfully');
    }

    // ------------------------------------------------------------------

    private function rules(): array
    {
        return ValidationRules::storeSculpture() + [
            'artwork_collection_id' => 'required',
        ];
    }

    /**
     * Data for the shared sculpture form (resources/views/backend/sculpture/form.blade.php),
     * rendered with the front-end layout.
     */
    private function formData(?SculptureModel $sculpture): array
    {
        $isSuperAdmin = user()->isSuperAdmin();

        return [
            'sculpture'           => $sculpture,
            'layout'              => 'layouts.redesign',
            'backUrl'             => route('inventory.sculptures.index'),
            // Company admins cannot pick a company: the sculpture always belongs to their own company
            'lockCompany'         => ! $isSuperAdmin,
            'companies'           => $isSuperAdmin ? Company::all() : Company::whereKey(user()->company_id)->get(),
            'artwork_collections' => $this->collectionsForUser(),
        ];
    }

    private function collectionsForUser()
    {
        return user()->isSuperAdmin()
            ? ArtworkCollection::orderBy('name')->get()
            : ArtworkCollection::where('company_id', user()->company_id)->orderBy('name')->get();
    }

    private function companyIdFor(Request $request, ?SculptureModel $sculpture = null)
    {
        if (user()->isSuperAdmin()) {
            $request->validate(['company_id' => 'required']);

            return $request->input('company_id');
        }

        return user()->company_id;
    }

    private function ensureCollectionBelongsToCompany($collectionId, $companyId): void
    {
        $exists = ArtworkCollection::withoutGlobalScope('forCurrentCompany')
            ->whereKey($collectionId)
            ->where('company_id', $companyId)
            ->exists();

        if (! $exists) {
            throw ValidationException::withMessages([
                'artwork_collection_id' => 'Please choose a collection of this company.',
            ]);
        }
    }

    private function ensureCanManage(SculptureModel $sculpture): void
    {
        abort_unless(
            user()->isSuperAdmin() || (int) $sculpture->company_id === (int) user()->company_id,
            403
        );
    }
}
