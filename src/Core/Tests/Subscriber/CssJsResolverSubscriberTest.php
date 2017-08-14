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

use Assetic\Asset\AssetInterface;
use Assetic\Factory\AssetFactory;
use LastCall\Mannequin\Core\Event\RenderEvent;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Core\Subscriber\CssJsResolverSubscriber;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CssJsResolverSubscriberTest extends TestCase
{
    public function testResolvesCssJs()
    {
        $rendered = new Rendered(['foo'], ['bar']);
        $event = $this->prophesize(RenderEvent::class);
        $event->getRendered()->willReturn($rendered);
        $event->getVariables()->willReturn([]);
        $event->isRoot()->willReturn(true);

        $factory = $this->getMockFactory();
        $generator = $this->getMockGenerator();
        $subscriber = new CssJsResolverSubscriber($factory->reveal(), $generator->reveal());
        $subscriber->resolve($event->reveal());

        $this->assertEquals(['foo.resolved.routed'], $rendered->getCss());
        $this->assertEquals(['bar.resolved.routed'], $rendered->getJs());
        $this->assertCount(2, $rendered->getAssets()->all());
    }

    public function testDoesNotResolveNestedCssJs()
    {
        $rendered = new Rendered(['foo'], ['bar']);
        $event = $this->prophesize(RenderEvent::class);
        $event->getVariables()->willReturn([]);
        $event->getRendered()->willReturn($rendered);
        $event->isRoot()->willReturn(false);

        $factory = $this->getMockFactory();
        $generator = $this->getMockGenerator();
        $subscriber = new CssJsResolverSubscriber($factory->reveal(), $generator->reveal());
        $subscriber->resolve($event->reveal());

        $this->assertEquals(['foo'], $rendered->getCss());
        $this->assertEquals(['bar'], $rendered->getJs());
        $this->assertCount(0, $rendered->getAssets()->all());
    }

    public function testBubblesFromVariables()
    {
        $parent = new Rendered(['foo'], ['bar']);
        $child = new Rendered(['baz'], ['boo']);

        $event = $this->prophesize(RenderEvent::class);
        $event->getRendered()->willReturn($parent);
        $event->getVariables()->willReturn(['c1' => $child]);
        $event->isRoot()->willReturn(true);

        $factory = $this->getMockFactory();
        $generator = $this->getMockGenerator();
        $subscriber = new CssJsResolverSubscriber($factory->reveal(), $generator->reveal());
        $subscriber->bubbleFromVariables($event->reveal());

        $this->assertEquals(['foo', 'baz'], $parent->getCss());
        $this->assertEquals(['bar', 'boo'], $parent->getJs());
    }

    private function getMockGenerator()
    {
        $generator = $this->prophesize(UrlGeneratorInterface::class);
        $generator->generate(
            'static',
            Argument::that(function ($params) {
                return is_array($params) && isset($params['name']) && is_string($params['name']);
            }),
            UrlGeneratorInterface::RELATIVE_PATH
        )->will(function ($args) {
            return $args[1]['name'].'.routed';
        });

        return $generator;
    }

    private function getMockFactory()
    {
        $self = $this;
        $factory = $this->prophesize(AssetFactory::class);
        $factory->createAsset(
            Argument::type('array'),
            [],
            Argument::type('array')
        )
            ->will(function ($args) use ($self) {
                $asset = $self->prophesize(AssetInterface::class);
                $asset->getTargetPath()->willReturn(implode('-', $args[0]).'.resolved');

                return $asset;
            });

        return $factory;
    }
}
