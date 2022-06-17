<?php

namespace App\Services;

use App\Models\Spot;
use App\Models\Surface;
use SimpleXMLElement;

class SpotXmlGenerator
{
    private Spot $spot;
    private mixed $xmlData;
    private SimpleXMLElement $xml;

    function __construct(Spot $spot)
    {
        $this->spot = $spot;
        $this->xmlData = $spot->xml;
    }

    public function createXml()
    {
        $this->xml = new SimpleXMLElement('<krpano/>');
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

        $this->xml->saveXML("$dir/pano.xml");
    }

    private function addIncludes()
    {
        $includeUrls = [
            '/krpano/skin/gyro.xml?%$timestamp%',
            '/krpano/hotspot.xml?%$timestamp%',
            '/krpano/action.xml?%$timestamp%',
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

        foreach ($this->spot->surfaces as $surface) {
            $content .= "set(hotspot[surface_{$surface->id}].url , '%\$Wall_{$surface->id}%')\n";
        }

        $action = $this->xml->addChild('action', $content);
        $action->addAttribute('name', 'startup');
        $action->addAttribute('autorun', 'onstart');
    }

    private function addView()
    {
        $attributes = [
            "hlookat" => "-90",
            "vlookat" => "0",
            "maxpixelzoom" => "1.0",
            "fov" => "90",
            "fovmax" => "120",
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
            ->addAttribute('url', 'panos/preview.jpg');
    }

    private function addImage()
    {
        $image = $this->xml->addChild('image');
        $cube = $image->addChild('cube');
        $cube->addAttribute('url', 'panos/%s/l%l/%v/l%l_%s_%v_%h.jpg');
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
           'atv' => $this->xmlData->scale_box['square']['ath'] ?? 0,
           'scale' => $this->xmlData->scale_box['square']['scale'] ?? 0,
            'url' => '/assets/1000x1000square.png'
        ]);

        $crossAttributes = array_merge($attributes,[
            'name' => 'cross',
            'ath' => $this->xmlData->scale_box['cross']['ath'] ?? 0,
            'atv' => $this->xmlData->scale_box['cross']['ath'] ?? 0,
            'scale' => $this->xmlData->scale_box['cross']['scale'] ?? 0,
            'url' => '/assets/1000x1000cross.png'
        ]);

        $square_enabled = $this->xmlData->scale_box['square']['enabled'] ?? false;
        $cross_enabled = $this->xmlData->scale_box['cross']['enabled'] ?? false;

        if ($square_enabled){
            $square = $this->xml->addChild('hotspot');
            foreach ($squareAttributes as $attribute => $value) {
                $square->addAttribute($attribute, $value);
            }
        }

        if ($cross_enabled){
            $cross = $this->xml->addChild('hotspot');
            foreach ($crossAttributes as $attribute => $value) {
                $cross->addAttribute($attribute, $value);
            }
        }
    }

    private function addSurfaces()
    {
        foreach ($this->spot->surfaces as $surface){
            $this->addSurfaceBackground($surface);
            $this->addSurfaceClick($surface);
        }
    }

    private function addSurfaceBackground(Surface $surface)
    {
        $attributes = [
            'name' => "surface_{$surface->id}",
            'canvas_id' => $surface->id,
            "hotspot_type" => 'artwork',
            "style" => "surface",
            "onclick" => "getUrl()",
            "coin" => "0",
            "url" => "/krpano/dummy.png",
            "url_main" => "/surface/p4/s4001(Main) copy.png", //todo
            "url_shared" => "/surface/p4/s4001BoxPoint1(Shared) copy.png",
            //todo
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
    }

    private function addSurfaceClick(Surface $surface)
    {
        /*
         * style = surface_click | surface_click_line*/

        $attributes = [
            'name' => "surface_{$surface->id}_click",
            'canvas_id' => $surface->id,
            "style" => $this->getSurfaceData($surface, 'scale', 'click'),
            "onclick" => "getUrl()",
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
        $navigations = $this->xmlData->spots ?? [];

        foreach ($navigations as $index => $navigation) {
            $this->addNavigation($index, $navigation);
        }
    }

    private function addNavigation($index, $navigationData)
    {
        $attributes = [
            'name' => "spot_{$index}",
            'hotspot_type' => 'overlay_surface',
            'onclick' => 'NavigateTo()',
            'style' => 'artwork_hotspot',
            "rx" => $navigationData['rx'] ?? 0,
            "goto" => $index->id,
            "hlookat" => $navigationData['hlookat'] ?? 0,
            "vlookat" => $navigationData['vlookat'] ?? 0,
            "ath" => $navigationData['ath'] ?? 0,
            "atv" => $navigationData['atv'] ?? 0,
            "scale" => $navigationData['scale'] ?? 0,
        ];

        $navigation = $this->xml->addChild('hotspot');

        foreach ($attributes as $attribute => $value) {
            $navigation->addAttribute($attribute, $value);
        }
    }

    private function addOverlays()
    {
        $overlays = $this->xmlData->overlays ?? [];

        foreach ($overlays as $index => $overlay) {
            $this->addOverlay($index, $overlay);
        }
    }

    private function addOverlay($index, $overlayData)
    {
        $attributes = [
            'name' => "overlay_{$this->spot->id}_$index",
            'style' => 'overlay_surface',
            "url" => "/krpano/overlay/p4/overlay48001-1(-52 0 1).png", //todo,
            "ath" => $overlayData['ath'] ?? 0,
            "atv" => $overlayData['atv'] ?? 0,
            "scale" => $overlayData['scale'] ?? 0,
            "zorder" => $overlayData['zorder'] ?? 0,
        ];

        $overlay = $this->xml->addChild('hotspot');

        foreach ($attributes as $attribute => $value) {
            $overlay->addAttribute($attribute, $value);
        }

    }

    private function getSurfaceData($surface, $field, $type = 'background')
    {
        return $this->xmlData->surfaces[$surface->id][$type][$field] ?? '';
    }
}
