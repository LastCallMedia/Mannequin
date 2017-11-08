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
use LastCall\Mannequin\Core\Component\Sample;
use LastCall\Mannequin\Core\Event\ComponentEvents;
use LastCall\Mannequin\Core\Event\RenderEvent;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Core\Tests\Stubs\TestComponent;
use LastCall\Mannequin\Core\Tests\Subscriber\ComponentSubscriberTestTrait;
use LastCall\Mannequin\Twig\Component\TwigComponent;
use LastCall\Mannequin\Twig\Subscriber\MarkupWrapperSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcher;

class MarkupWrapperSubscriberTest extends TestComponent
{
    use ComponentSubscriberTestTrait;

    public function getWrapperTests()
    {
        $rendered = new Rendered();
        $rendered->setMarkup('foo');
        $markup = new \Twig_Markup('foo');

        return [
            [['foo' => 'foo'], ['foo' => 'foo']],
            [['foo' => $rendered], ['foo' => $markup]],
            [['foo' => ['bar' => $rendered]], ['foo' => ['bar' => $markup]]],
        ];
    }

    /**
     * @dataProvider getWrapperTests
     */
    public function testWrapper($variables, $expected)
    {
        $collection = $this->prophesize(ComponentCollection::class);
        $component = $this->prophesize(TwigComponent::class);
        $sample = $this->prophesize(Sample::class);

        $event = new RenderEvent($collection, $component, $sample);
        $event->setVariables($variables);

        $dispatcher = new EventDispatcher();
        $dispatcher->addSubscriber(new MarkupWrapperSubscriber());
        $dispatcher->dispatch(ComponentEvents::PRE_RENDER, $event);

        $this->assertEquals($expected, $event->getVariables());
    }
}
