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

        if (! user()->can('accessCollector', $this->company)) {
            $this->output = 'You are not allowed to use Collector Tools for this company.';
            return;
        }
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
        try {
            $collector = new CollectorApi($this->company);
            $object = $collector->getObjectById($this->objectId);
            $this->artwork = $collector->syncArtwork($object);
            $this->success = true;
            $this->output = "Artwork synced successfully";
        } catch (\Exception $exception){
            $this->output = e($exception->getMessage());
        }
    }

    public function getCollection()
    {
        try {
            $collector = new CollectorApi($this->company);
            $report_url = route('backend.collector.report');

            $collector->syncCollection($this->collectionId);
            $this->success = true;
            $this->output = "Collection sync has started. Click <a href='$report_url' target='_blank'>here</a> to see the report";
        } catch (\Exception $exception){
            $this->output = e($exception->getMessage());
        }
    }

}
