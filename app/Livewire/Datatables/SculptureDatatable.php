<?php

namespace App\Livewire\Datatables;

use App\Models\ArtworkCollection;
use App\Models\SculptureModel;
use App\Models\Company;

class SculptureDatatable extends BaseDatatable
{
    public $model = SculptureModel::class;
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

        $data['heading'] = __('Sculptures');

        $data['collections'] = ArtworkCollection::latest('name')->get();
        $data['companies'] = Company::latest('name')->get();
        $data['artists'] = SculptureModel::groupBy('artist')->pluck('artist');

        $rows = $this->model::query()
            ->with('collection', 'company', 'media')
            ->when(
                $this->selectedCollection,
                fn($query) => $query->where('artwork_collection_id', $this->selectedCollection)
            )->when(
                $this->selectedCompany,
                fn($query) => $query->where('company_id', $this->selectedCompany)
            )->when(
                $this->selectedArtist,
                fn($query) => $query->where('artist', $this->selectedArtist)
            )->whereAnyColumnLike($this->search)
            ->sort($this->sortBy, $this->sortOrder)
            ->paginate($this->perPage);

        $rows->getCollection()->transform(function ($row) {
            $row->company_name = $row->company->name;
            $row->collection_name = $row->collection?->name;
            $length = number_format((float) $row->data['length'], 2);
            $width = number_format((float) $row->data['width'], 2);
            $height = number_format((float) $row->data['height'], 2);
            $row->dimensions = "{$height}x{$width}x{$length}";
            $row->image_url = $row->getFirstMediaUrl('thumbnail');
            return $row;
        });

        error_log($rows);

        $data['rows'] = $rows;
        $data['label'] = 'sculptureModel';

        return view('livewire.datatables.sculpture-datatable', $data);
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
                'name' => 'l" x w" x h"',
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

        if (!user()->isAdmin()) {
            unset($columns['company_name']);
        }

        return $columns;
    }
    public function getRoutes()
    {
        $routes = [
            'create' => 'backend.sculptures.create',
            'edit' => 'backend.sculptures.edit',
            'delete' => 'backend.sculptures.destroy',
        ];

        if (user()->cannot('create', SculptureModel::class)) {
            unset($routes['create']);
        }

        return $routes;
    }
}