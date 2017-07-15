<?php

namespace LastCall\Mannequin\Core\Tests\Subscriber;

use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Subscriber\NestedPatternVariableSubscriber;
use LastCall\Mannequin\Core\Variable\Definition;
use LastCall\Mannequin\Core\Variable\Set;
use PHPUnit\Framework\TestCase;

class NestedPatternVariableSubscriberTest extends TestCase
{
    use DiscoverySubscriberTestTrait;

    public function testDiscoversNestedPatternInVariable()
    {
        $nested = $this->prophesize(PatternInterface::class)->reveal();

        $pattern = $this->prophesize(PatternInterface::class);
        $pattern->getId()->willReturn('foo');
        $pattern->getVariableDefinition()->willReturn(
            new Definition(['bar' => 'pattern'])
        );
        $pattern->getVariableSets()->willReturn(
            ['default' => new Set('Default', ['bar' => 'baz'])]
        );
        $pattern->addUsedPattern($nested)->willReturn($nested)->shouldBeCalled(
        );

        $collection = $this->prophesize(PatternCollection::class);
        $collection->get('baz')->willReturn($nested)->shouldBeCalled();

        $subscriber = new NestedPatternVariableSubscriber();
        $this->dispatchDiscover(
            $subscriber,
            $pattern->reveal(),
            $collection->reveal()
        );
    }
}
