<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Subscriber;

use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Component\Sample;
use LastCall\Mannequin\Core\Mannequin;
use LastCall\Mannequin\Core\Subscriber\VariableResolverSubscriber;
use LastCall\Mannequin\Core\Variable\VariableResolver;
use LastCall\Mannequin\Core\Variable\VariableSet;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class VariableResolverSubscriberTest extends TestCase
{
    use ComponentSubscriberTestTrait;

    public function testPassesExpectedContextToResolver()
    {
        $mannequin = $this->prophesize(Mannequin::class);
        $context = Argument::that(function ($actual) {
            $this->assertEquals(
                ['mannequin', 'collection', 'component', 'sample'],
                array_keys($actual)
            );
            $this->assertInstanceOf(Mannequin::class, $actual['mannequin']);
            $this->assertInstanceOf(ComponentCollection::class, $actual['collection']);
            $this->assertInstanceOf(ComponentInterface::class, $actual['component']);
            $this->assertInstanceOf(Sample::class, $actual['sample']);

            return true;
        });
        $resolver = $this->prophesize(VariableResolver::class);
        $resolver
            ->resolve(Argument::type(VariableSet::class), $context)
            ->shouldBeCalled()
            ->willReturn([]);

        $subscriber = new VariableResolverSubscriber($resolver->reveal(), $mannequin->reveal());
        $this->dispatchPreRender($subscriber);
    }
}
