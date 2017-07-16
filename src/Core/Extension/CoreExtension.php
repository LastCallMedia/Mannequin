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

use LastCall\Mannequin\Core\Subscriber\LastChanceNameSubscriber;
use LastCall\Mannequin\Core\Subscriber\NestedPatternVariableSubscriber;
use LastCall\Mannequin\Core\Subscriber\YamlFileMetadataSubscriber;
use LastCall\Mannequin\Core\Variable\PatternResolver;
use LastCall\Mannequin\Core\Variable\ScalarResolver;
use LastCall\Mannequin\Core\Variable\Set;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CoreExtension extends AbstractExtension
{
    public function getVariableResolvers(): array
    {
        return [
            new ScalarResolver(),
            new PatternResolver(
                function ($id, Set $set = null) {
                    $pattern = $this->getConfig()->getCollection()->get($id);
                    $set = $set ?: $pattern->getVariableSets()['default'];

                    return $this->getConfig()->getRenderer()->render(
                        $pattern,
                        $set
                    );
                }
            ),
        ];
    }

    public function attachToDispatcher(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber(new YamlFileMetadataSubscriber());
        $dispatcher->addSubscriber(new NestedPatternVariableSubscriber());
        $dispatcher->addSubscriber(new LastChanceNameSubscriber());
    }
}
