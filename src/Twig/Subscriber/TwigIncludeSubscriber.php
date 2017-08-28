<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Subscriber;

use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use LastCall\Mannequin\Twig\TwigInspectorInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class TwigIncludeSubscriber implements EventSubscriberInterface
{
    private $inspector;

    public function __construct(
        TwigInspectorInterface $inspector
    ) {
        $this->inspector = $inspector;
    }

    public static function getSubscribedEvents()
    {
        return [
            PatternEvents::DISCOVER => 'detect',
        ];
    }

    public function detect(PatternDiscoveryEvent $event)
    {
        $pattern = $event->getPattern();
        $collection = $event->getCollection();

        if ($pattern instanceof TwigPattern) {
            $included = $this->inspector->inspectLinked($pattern->getTwig(), $pattern->getSource());
            foreach ($included as $name) {
                if ($collection->has($name)) {
                    $pattern->addUsedPattern($collection->get($name));
                }
            }
        }
    }
}
