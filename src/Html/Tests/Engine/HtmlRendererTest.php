<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Html\Tests\Engine;

use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Tests\Engine\RendererTestCase;
use LastCall\Mannequin\Html\Component\HtmlComponent;
use LastCall\Mannequin\Html\Engine\HtmlEngine;

class HtmlRendererTest extends RendererTestCase
{
    public function getSupportedComponent(): ComponentInterface
    {
        return new HtmlComponent(
            'foo',
            [],
            new \SplFileInfo(__DIR__.'/../Resources/button.html')
        );
    }

    public function getRenderer(): EngineInterface
    {
        return new HtmlEngine();
    }
}
