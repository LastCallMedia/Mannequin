<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Engine;

use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Engine\DelegatingEngine;
use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Rendered;
use Prophecy\Argument;

class DelegatingRendererTest extends RendererTestCase
{
    public function getRenderer(): EngineInterface
    {
        $subrenderer = $this->prophesize(EngineInterface::class);
        $subrenderer->supports(Argument::type(ComponentInterface::class))->will(
            function ($args) {
                return $args[0]->getId() === 'supported';
            }
        );
        $subrenderer->render(
            Argument::type(ComponentInterface::class),
            Argument::type('array'),
            Argument::type(Rendered::class)
        )->will(
            function () {
                $rendered = new Rendered();
                $rendered->setCss(['@global_css']);
                $rendered->setJs(['@global_js']);

                return $rendered;
            }
        );
        $subrenderer->renderSource(Argument::type(ComponentInterface::class))
            ->willReturn('Test source');

        return new DelegatingEngine(
            [$subrenderer->reveal()]
        );
    }

    public function getSupportedPattern(): ComponentInterface
    {
        return $this->createPattern('supported')->reveal();
    }
}
