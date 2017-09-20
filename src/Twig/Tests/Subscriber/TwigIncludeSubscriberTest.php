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

use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Tests\Stubs\TestComponent;
use LastCall\Mannequin\Core\Tests\Subscriber\ComponentSubscriberTestTrait;
use LastCall\Mannequin\Twig\Component\TwigComponent;
use LastCall\Mannequin\Twig\Subscriber\TwigIncludeSubscriber;
use PHPUnit\Framework\TestCase;

class TwigIncludeSubscriberTest extends TestCase
{
    use ComponentSubscriberTestTrait;

    public function getTwig()
    {
        $loader = new \Twig_Loader_Array([
            'p1' => '{% block _collected_usage %}["foo"]{%endblock%}',
        ]);
        $twig = new \Twig_Environment($loader);

        return $twig;
    }

    public function testDiscoversUsageOfValidComponents()
    {
        $twig = $this->getTwig();
        $source = $twig->load('p1')->getSourceContext();
        $p1 = new TwigComponent('p1', [], $source, $twig);
        $foo = new TestComponent('foo');
        $subscriber = new TwigIncludeSubscriber();

        $collection = new ComponentCollection([$foo]);
        $this->dispatchDiscover($subscriber, $p1, $collection);
        $this->assertEquals([$foo], $p1->getUsedComponents());
    }

    public function testIgnoresUsageOfUnknownComponents()
    {
        $twig = $this->getTwig();
        $source = $twig->load('p1')->getSourceContext();
        $p1 = new TwigComponent('p1', [], $source, $twig);
        $subscriber = new TwigIncludeSubscriber();

        $collection = new ComponentCollection([]);
        $this->dispatchDiscover($subscriber, $p1, $collection);
        $this->assertEquals([], $p1->getUsedComponents());
    }

    /**
     * @expectedException \LastCall\Mannequin\Core\Exception\TemplateParsingException
     * @expectedExceptionMessage Twig error thrown during usage checking of no_exist
     */
    public function testHandlesTwigException()
    {
        $twig = $this->getTwig();
        $source = new \Twig_Source('', 'no_exist', '');
        $component = new TwigComponent('', [], $source, $twig);
        $subscriber = new TwigIncludeSubscriber();
        $this->dispatchDiscover($subscriber, $component);
    }
}
