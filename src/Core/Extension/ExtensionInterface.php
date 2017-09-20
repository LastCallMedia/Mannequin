<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Extension;

use LastCall\Mannequin\Core\Mannequin;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

interface ExtensionInterface
{
    /**
     * Get the component discoverers provided by this extension.
     *
     * @return \LastCall\Mannequin\Core\Discovery\DiscoveryInterface[]
     */
    public function getDiscoverers(): array;

    /**
     * Get the component renderers provided by this extension.
     *
     * @return \LastCall\Mannequin\Core\Engine\EngineInterface[]
     */
    public function getEngines(): array;

    /**
     * Attach an extension's listeners/subscribers to a dispatcher.
     *
     * @param \Symfony\Component\EventDispatcher\EventDispatcherInterface $dispatcher
     */
    public function subscribe(EventDispatcherInterface $dispatcher);

    /**
     * Register the extension with the application.
     *
     * @param \LastCall\Mannequin\Core\Mannequin $application
     *
     * @return mixed
     */
    public function register(Mannequin $application);
}
