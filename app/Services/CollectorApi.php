<?php

namespace App\Services;

use App\Models\Spot;
use Arr;
use Exception;
use Http;

class CollectorApi
{

    private string $baseUrl = 'https://api.collectorsystems.com';


    function __construct($subscription_id)
    {
        $this->baseUrl .= "/$subscription_id";
    }

    public function getObjectById($object_id = '379233')
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

        $object = $request->object()->data[0] ;

        if ( ! property_exists($object, "heightimperial")
             || ! property_exists($object, "widthimperial")
        ) {
            throw new Exception("No dimensions found, artwork not fetched",
                404);
        }

        $object->image_url = "{$this->baseUrl}/objects/{$object_id}/mainimage";
        $object->dimensions = $this->getDimensions($object);

        return $object;
    }

    private function getDimensions($object)
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

}
