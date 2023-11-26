<?php

namespace App\Livewire\Modals;

use App\Models\Artwork;
use App\Models\Company;
use App\Services\CollectorApi;
use WireElements\Pro\Components\Modal\Modal;

class CollectorTools extends Modal
{
    public Company|int $company;
    public $output;

    public $objectId ;
    public $collectionId ;
    public $syncBy = 'object';
    public ?Artwork $artwork;
    public $success = false;

    public function mount(Company $company)
    {
        $this->company = $company;
    }

    public function render()
    {
        return view('livewire.modals.collector-tools');
    }

    public function sync()
    {
        $this->output = false;
        $this->artwork = null;
        $this->success = false;

        if ($this->syncBy == 'object'){
            $this->getArtwork();
        }

        if ($this->syncBy == 'collection'){
            $this->getCollection();
        }
    }

    public function getArtwork()
    {
        $collector = new CollectorApi($this->company);
        try {
            $object = $collector->getObjectById($this->objectId);
            $this->artwork = $collector->syncArtwork($object);
            $this->success = true;
            $this->output = "Artwork synced successfully";
        } catch (\Exception $exception){
            $this->output = $exception->getMessage();
        }
    }

    public function getCollection()
    {
        $collector = new CollectorApi($this->company);
        try {
            $report_url = route('backend.collector.report');

            $collector->syncCollection($this->collectionId);
            $this->success = true;
            $this->output = "Collection has started syncing. Click <a href='$report_url' target='_blank'>here</a> to see report";
        } catch (\Exception $exception){
            $this->output = $exception->getMessage();
        }
    }

}
