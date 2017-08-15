<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Subscriber;

use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Event\RenderEvent;
use Symfony\Component\Asset\PackageInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class GlobalAssetSubscriber implements EventSubscriberInterface
{
    private $package;
    private $globalCss;
    private $globalJs;

    public static function getSubscribedEvents()
    {
        return [
            PatternEvents::PRE_RENDER => 'addGlobalAssets',
        ];
    }

    public function __construct(PackageInterface $package, array $globalCss, array $globalJs)
    {
        $this->package = $package;
        $this->globalCss = $globalCss;
        $this->globalJs = $globalJs;
    }

    public function addGlobalAssets(RenderEvent $event)
    {
        $rendered = $event->getRendered();
        $rendered->setCss(array_map([$this->package, 'getUrl'], $this->globalCss));
        $rendered->setJs(array_map([$this->package, 'getUrl'], $this->globalJs));
    }
}
