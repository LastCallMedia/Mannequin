<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Discovery;

use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ChainDiscovery implements DiscoveryInterface
{
    private $discoverers = [];

    private $dispatcher;

    public function __construct(
        array $discoverers = [],
        EventDispatcherInterface $dispatcher
    ) {
        foreach ($discoverers as $discoverer) {
            if (!$discoverer instanceof DiscoveryInterface) {
                throw new \InvalidArgumentException(
                    sprintf(
                        'Discoverer must implement %s',
                        DiscoveryInterface::class
                    )
                );
            }
            $this->discoverers[] = $discoverer;
        }
        $this->dispatcher = $dispatcher;
    }

    public function discover(): PatternCollection
    {
        $patterns = [];
        foreach ($this->discoverers as $discoverer) {
            foreach ($discoverer->discover() as $pattern) {
                $patterns[] = $pattern;
            }
        }
        $collection = new PatternCollection($patterns);
        foreach ($collection as $pattern) {
            $this->dispatcher->dispatch(
                PatternEvents::DISCOVER,
                new PatternDiscoveryEvent($pattern, $collection)
            );
        }

        return $collection;
    }
}
