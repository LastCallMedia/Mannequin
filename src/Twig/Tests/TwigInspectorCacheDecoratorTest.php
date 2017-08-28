<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Tests;

use LastCall\Mannequin\Twig\TwigInspectorCacheDecorator;
use LastCall\Mannequin\Twig\TwigInspectorInterface;
use PHPUnit\Framework\TestCase;
use Psr\Cache\CacheItemInterface;
use Psr\Cache\CacheItemPoolInterface;

class TwigInspectorCacheDecoratorTest extends TestCase
{
    public function testReturnsLinkedFromCache()
    {
        $source = $this->prophesize(\Twig_Source::class);
        $twig = $this->prophesize(\Twig_Environment::class);

        $inspector = $this->prophesize(TwigInspectorInterface::class);
        $inspector->inspectLinked($twig, $source)->shouldNotBeCalled();

        $item = $this->prophesize(CacheItemInterface::class);
        $item->isHit()->willReturn(true);
        $item->get()->willReturn(['bar']);
        $cache = $this->prophesize(CacheItemPoolInterface::class);
        $cache->getItem('linked.d41d8cd98f00b204e9800998ecf8427e')->willReturn(
            $item
        );

        $inspector = new TwigInspectorCacheDecorator(
            $inspector->reveal(),
            $cache->reveal()
        );
        $this->assertEquals(['bar'], $inspector->inspectLinked($twig->reveal(), $source->reveal()));
    }

    public function testFetchesLinkedFromDecoratedWhenCacheMiss()
    {
        $source = $this->prophesize(\Twig_Source::class);
        $twig = $this->prophesize(\Twig_Environment::class);

        $inspector = $this->prophesize(TwigInspectorInterface::class);
        $inspector->inspectLinked($twig, $source)
            ->willReturn(['bar'])
            ->shouldBeCalled();

        $item = $this->prophesize(CacheItemInterface::class);
        $item->isHit()->willReturn(false);
        $item->set(['bar'])->will(
            function ($args) {
                $this->get()->willReturn($args[0]);
            }
        );

        $cache = $this->prophesize(CacheItemPoolInterface::class);
        $cache->getItem('linked.d41d8cd98f00b204e9800998ecf8427e')->willReturn(
            $item
        );
        $cache->save($item)->shouldBeCalled();

        $inspector = new TwigInspectorCacheDecorator(
            $inspector->reveal(),
            $cache->reveal()
        );
        $this->assertEquals(['bar'], $inspector->inspectLinked($twig->reveal(), $source->reveal()));
    }
}
