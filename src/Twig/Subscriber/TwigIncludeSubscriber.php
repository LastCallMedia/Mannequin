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

use LastCall\Mannequin\Core\Event\ComponentDiscoveryEvent;
use LastCall\Mannequin\Core\Event\ComponentEvents;
use LastCall\Mannequin\Core\Exception\TemplateParsingException;
use LastCall\Mannequin\Twig\Component\TwigComponent;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

/**
 * This subscriber enriches component data with usage information.
 *
 * Usage information is stored in a JSON encoded array in the _collected_usage
 * block. To automatically build this block, use the TwigUsageCollectorVisitor.
 *
 * @see \LastCall\Mannequin\Twig\Twig\TwigUsageCollectorVisitor
 */
class TwigIncludeSubscriber implements EventSubscriberInterface
{
    const BLOCK_NAME = '_collected_usage';

    public static function getSubscribedEvents()
    {
        return [
            ComponentEvents::DISCOVER => 'detect',
        ];
    }

    public function detect(ComponentDiscoveryEvent $event)
    {
        $component = $event->getComponent();

        if ($component instanceof TwigComponent) {
            try {
                $template = $component->getTwig()->load($component->getSource()->getName());
                if ($template->hasBlock(self::BLOCK_NAME)) {
                    $collection = $event->getCollection();
                    $used = json_decode($template->renderBlock(self::BLOCK_NAME));
                    foreach ($used as $name) {
                        if ($collection->has($name)) {
                            $component->addUsedComponent($collection->get($name));
                        }
                    }
                }
            } catch (\Twig_Error $e) {
                $message = sprintf('Twig error thrown during usage checking of %s: %s',
                    $component->getSource()->getName(),
                    $e->getMessage()
                );
                throw new TemplateParsingException($message, $e->getCode(), $e);
            }
        }
    }
}
