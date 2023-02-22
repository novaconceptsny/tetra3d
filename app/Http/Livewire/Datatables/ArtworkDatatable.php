<?php

namespace App\Http\Livewire\Datatables;

use App\Models\Artwork;
use App\Models\ArtworkCollection;
use App\Models\Company;

class ArtworkDatatable extends BaseDatatable
{
    public $model = Artwork::class;
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
        if ($this->projectId){
            $this->selectedCollection = \DB::table('artwork_collection_project')
                ->where('project_id', $this->projectId)
                ->value('artwork_collection_id');
        }
    }

    public function dehydrate(){
        $this->dispatchBrowserEvent('contentChanged');
    }

    public function render()
    {
        $data = array();

        $data['heading'] = __('Artworks');

        $data['collections'] = ArtworkCollection::latest('name')->get();
        $data['companies'] = Company::latest('name')->get();
        $data['artists'] = Artwork::groupBy('artist')->pluck('artist');

        $rows = $this->model::query()
            ->with('collection', 'company', 'media')
            ->when(
                $this->selectedCollection,
                fn($query) => $query->where(
                    'artwork_collection_id', $this->selectedCollection
                )
            )
            ->when(
                $this->selectedCompany,
                fn($query) => $query->where(
                    'company_id', $this->selectedCompany
                )
            )
            ->when(
                $this->selectedArtist,
                fn($query) => $query->where(
                    'artist', $this->selectedArtist
                )
            )
            ->whereAnyColumnLike($this->search)
            ->sort($this->sortBy, $this->sortOrder)
            ->paginate($this->perPage);

        $rows->getCollection()->transform(function ($row){
            $row->company_name = $row->company->name;
            $row->collection_name = $row->collection?->name;
            return $row;
        });

        $data['rows'] = $rows;
        $data['label'] = 'artwork';

        $view = $this->frontend ? "livewire.datatables.artwork-frontend" : "livewire.datatables.artwork";
        return view($view, $data);
    }

    public function resetFilters()
    {
        $this->reset([
            'perPage', 'selectedCollection',
            'search', 'selectedCompany', 'selectedArtist',
            'sortBy', 'sortOrder'
        ]);

    }

    public function updateCollection()
    {
        if(!$this->targetCollection || !$this->selectedRows){
            return;
        }

        $this->model::whereIn('id', $this->selectedRows)->update([
            'artwork_collection_id' => $this->targetCollection
        ]);

        $this->emit('flashNotification', 'Collection updated');
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
                'name' => 'Dimensions (h" x w")',
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
        ];

        if (!user()->isAdmin()){
            unset($columns['company_name']);
        }

        return $columns;
    }

    public function getRoutes()
    {
        $routes =  [
            'create' => 'backend.artworks.create',
            'edit' => 'backend.artworks.edit',
            'delete' => 'backend.artworks.destroy',
        ];

        if (user()->cannot('create', Artwork::class)) {
            unset($routes['create']);
        }

        return $routes;
    }
}
