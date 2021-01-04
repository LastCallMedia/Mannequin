<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2020 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\EventListener;



use Pimple\Container;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

/**
 * Interface EventListenerProviderInterface
 * 
 */
interface EventListenerProviderInterface
{
    public function subscribe(Container $app, EventDispatcherInterface $dispatcher);
}
