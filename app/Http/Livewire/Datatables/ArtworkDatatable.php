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

    public function mount()
    {
        $this->columns = $this->getColumns();
        $this->routes = $this->getRoutes();
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

        return view('livewire.datatables.artwork', $data);
    }

    public function resetFilters()
    {
        $this->reset([
            'perPage', 'selectedCollection',
            'search', 'selectedCompany', 'selectedArtist',
            'sortBy', 'sortOrder'
        ]);

    }

    public function getColumns()
    {
        $columns = [
            'company_name' => [
                'name' => 'Company',
                'visible' => true,
            ],
            'collection_name' => [
                'name' => 'Collection',
                'visible' => true,
            ],
            'name' => [
                'name' => 'Name',
                'visible' => true,
                'sortable' => true,
            ],
            'dimensions' => [
                'name' => 'Dimensions in Inch (w x h)',
                'visible' => true,
            ],
            'artist' => [
                'name' => 'Artist',
                'visible' => true,
                'sortable' => true,
            ],
            'type' => [
                'name' => 'Type',
                'visible' => true,
                'sortable' => true,
            ],
            'img' => [
                'name' => 'Image',
                'visible' => true,
                'render' => false,
                'move_to_start' => true
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
