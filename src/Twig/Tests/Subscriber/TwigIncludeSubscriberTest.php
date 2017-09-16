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

use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Tests\Stubs\TestPattern;
use LastCall\Mannequin\Core\Tests\Subscriber\DiscoverySubscriberTestTrait;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use LastCall\Mannequin\Twig\Subscriber\TwigIncludeSubscriber;
use PHPUnit\Framework\TestCase;

class TwigIncludeSubscriberTest extends TestCase
{
    use DiscoverySubscriberTestTrait;

    public function getTwig()
    {
        $loader = new \Twig_Loader_Array([
            'p1' => '{% block _collected_usage %}["foo"]{%endblock%}',
        ]);
        $twig = new \Twig_Environment($loader);

        return $twig;
    }

    public function testDiscoversUsageOfValidPatterns()
    {
        $twig = $this->getTwig();
        $source = $twig->load('p1')->getSourceContext();
        $p1 = new TwigPattern('p1', [], $source, $twig);
        $foo = new TestPattern('foo');
        $subscriber = new TwigIncludeSubscriber();

        $collection = new PatternCollection([$foo]);
        $this->dispatchDiscover($subscriber, $p1, $collection);
        $this->assertEquals([$foo], $p1->getUsedPatterns());
    }

    public function testIgnoresUsageOfUnknownPatterns()
    {
        $twig = $this->getTwig();
        $source = $twig->load('p1')->getSourceContext();
        $p1 = new TwigPattern('p1', [], $source, $twig);
        $subscriber = new TwigIncludeSubscriber();

        $collection = new PatternCollection([]);
        $this->dispatchDiscover($subscriber, $p1, $collection);
        $this->assertEquals([], $p1->getUsedPatterns());
    }
}
