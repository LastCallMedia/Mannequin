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

use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Event\ComponentDiscoveryEvent;
use LastCall\Mannequin\Core\Event\ComponentEvents;
use LastCall\Mannequin\Core\Exception\TemplateParsingException;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Chain discovery.
 *
 * Run discovery for multiple discoverers, then execute a component discovery
 * event for each component that was found.
 */
class ChainDiscovery implements DiscoveryInterface
{
    private $discoverers = [];

    private $dispatcher;

    private $logger;

    public function __construct(
        array $discoverers = [],
        EventDispatcherInterface $dispatcher,
        LoggerInterface $logger = null
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
        $this->logger = $logger;
    }

    public function discover(): ComponentCollection
    {
        $components = [];
        foreach ($this->discoverers as $discoverer) {
            foreach ($discoverer->discover() as $component) {
                $components[] = $component;
            }
        }
        $collection = new ComponentCollection($components);
        foreach ($collection as $component) {
            try {
                $this->dispatcher->dispatch(
                    ComponentEvents::DISCOVER,
                    new ComponentDiscoveryEvent($component, $collection)
                );
            } catch (TemplateParsingException $e) {
                $component->addProblem($e->getMessage());
                if ($this->logger) {
                    $message = sprintf('Metadata error for %s. %s', $component->getName(), $e->getMessage());
                    $this->logger->error($message, [
                        'exception' => $e,
                    ]);
                }
                // Swallow the error message now that it's been noted.
                // We don't want the rest of discovery to be blocked because
                // of one bad template.
            }
        }

        return $collection;
    }
}
