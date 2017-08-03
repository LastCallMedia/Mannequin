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
use LastCall\Mannequin\Core\Exception\TemplateParsingException;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use Psr\Log\LoggerInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Chain discovery.
 *
 * Run discovery for multiple discoverers, then execute a pattern discovery
 * event for each pattern that was found.
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
            try {
                $this->dispatcher->dispatch(
                    PatternEvents::DISCOVER,
                    new PatternDiscoveryEvent($pattern, $collection)
                );
            } catch (TemplateParsingException $e) {
                $pattern->addProblem($e->getMessage());
                if ($this->logger) {
                    $message = sprintf('Metadata error for %s. %s', $pattern->getName(), $e->getMessage());
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
