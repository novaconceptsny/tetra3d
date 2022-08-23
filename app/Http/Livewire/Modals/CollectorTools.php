<?php

namespace App\Http\Livewire\Modals;

use App\Models\Artwork;
use App\Models\ArtworkCollection;
use App\Models\Company;
use App\Services\CollectorApi;
use Livewire\Component;

class CollectorTools extends Component
{
    public Company $company;
    public $output;

    public $objectId ;
    public ?Artwork $artwork;

    public function mount(Company $company)
    {
        $this->company = $company;
    }

    public function render()
    {
        return view('livewire.modals.collector-tools');
    }

    public function getArtwork()
    {
        $this->output = false;
        $this->artwork = null;

        $collector = new CollectorApi($this->company->collector_subscription_id);
        try {
            $object = $collector->getObjectById($this->objectId);

            $collection = ArtworkCollection::firstOrCreate([
                'company_id' => $this->company->id,
                'name' => $object->collectionname
            ], []);

            $artwork = Artwork::updateOrCreate([
                'collector_object_id' => $object->objectid,
            ], [
                'company_id' => $this->company->id,
                'artwork_collection_id' => $collection->id,
                'name' => $object->title,
                'artist' => $object->artistname,
                'type' => $object->objecttype,
                'data' => [
                    'width_inch' => $object->dimensions['width'],
                    'height_inch' => $object->dimensions['height'],
                ],
            ]);

            $artwork->addMediaFromUrl($object->image_url)->toMediaCollection('image');
            $this->artwork = $artwork;

        } catch (\Exception $exception){
            $this->output = $exception->getMessage();
        }
    }

}
