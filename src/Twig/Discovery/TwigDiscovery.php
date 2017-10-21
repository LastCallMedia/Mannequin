<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Discovery;

use LastCall\Mannequin\Core\Component\BrokenComponent;
use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Twig\Driver\TwigDriverInterface;
use LastCall\Mannequin\Twig\Component\TwigComponent;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

/**
 * This class converts an iterable object of template names into TwigComponent
 * objects by using the Twig driver.
 */
class TwigDiscovery implements DiscoveryInterface, LoggerAwareInterface
{
    use IdEncoder;

    private $names;

    private $driver;

    private $logger;

    public function __construct(TwigDriverInterface $driver, $names)
    {
        $this->driver = $driver;
        if (!is_array($names) && !$names instanceof \Traversable) {
            throw new \InvalidArgumentException(
                '$names must be an array or a \Traversable object.'
            );
        }
        $this->names = $names;
        $this->logger = new NullLogger();
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * {@inheritdoc}
     */
    public function discover(): ComponentCollection
    {
        $twig = $this->driver->getTwig();
        $components = [];
        foreach ($this->names as $names) {
            $aliases = (array) $names;
            $name = reset($aliases);
            try {
                $component = new TwigComponent(
                    $this->encodeId($name),
                    $aliases,
                    $twig->load($name)->getSourceContext(),
                    $twig
                );
            } catch (\Twig_Error $e) {
                $this->logger->error('Twig error in {template}: {message}', ['template' => $name, 'message' => $e->getMessage()]);
                $component = new BrokenComponent(
                    $this->encodeId($name),
                    $aliases
                );
                $component->addProblem($e->getMessage());
            }
            $component->setName($name);
            $components[] = $component;
        }

        return new ComponentCollection($components);
    }
}
