<?php

namespace App\MediaLibrary;


trait InteractsWithMedia
{
    use \Spatie\MediaLibrary\InteractsWithMedia;

    public function getMediaUrls($collectionName = 'default')
    {
        $media = $this->getMedia($collectionName);
        $urls = array();
        foreach ($media as $mediaItem) {
            $urls[] = $mediaItem->getUrl();
        }

        return $urls;
    }

}
