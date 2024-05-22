<?php

namespace App\Services;

use App\Jobs\SyncArtworksJob;
use App\Models\Artwork;
use App\Models\ArtworkCollection;
use App\Models\Company;
use App\Models\Spot;
use Arr;
use Exception;
use Http;
use Illuminate\Support\Facades\Log;

class CollectorApi
{

    private string $baseUrl = 'https://api.collectorsystems.com';

    private Company $company;


    function __construct(Company $company)
    {
        $this->company = $company;
        $subscription_id = $company->collector_subscription_id;

        $this->baseUrl .= "/$subscription_id";
    }

    public function syncCollection($collection_id)
    {
        if (!$collection_id){
            throw new Exception("Collection ID can not be null", 403);
        }

        $request = Http::get("{$this->baseUrl}/collections/$collection_id");

        if (!$request->successful()){
            throw new Exception("Something went wrong", 404);
        }

        if (empty($request->object()->data)){
            throw new Exception("No Collection found", 404);
        }

        $collection = $request->object()->data[0];

        SyncArtworksJob::dispatch($this, $collection);

        //$this->syncArtworksFromCollection($collection);
    }

    public function syncArtworksFromCollection($collection): void
    {
        ini_set('memory_limit', -1);

        \File::delete(storage_path('logs/collector.log'));

        $objects = $collection->objects;

        Log::channel('collector-sync-report')->info(
            "*********** Syncing Collection $collection->collectionname ***********"
        );

        foreach ($objects as $index => $object){
            $current = $index + 1;

            Log::channel('collector-sync-report')->info(
                "Syncing Object $current/$collection->objectcount "
            );

            try {
                $this->prepareObject($object);
                $this->syncArtwork($object);
            } catch (Exception $exception){
                Log::channel('collector-sync-report')->error(
                    "############ Error in Object $object->objectid ############ \n {$exception->getMessage()}"
                );
            }
        }

        Log::channel('collector-sync-report')->info(
            "*********** Collection Synced Successfully ***********"
        );
    }

    public function syncArtwork($object): Artwork
    {
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

        // refresh model, to ensure the media is attached!
        $artwork->refresh();

        $artwork->resizeImage();

        return $artwork;
    }

    /**
     * @throws Exception
     */
    public function getObjectById($object_id)
    {
        if (!$object_id){
            throw new Exception("Object ID can not be null", 403);
        }

        $request = Http::get("{$this->baseUrl}/objects/{$object_id}?pretty=1");

        if (!$request->successful()){
            throw new Exception("Something went wrong", 404);
        }

        if (empty($request->object()->data)){
            throw new Exception("No object found", 404);
        }

        $object = $request->object()->data[0];

        $this->prepareObject($object);

        return $object;
    }

    private function getDimensions($object): array
    {
        return array(
            'width' => $object->widthimperial,
            'height' => $object->heightimperial
        );

        /*if(!property_exists($object, "dimensions")){
            return $dimensions;
        }

        $object_dimension = Arr::where(
            $object->dimensions,
            fn($dimension) => $dimension->dimensiondescription == $object->dimensiondescription
        );
        $object_dimension = Arr::first($object_dimension);

        $dimensions['height'] = $object_dimension->heightimperial;
        $dimensions['width'] = $object_dimension->widthimperial;

        return $dimensions;*/
    }

    /**
     * @throws Exception
     */
    private function prepareObject(&$object): void
    {
        if ( ! property_exists($object, "heightimperial")
             || ! property_exists($object, "widthimperial")
        ) {
            throw new Exception(
                "No dimensions found, artwork not fetched",
                404
            );
        }

        $object->image_url = "{$this->baseUrl}/objects/$object->objectid/mainimage";
        $object->dimensions = $this->getDimensions($object);
    }

}
