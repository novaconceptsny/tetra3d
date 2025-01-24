<?php

namespace App\Livewire\Datatables;

use App\Models\Artwork;
use App\Models\ArtworkCollection;
use App\Models\Company;
use App\Models\SculptureModel;

class InventoryDatatable extends BaseDatatable
{
    public $model = Artwork::class;
    public $sculptureModel = SculptureModel::class;
    public $selectedCollection = '';
    public $selectedCompany = '';
    public $selectedArtist = '';
    public $bulkDeleteEnabled = true;
    public $targetCollection = '';
    public $projectId = null;
    public $frontend = false;

    public function mount()
    {
        $this->columns = $this->getColumns();
        $this->routes = $this->getRoutes();
        $this->projectId = request('project_id');
        $this->selectedCollection = request('collection_id');
        // Set default sorting by created_at in descending order
        $this->sortBy = 'created_at';
        $this->sortOrder = 'desc';
        
        if ($this->projectId) {
            $this->selectedCollection = \DB::table('artwork_collection_project')
                ->where('project_id', $this->projectId)
                ->value('artwork_collection_id');
        }
    }

    public function dehydrate()
    {
        $this->dispatch('contentChanged');
    }

    public function render()
    {
        $data = array();

        $data['heading'] = __('Inventory');

        $data['collections'] = ArtworkCollection::latest('name')->get();
        $data['companies'] = Company::latest('name')->get();
        $data['artists'] = Artwork::groupBy('artist')->pluck('artist');

        $artworkQuery = $this->model::query()
            ->select([
                'id',
                'artwork_collection_id',
                'company_id',
                'name',
                'data',
                'artist',
                'type',
                'created_at',
                \DB::raw("'artwork' as model_type")
            ])
            ->with('collection', 'company', 'media')
            ->when(
                $this->selectedCollection,
                fn($query) => $query->where(
                    'artwork_collection_id',
                    $this->selectedCollection
                )
            )
            ->when(
                $this->selectedCompany,
                fn($query) => $query->where(
                    'company_id',
                    $this->selectedCompany
                )
            )
            ->when(
                $this->selectedArtist,
                fn($query) => $query->where(
                    'artist',
                    $this->selectedArtist
                )
            );

        $sculptureQuery = $this->sculptureModel::query()
            ->select([
                'id',
                'artwork_collection_id',
                'company_id',
                'name',
                'data',
                'artist',
                'type',
                'created_at',
                \DB::raw("'sculpture' as model_type")
            ])
            ->with('collection', 'company', 'media')
            ->when(
                $this->selectedCollection,
                fn($query) => $query->where('artwork_collection_id', $this->selectedCollection)
            )
            ->when(
                $this->selectedCompany,
                fn($query) => $query->where('company_id', $this->selectedCompany)
            )
            ->when(
                $this->selectedArtist,
                fn($query) => $query->where('artist', $this->selectedArtist)
            );

        $artworkRows = $artworkQuery
            ->whereAnyColumnLike($this->search)
            ->paginate($this->perPage);

        $sculptureRows = $sculptureQuery
            ->whereAnyColumnLike($this->search)
            ->paginate($this->perPage);

        $sculptureRows->getCollection()->transform(function ($row) {
            $row->company_name = $row->company->name;
            $row->collection_name = $row->collection?->name;
            $row->route_prefix = 'sculpture';
            return $row;
        });

        $artworkRows->getCollection()->transform(function ($row) {
            $row->company_name = $row->company->name;
            $row->collection_name = $row->collection?->name;
            $row->route_prefix = 'artwork';
            return $row;
        });

        $mergedCollection = $artworkRows->merge($sculptureRows);
        
        // Sort the merged collection by date
        if ($this->sortBy === 'created_at') {
            $mergedCollection = $mergedCollection->sortBy([
                ['created_at', $this->sortOrder === 'desc' ? 'desc' : 'asc']
            ]);
        }

        $page = request()->get('page', 1);
        $perPage = $this->perPage;
        $items = $mergedCollection->forPage($page, $perPage);

        $data['rows'] = new \Illuminate\Pagination\LengthAwarePaginator(
            $items,
            $mergedCollection->count(),
            $perPage,
            $page,
            ['path' => request()->url()]
        );
        $data['label'] = 'artwork';

        $view = "livewire.datatables.inventory";
        return view($view, $data);
    }

    public function resetFilters()
    {
        $this->reset([
            'perPage',
            'selectedCollection',
            'search',
            'selectedCompany',
            'selectedArtist',
            'sortBy',
            'sortOrder'
        ]);

    }

    public function updateCollection()
    {
        if (!$this->targetCollection || !$this->selectedRows) {
            return;
        }

        $this->model::whereIn('id', $this->selectedRows)->update([
            'artwork_collection_id' => $this->targetCollection
        ]);

        $this->dispatch('flashNotification', message: 'Collection updated');
    }

    public function getColumns()
    {
        $columns = [
            'img' => [
                'name' => 'Image',
                'visible' => true,
                'render' => false,
                'td-classes' => 'artwork-img'
            ],
            'company_name' => [
                'name' => 'Company',
                'visible' => true,
                'th-classes' => 'w-10'
            ],
            'collection_name' => [
                'name' => 'Collection',
                'visible' => true,
                'th-classes' => 'w-10'
            ],
            'name' => [
                'name' => 'Name',
                'visible' => true,
                'sortable' => true,
                'th-classes' => 'w-40'
            ],
            'dimensions' => [
                'name' => 'h" x w" x d"',
                'visible' => true,
            ],
            'artist' => [
                'name' => 'Artist',
                'visible' => true,
                'sortable' => true,
                'th-classes' => 'w-10'
            ],
            'type' => [
                'name' => 'Type',
                'visible' => true,
                'sortable' => true,
            ],
            'created_at' => [
                'name' => 'Date Created',
                'visible' => true,
                'sortable' => true,
            ],
        ];

        if (!user()->isAdmin()) {
            unset($columns['company_name']);
        }

        return $columns;
    }

    public function getRoutes()
    {
        $routes = [
            'create-artwork' => 'backend.artworks.create',
            'edit-artwork' => 'backend.artworks.edit',
            'delete-artwork' => 'backend.artworks.destroy',
            'create-sculpture' => 'backend.sculptures.create',
            'edit-sculpture' => 'backend.sculptures.edit',
            'delete-sculpture' => 'backend.sculptures.destroy',
        ];

        if (user()->cannot('create', Artwork::class)) {
            unset($routes['create']);
        }

        return $routes;
    }
}
