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

use LastCall\Mannequin\Core\Engine\BrokenEngine;
use LastCall\Mannequin\Core\ExpressionLanguage\CoreExpressionLanguageProvider;
use LastCall\Mannequin\Core\Subscriber\GlobalAssetSubscriber;
use LastCall\Mannequin\Core\Subscriber\LastChanceNameSubscriber;
use LastCall\Mannequin\Core\Subscriber\VariableResolverSubscriber;
use LastCall\Mannequin\Core\Subscriber\YamlFileMetadataSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class CoreExtension extends AbstractExtension implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        // This is kind of a weird way to provide functions, but it lets us keep
        // those functions elsewhere so they can be tested.
        $provider = new CoreExpressionLanguageProvider();

        return $provider->getFunctions();
    }

    public function getEngines(): array
    {
        return [
            new BrokenEngine(),
        ];
    }

    public function subscribe(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber(new YamlFileMetadataSubscriber($this->mannequin->getMetadataParser()));
        $dispatcher->addSubscriber(new LastChanceNameSubscriber());
        $dispatcher->addSubscriber(new GlobalAssetSubscriber(
            $this->mannequin->getAssetPackage(),
            $this->mannequin->getConfig()->getGlobalCss(),
            $this->mannequin->getConfig()->getGlobalJs()
        ));
        $dispatcher->addSubscriber(new VariableResolverSubscriber($this->mannequin->getVariableResolver(), $this->mannequin));
    }
}
