<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Tests\Subscriber;

use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Tests\Subscriber\DiscoverySubscriberTestTrait;
use LastCall\Mannequin\Core\Tests\YamlParserProphecyTrait;
use LastCall\Mannequin\Core\YamlMetadataParser;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use LastCall\Mannequin\Twig\Subscriber\InlineTwigYamlMetadataSubscriber;
use LastCall\Mannequin\Twig\TwigInspectorInterface;
use PHPUnit\Framework\TestCase;
use Symfony\Component\EventDispatcher\EventDispatcher;

class InlineTwigYamlMetadataTest extends TestCase
{
    use DiscoverySubscriberTestTrait;
    use YamlParserProphecyTrait;

    public function testCallsInspector()
    {
        $inspector = $this->prophesize(TwigInspectorInterface::class);

        $parser = $this->prophesize(YamlMetadataParser::class);
        $source = new \Twig_Source('', 'test', 'test');
        $pattern = new TwigPattern('foo', [], $source);

        $inspector
            ->inspectPatternData($source)
            ->shouldBeCalled()
            ->willReturn(false);

        $this->dispatch($inspector->reveal(), $parser->reveal(), $pattern);
    }

    public function testCallsParser()
    {
        $inspector = $this->prophesize(TwigInspectorInterface::class);

        $parser = $this->prophesize(YamlMetadataParser::class);
        $source = new \Twig_Source('', 'test', 'test');
        $pattern = new TwigPattern('foo', [], $source);

        $inspector
            ->inspectPatternData($source)
            ->shouldBeCalled()
            ->willReturn('abc');

        $parser
            ->parse('abc', 'test')
            ->shouldBeCalled()
            ->willReturn([]);

        $this->dispatch($inspector->reveal(), $parser->reveal(), $pattern);
    }

    private function dispatch($inspector, $parser, TwigPattern $pattern)
    {
        $subscriber = new InlineTwigYamlMetadataSubscriber($inspector, $parser);

        $collection = $this->prophesize(PatternCollection::class);
        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber($subscriber);
        $dispatcher->dispatch(PatternEvents::DISCOVER, new PatternDiscoveryEvent($pattern, $collection->reveal()));
    }
}
