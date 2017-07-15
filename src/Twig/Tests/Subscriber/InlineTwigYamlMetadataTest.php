<?php

namespace LastCall\Mannequin\Twig\Tests\Subscriber;

use LastCall\Mannequin\Core\Tests\Subscriber\DiscoverySubscriberTestTrait;
use LastCall\Mannequin\Core\Tests\YamlParserProphecyTrait;
use LastCall\Mannequin\Core\Variable\Definition;
use LastCall\Mannequin\Core\Variable\Set;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use LastCall\Mannequin\Twig\Subscriber\InlineTwigYamlMetadataSubscriber;
use LastCall\Mannequin\Twig\TwigInspectorInterface;
use PHPUnit\Framework\TestCase;

class InlineTwigYamlMetadataTest extends TestCase
{
    use DiscoverySubscriberTestTrait;
    use YamlParserProphecyTrait;

    public function testSetsName()
    {
        $pattern = $this->renderAndDispatch(['name' => 'foo']);
        $this->assertEquals('foo', $pattern->getName());
    }

    private function renderAndDispatch($metadata)
    {
        $pattern = new TwigPattern(
            'foo',
            [],
            new \Twig_Source('{%block patterninfo%}{%endblock%}', 'test', '')
        );
        $inspector = $this->prophesize(TwigInspectorInterface::class);
        $inspector->inspectPatternData($pattern->getSource())->willReturn('');

        $parser = $this->getParserProphecy($metadata);
        $subscriber = new InlineTwigYamlMetadataSubscriber(
            $inspector->reveal(),
            $parser->reveal()
        );
        $this->dispatchDiscover($subscriber, $pattern);

        return $pattern;
    }

    public function testSetsDescription()
    {
        $pattern = $this->renderAndDispatch(['description' => 'foo']);
        $this->assertEquals('foo', $pattern->getDescription());
    }

    public function testSetsTags()
    {
        $pattern = $this->renderAndDispatch(['tags' => ['foo' => 'bar']]);
        $this->assertEquals(['foo' => 'bar'], $pattern->getTags());
    }

    public function testSetsDefinition()
    {
        $definition = new Definition(['foo' => 'bar']);
        $pattern = $this->renderAndDispatch(['definition' => $definition]);
        $this->assertSame($definition, $pattern->getVariableDefinition());
    }

    public function testCanOverrideDefaultSet()
    {
        $sets = ['default' => new Set('Overridden')];
        $pattern = $this->renderAndDispatch(['sets' => $sets]);
        $this->assertSame($sets, $pattern->getVariableSets());
    }

    public function testCanAddNewSet()
    {
        $sets = ['additional' => new Set('Additional')];
        $pattern = $this->renderAndDispatch(['sets' => $sets]);
        $this->assertEquals(
            [
                'default' => new Set('Default'),
                'additional' => new Set('Additional'),
            ],
            $pattern->getVariableSets()
        );
    }

    private function getTwig()
    {
        $loader = new \Twig_Loader_Array(
            [
                'test' => '{%block patterninfo%}{%endblock%}',
            ]
        );

        return new \Twig_Environment($loader);
    }
}
