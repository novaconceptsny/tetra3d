<?php

namespace App\Services;

use App\Models\Spot;
use App\Models\Surface;
use App\SimpleDOM\SimpleDOM;
use SimpleXMLElement;

class SpotXmlGenerator
{
    private Spot $spot;
    private mixed $xmlData;
    private SimpleDOM $xml;

    function __construct(Spot $spot)
    {
        $this->spot = $spot;
        $this->xmlData = $spot->xml;
    }

    public function createXml()
    {
        $this->xml = new SimpleDOM('<krpano/>');
        $this->addIncludes();
        $this->addAction();
        $this->addView();
        $this->addControl();
        $this->addPreview();
        $this->addImage();
        $this->addScaleBox();
        $this->addSurfaces();
        $this->addNavigations();
        $this->addOverlays();

        $dir = public_path("storage/tours/{$this->spot->tour_id}/{$this->spot->id}");

        if (!\File::isDirectory($dir)){
            \File::makeDirectory($dir, 0777, true, true);
        }

        $dom = new \DOMDocument('1.0');
        $dom->preserveWhiteSpace = true;
        $dom->formatOutput = true;
        $dom->loadXML($this->xml->asXML());
        $dom->save("$dir/pano.xml");

        /*$this->xml->saveXML("$dir/pano.xml");*/
    }

    private function addIncludes()
    {
        $includeUrls = [
            $this->getKrpanoAsset('skin/gyro.xml?%$timestamp%'),
            $this->getKrpanoAsset('hotspot.xml?%$timestamp%'),
            $this->getKrpanoAsset('action.xml?%$timestamp%'),
        ];

        foreach ($includeUrls as $url) {
            $this->xml->addChild('include')->addAttribute('url', $url);
        }
    }

    private function addAction()
    {
        $content = "
        div(imageaspect_inst, layer[NOVA_Inst].imagewidth, layer[NOVA_Inst].imageheight);
        if(imageaspect_inst GT screenaspect,
        set(layer[NOVA_Inst].width,80%); set(layer[NOVA_Inst].height,prop);
        ,
        set(layer[NOVA_Inst].width,prop); set(layer[NOVA_Inst].height,70%);
        );
        initiate();
        ";

        foreach ($this->spot->surfaces as $index => $surface) {
            $content .= "set(hotspot[surface_{$surface->id}].url , '%\$surface_{$surface->id}%')\n\t";
            /*$content .= "set(hotspot[surface_{$surface->id}].url , '/krpano/dummy.png')\n\t";*/

            if (count($this->spot->surfaces) == $index + 1) {
                $content = str($content)->trim(" \t")->append("  ");
            }
        }

        $action = $this->xml->addChild('action', $content);
        $action->addAttribute('name', 'startup');
        $action->addAttribute('autorun', 'onstart');
    }

    private function addView()
    {
        $attributes = [
            "hlookat" => $this->xmlData->view['hlookat'] ?? 0,
            "vlookat" => $this->xmlData->view['vlookat'] ?? 0,
            "maxpixelzoom" => "1.0",
            "fov" => $this->xmlData->view['fov'] ?? 90,
            "fovmax" => $this->xmlData->view['fovmax'] ?? 120,
            "limitview" => "auto",
        ];

        $view = $this->xml->addChild('view');

        foreach ($attributes as $attribute => $value) {
            $view->addAttribute($attribute, $value);
        }
    }

    private function addControl()
    {
        $this->xml->addChild('control')
            ->addAttribute('bouncinglimits', 'false');
    }

    private function addPreview()
    {
        $this->xml->addChild('preview')
            ->addAttribute('url', $this->getSpotAsset('panos/preview.jpg'));
    }

    private function addImage()
    {
        $image = $this->xml->addChild('image');
        $cube = $image->addChild('cube');
        $cube->addAttribute('url', $this->getSpotAsset('panos/%s/l%l/%v/l%l_%s_%v_%h.jpg'));
        $cube->addAttribute('multires', '512,768,1664,3200');
    }

    private function addScaleBox()
    {
        $attributes = [
          'type' => 'image',
          'handcursor' => 'false',
          'capture' => 'false',
          'distorted' => 'true',
          'alpha' => '1',
        ];

        $squareAttributes = array_merge($attributes,[
           'name' => 'square',
           'ath' => $this->xmlData->scale_box['square']['ath'] ?? 0,
           'atv' => $this->xmlData->scale_box['square']['atv'] ?? 0,
           'scale' => $this->xmlData->scale_box['square']['scale'] ?? 0,
           'url' => $this->getKrpanoAsset('assets/1000x1000square.png')
        ]);

        $crossAttributes = array_merge($attributes,[
            'name' => 'cross',
            'ath' => $this->xmlData->scale_box['cross']['ath'] ?? 0,
            'atv' => $this->xmlData->scale_box['cross']['atv'] ?? 0,
            'scale' => $this->xmlData->scale_box['cross']['scale'] ?? 0,
            'url' => $this->getKrpanoAsset('assets/1000x1000cross.png')
        ]);

        $square_enabled = $this->xmlData->scale_box['square']['enabled'] ?? false;
        $cross_enabled = $this->xmlData->scale_box['cross']['enabled'] ?? false;

        if ($square_enabled) {
            $square = $this->xml->addChild('hotspot');

            $this->addComment($square, 'Scale box');

            foreach ($squareAttributes as $attribute => $value) {
                $square->addAttribute($attribute, $value);
            }
        }

        if ($cross_enabled){
            $cross = $this->xml->addChild('hotspot');

            if (!$square_enabled){
                $this->addComment($cross, 'Scale box');
            }

            foreach ($crossAttributes as $attribute => $value) {
                $cross->addAttribute($attribute, $value);
            }
        }
    }

    private function addSurfaces()
    {
        foreach ($this->spot->surfaces as $index => $surface){
            $background = $this->addSurfaceBackground($surface);
            $this->addSurfaceClick($surface);

            if ($index === 0) {
                $this->addComment($background, 'Surfaces');
            }
        }
    }

    private function addSurfaceBackground(Surface $surface)
    {
        $attributes = [
            'name' => "surface_{$surface->id}",
            'canvas_url' => route('surfaces.show', [$surface, 'spot_id' => $this->spot]),
            "hotspot_type" => 'artwork',
            "style" => "surface",
            "onclick" => "openCanvas()",
            "coin" => "0",
            "url" => "/krpano/dummy.png",
            "url_main" => $surface->getFirstMediaUrl('main'),
            "url_shared" => $surface->getFirstMedia('shared',
                ['spot_id' => $this->spot->id]
            )?->getUrl(),
            "scale" => $this->getSurfaceData($surface, 'scale'),
            "ath" => $this->getSurfaceData($surface, 'ath'),
            "atv" => $this->getSurfaceData($surface, 'atv'),
            "ox" => $this->getSurfaceData($surface, 'ox'),
            "oy" => $this->getSurfaceData($surface, 'oy'),
            "zorder" => $this->getSurfaceData($surface, 'zorder'),
            "main_w" => $this->getSurfaceData($surface, 'main_w'),
            "main_h" => $this->getSurfaceData($surface, 'main_h'),
            "shared_w" => $this->getSurfaceData($surface, 'shared_w'),
            "shared_h" => $this->getSurfaceData($surface, 'shared_h'),
            "select" => $this->getSurfaceData($surface, 'shared_h'),
            "ox_offset" => $this->getSurfaceData($surface, 'ox_offset'),
            "oy_offset" => $this->getSurfaceData($surface, 'oy_offset'),
            "onloaded" => "setupSurface({$this->getSurfaceData($surface, 'onloaded')})",
        ];

        $hotspot = $this->xml->addChild('hotspot');

        foreach ($attributes as $attribute => $value) {
            $hotspot->addAttribute($attribute, $value);
        }

        return $hotspot;
    }

    private function addSurfaceClick(Surface $surface)
    {
        $attributes = [
            'name' => "surface_{$surface->id}_click",
            'canvas_url' => route('surfaces.show', [$surface, 'spot_id' => $this->spot]),
            "style" => $this->getSurfaceData($surface, 'style', 'click'),
            "onclick" => "openCanvas()",
        ];

        $points = $this->xmlData->surfaces[$surface->id]['click']['points'] ?? [];

        $hotspot = $this->xml->addChild('hotspot');

        foreach ($attributes as $attribute => $value) {
            $hotspot->addAttribute($attribute, $value);
        }

        foreach ($points as $pointData) {
            $this->addPoint($hotspot, $pointData);
        }
    }

    private function addPoint($hotspot, $attributes)
    {
        $point = $hotspot->addChild('point');

        foreach ($attributes as $attribute => $value) {
            $point->addAttribute($attribute, $value);
        }
    }

    private function addNavigations()
    {
        $navigations = $this->xmlData->navigations ?? [];

        foreach ($navigations as $index => $navigation) {
            $navigation_is_enabled = $navigation['enabled'] ?? false;

            if(!$navigation_is_enabled){
                continue;
            }

            $navigation = $this->addNavigation($index, $navigation);

            if ($index === array_key_first($navigations)) {
                $this->addComment($navigation, 'Navigations');
            }
        }
    }

    private function addNavigation($index, $navigationData)
    {
        $attributes = [
            'name' => "spot_{$index}",
            'hotspot_type' => 'navigation',
            'onclick' => 'NavigateTo()',
            'style' => 'artwork_hotspot',
            "rx" => $navigationData['rx'] ?? 75,
            "goto" => $index,
            "goto_url" => route('tours.show', [$this->spot->tour, 'spot_id' => $index]),
            "hlookat" => $navigationData['hlookat'] ?? 0,
            "vlookat" => $navigationData['vlookat'] ?? 0,
            "ath" => $navigationData['ath'] ?? 0,
            "atv" => $navigationData['atv'] ?? 0,
            "scale" => $navigationData['scale'] ?? 1,
        ];

        $navigation = $this->xml->addChild('hotspot');

        foreach ($attributes as $attribute => $value) {
            $navigation->addAttribute($attribute, $value);
        }

        return $navigation;
    }

    private function addOverlays()
    {
        $overlays = $this->xmlData->overlays ?? [];

        foreach ($overlays as $index => $overlay) {
            $overlay = $this->addOverlay($index, $overlay);

            if ($index === array_key_first($overlays)) {
                $this->addComment($overlay, 'Overlays');
            }
        }
    }

    private function addOverlay($index, $overlayData)
    {
        $attributes = [
            'name' => "overlay_{$this->spot->id}_$index",
            'style' => 'overlay_surface',
            "url" => $this->spot->getFirstMedia('overlays',
                ['uuid' => $overlayData['uuid']]
            )?->getUrl(),
            "ath" => $overlayData['ath'] ?? 0,
            "atv" => $overlayData['atv'] ?? 0,
            "scale" => $overlayData['scale'] ?? 0,
            "zorder" => $overlayData['zorder'] ?? 0,
        ];

        $overlay = $this->xml->addChild('hotspot');

        foreach ($attributes as $attribute => $value) {
            $overlay->addAttribute($attribute, $value);
        }

        return $overlay;
    }

    private function getSurfaceData($surface, $field, $type = 'background')
    {
        return $this->xmlData->surfaces[$surface->id][$type][$field] ?? '';
    }

    private function addComment($element, $content, $style = true)
    {
        if (!method_exists($element, 'insertComment')){
            return;
        }

        $styler = ' ####### ';

        if ($style){
            $content = str($content)->prepend($styler)->append($styler);
        }

        $element->insertComment($content, 'before');
    }

    private function getKrpanoAsset($asset)
    {
        return asset("krpano/$asset");
    }

    private function getSpotAsset($asset)
    {
        return asset("storage/tours/{$this->spot->tour_id}/{$this->spot->id}/$asset");
    }
}
