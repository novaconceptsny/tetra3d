<?php

namespace App\Livewire\Datatables;

use Livewire\Component;
use App\Models\Artwork;
use App\Models\ArtworkCollection;
use App\Models\Company;
use App\Models\SculptureModel;

class SculptureDatatable extends BaseDatatable
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
        $this->dispatch('contentChanged');
    }
    public function render()
    {
        $data = array();

        $data['heading'] = __('Sculptures');

        $rows = SculptureModel::all();
        
        foreach($rows as $row) {
            $row->data = json_decode($row->data);
            $row->data->length = number_format((float)$row->data->length, 2);
            $row->data->width = number_format((float)$row->data->width, 2);
            $row->data->height = number_format((float)$row->data->height, 2);
            $row->data = $row->data->length.'x'.$row->data->width.'x'.$row->data->height;
        }

        $data['rows'] = $rows;
        $data['label'] = 'sculptureModel';

        return view('livewire.datatables.sculpture-datatable', $data);
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
            'name' => [
                'name' => 'Name',
                'visible' => true,
                'sortable' => true,
                'th-classes' => 'w-40'
            ],
            'data' => [
                'name' => 'Dimensions (l" x w" x h")',
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
            'create' => 'backend.sculptures.create',
            'edit' => 'backend.sculptures.edit',
            'delete' => 'backend.sculptures.destroy',
        ];

        if (user()->cannot('create', Artwork::class)) {
            unset($routes['create']);
        }

        return $routes;
    }
}
