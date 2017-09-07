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
use Prophecy\Argument;
use Symfony\Component\Cache\Adapter\ArrayAdapter;

class TwigInspectorCacheDecoratorTest extends TestCase
{
    public function testInspectLinkedCalledOnceIfTemplateIsFresh()
    {
        $inspector = $this->prophesize(TwigInspectorInterface::class);
        $cache = new ArrayAdapter();
        $source = new \Twig_Source('', 'foo', 'bar');
        $twigMock = $this->prophesize(\Twig_Environment::class);

        $twigMock->isTemplateFresh('foo', Argument::type('int'))
            ->willReturn(true);

        $inspector->inspectLinked($twigMock, $source)
            ->willReturn(['bar'])
            ->shouldBeCalledTimes(1);

        $decorator = new TwigInspectorCacheDecorator(
            $inspector->reveal(),
            $cache
        );
        $twig = $twigMock->reveal();
        $this->assertEquals(['bar'], $decorator->inspectLinked($twig, $source));
        $this->assertEquals(['bar'], $decorator->inspectLinked($twig, $source));
    }

    public function testInspectLinkedCalledAgainIfTemplateIsStale()
    {
        $inspector = $this->prophesize(TwigInspectorInterface::class);
        $cache = new ArrayAdapter();
        $source = new \Twig_Source('', 'foo', 'bar');
        $twigMock = $this->prophesize(\Twig_Environment::class);

        $twigMock->isTemplateFresh('foo', Argument::type('int'))
            ->willReturn(false);

        $inspector->inspectLinked($twigMock, $source)
            ->willReturn(['bar'])
            ->shouldBeCalledTimes(2);

        $decorator = new TwigInspectorCacheDecorator(
            $inspector->reveal(),
            $cache
        );
        $twig = $twigMock->reveal();
        $this->assertEquals(['bar'], $decorator->inspectLinked($twig, $source));
        $this->assertEquals(['bar'], $decorator->inspectLinked($twig, $source));
    }

    public function testInspectPatternDataCalledOnceIfTemplateIsFresh()
    {
        $inspector = $this->prophesize(TwigInspectorInterface::class);
        $cache = new ArrayAdapter();
        $source = new \Twig_Source('', 'foo', 'bar');
        $twigMock = $this->prophesize(\Twig_Environment::class);

        $twigMock->isTemplateFresh('foo', Argument::type('int'))
            ->willReturn(true);

        $inspector->inspectPatternData($twigMock, $source)
            ->willReturn(['bar'])
            ->shouldBeCalledTimes(1);

        $decorator = new TwigInspectorCacheDecorator(
            $inspector->reveal(),
            $cache
        );
        $twig = $twigMock->reveal();
        $this->assertEquals(['bar'], $decorator->inspectPatternData($twig, $source));
        $this->assertEquals(['bar'], $decorator->inspectPatternData($twig, $source));
    }

    public function testInspectPatternDataCalledAgainIfTemplateIsStale()
    {
        $inspector = $this->prophesize(TwigInspectorInterface::class);
        $cache = new ArrayAdapter();
        $source = new \Twig_Source('', 'foo', 'bar');
        $twigMock = $this->prophesize(\Twig_Environment::class);

        $twigMock->isTemplateFresh('foo', Argument::type('int'))
            ->willReturn(false);

        $inspector->inspectPatternData($twigMock, $source)
            ->willReturn(['bar'])
            ->shouldBeCalledTimes(2);

        $decorator = new TwigInspectorCacheDecorator(
            $inspector->reveal(),
            $cache
        );
        $twig = $twigMock->reveal();
        $this->assertEquals(['bar'], $decorator->inspectPatternData($twig, $source));
        $this->assertEquals(['bar'], $decorator->inspectPatternData($twig, $source));
    }
}
