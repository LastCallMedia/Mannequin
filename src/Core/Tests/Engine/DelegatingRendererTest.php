<?php


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