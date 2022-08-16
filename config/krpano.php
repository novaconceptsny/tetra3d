<?php

return [

    'path' => env('KRPANO_TOOLS_PATH', 'krpanotools'),
    'config' => array(
        'tilepath' => '%OUTPUTPATH%/panos/[c/]l%Al/%Av/l%Al[_c]_%Av_%Ah.jpg',
        'previewpath' => '%OUTPUTPATH%/panos/preview.jpg',
        'preview' => 'true',
        'html' => 'false',
        'xml' => 'true',
        'makethumb' => 'false',
    ),

];
