<?php

namespace App\Services;

use App\Models\Spot;
use App\Models\Surface;
use SimpleXMLElement;

class SpotXmlGenerator
{
    private Spot $spot;
    private SimpleXMLElement $xml;

    function __construct(Spot $spot)
    {
        $this->spot = $spot;
    }

    public function createXml()
    {
        $this->xml = new SimpleXMLElement('<krpano/>');
        $this->addIncludes();
        $this->addAction();
        $this->addView();
        $this->addPreview();
        $this->addImage();
        $this->addSurfaces();
        $this->addOverlays();

        $dir = storage_path("projects/1");

        $this->xml->saveXML("$dir/tour.xml");
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
            $content .= "set(hotspot[Wall{$surface->id}].url , '%\$Wall_{$surface->id}%')";
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

    private function addSurfaces()
    {
        foreach ($this->spot->surfaces as $surface){
            $this->addSurfaceBackground($surface);
            $this->addSurfaceClick($surface);
        }
    }

    private function addOverlays()
    {
        $attributes = [
            'name' => 'overlay48001-1',
            'style' => 'overlay_surface',
            "url" => "/krpano/overlay/p4/overlay48001-1(-52 0 1).png",
            "url_shared" => "/surface/p4/s4001BoxPoint1(Shared) copy.png",
            "ath" => "-180.1",
            "atv" => "0",
            "scale" => "2.667",
            "zorder" => "21",
        ];

        $hotspot = $this->xml->addChild('hotspot');

        foreach ($attributes as $attribute => $value) {
            $hotspot->addAttribute($attribute, $value);
        }
    }

    private function addSurfaceBackground(Surface $surface)
    {
        $attributes = [
            'name' => 'Wall',
            'canvas_id' => '',
            "hotspot_type" => "artwork",
            "style" => "surface",
            "onclick" => "getUrl()",
            "coin" => "0",
            "url" => "/krpano/dummy.png",
            "url_main" => "/surface/p4/s4001(Main) copy.png",
            "url_shared" => "/surface/p4/s4001BoxPoint1(Shared) copy.png",
            "scale" => "2.667",
            "ath" => "-180.1",
            "atv" => "0",
            "ox" => "910",
            "oy" => " - 233",
            "zorder" => "21",
            "main_w" => "1500",
            "main_h" => "918",
            "shared_w" => "730",
            "shared_h" => "444",
            "select" => "h",
            "ox_offset" => "0",
            "oy_offset" => "0",
            "onloaded" => "setupSurface(live)",
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
            'name' => 'Wall',
            'canvas_id' => '',
            "style" => "surface_click",
            "onclick" => "getUrl()",
        ];

        $points = [
          ['ath' => 172.85, 'atv' => -58.38],
          ['ath' => -105.01, 'atv' => -22.80],
          ['ath' => -105.08, 'atv' => 10.32],
          ['ath' => -105.08, 'atv' => 10.32],
        ];

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
}
