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

use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Core\Variable\Set;
use Prophecy\Argument;

class DelegatingRendererTest extends RendererTestCase
{
    public function getRenderer(): EngineInterface
    {
        $subrenderer = $this->prophesize(EngineInterface::class);
        $subrenderer->supports(Argument::type(PatternInterface::class))->will(
            function ($args) {
                return $args[0]->getId() === 'supported';
            }
        );
        $subrenderer->render(
            Argument::type(PatternInterface::class),
            Argument::type(Set::class)
        )->will(
            function ($args) {
                return new Rendered();
            }
        );
        $subrenderer->renderSource(Argument::type(PatternInterface::class))
            ->willReturn('Test source');

        return new \LastCall\Mannequin\Core\Engine\DelegatingEngine(
            [$subrenderer->reveal()]
        );
    }

    public function getSupportedPattern(): PatternInterface
    {
        return $this->createPattern('supported')->reveal();
    }
}
