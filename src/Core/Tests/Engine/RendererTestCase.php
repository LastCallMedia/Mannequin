<?php


namespace LastCall\Mannequin\Core\Tests\Engine;


use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Core\Variable\Definition;
use LastCall\Mannequin\Core\Variable\Set;
use PHPUnit\Framework\TestCase;

abstract class RendererTestCase extends TestCase
{

    public function testSupports()
    {
        $this->assertTrue(
            $this->getRenderer()->supports($this->getSupportedPattern())
        );
        $this->assertFalse(
            $this->getRenderer()->supports($this->getUnsupportedPattern())
        );
    }

    abstract public function getRenderer(): EngineInterface;

    abstract public function getSupportedPattern(): PatternInterface;

    protected function getUnsupportedPattern(): PatternInterface
    {
        return $this->createPattern('unsupported')->reveal();
    }

    protected function createPattern($id)
    {
        $pattern = $this->prophesize(PatternInterface::class);
        $pattern->getId()->willReturn($id);
        $pattern->getVariableSets()->willReturn(
            ['default' => new Set('Default')]
        );
        $pattern->getVariableDefinition()->willReturn(new Definition());

        return $pattern;
    }

    public function testRender()
    {
        $pattern = $this->getSupportedPattern();
        $rendered = $this->getRenderer()->render(
            $pattern,
            $pattern->getVariableSets()['default']
        );
        $this->assertInstanceOf(Rendered::class, $rendered);

        return $rendered;
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\UnsupportedPatternException
     */
    public function testRenderUnsupported()
    {
        $pattern = $this->getUnsupportedPattern();
        $this->getRenderer()->render(
            $pattern,
            $pattern->getVariableSets()['default']
        );
    }

    public function testRenderSource()
    {
        $pattern = $this->getSupportedPattern();
        $source = $this->getRenderer()->renderSource(
            $pattern,
            $pattern->getVariableSets()['default']
        );
        $this->assertTrue(is_string($source));

        return $source;
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\UnsupportedPatternException
     */
    public function testRenderSourceUnsupported()
    {
        $pattern = $this->getUnsupportedPattern();
        $this->getRenderer()->renderSource(
            $pattern,
            $pattern->getVariableSets()['default']
        );
    }

}