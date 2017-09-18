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

use LastCall\Mannequin\Core\Component\TemplateFileInterface;
use LastCall\Mannequin\Core\Event\ComponentDiscoveryEvent;
use LastCall\Mannequin\Core\Event\ComponentEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

class LastChanceNameSubscriber implements EventSubscriberInterface
{
    public static function getSubscribedEvents()
    {
        return [
            ComponentEvents::DISCOVER => ['setComponentName', -100],
        ];
    }

    public function setComponentName(ComponentDiscoveryEvent $event)
    {
        $component = $event->getComponent();
        if (empty($component->getName())) {
            if ($component instanceof TemplateFileInterface) {
                if ($file = $component->getFile()) {
                    $name = explode('.', $file->getBasename())[0];
                    $name = ucfirst(
                        strtr(
                            trim($name, '-_.'),
                            [
                                '-' => ' ',
                                '_' => ' ',
                            ]
                        )
                    );
                    $component->setName($name);
                }
            } else {
                $component->setName($component->getId());
            }
        }
    }
}
