<?php

namespace WireElements\Pro\Components\SlideOver;

use WireElements\Pro\Components\Overlay;

abstract class SlideOver extends Overlay
{
    protected static function closeEvent(): string
    {
        return 'slide-over.close';
    }
}
