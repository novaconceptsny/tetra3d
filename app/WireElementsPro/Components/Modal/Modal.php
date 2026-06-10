<?php

namespace WireElements\Pro\Components\Modal;

use WireElements\Pro\Components\Overlay;

abstract class Modal extends Overlay
{
    protected static function closeEvent(): string
    {
        return 'modal.close';
    }
}
