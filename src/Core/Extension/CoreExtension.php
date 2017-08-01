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
use LastCall\Mannequin\Core\Subscriber\YamlFileMetadataSubscriber;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class CoreExtension extends AbstractExtension implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return [
            $this->getPatternExpressionFunction(),
        ];
    }

    public function attachToDispatcher(EventDispatcherInterface $dispatcher)
    {
        $dispatcher->addSubscriber(new YamlFileMetadataSubscriber($this->getConfig()->getMetadataParser()));
        $dispatcher->addSubscriber(new LastChanceNameSubscriber());
    }

    private function getPatternExpressionFunction()
    {
        return new ExpressionFunction('pattern', function ($arguments, $pid) {
            throw new \ErrorException('Pattern expressions cannot yet be compiled.');
        }, function ($context, $pid) {
            /** @var \LastCall\Mannequin\Core\Pattern\PatternCollection $collection */
            $collection = $context['collection'];
            $engine = $context['engine'];
            $resolver = $context['resolver'];

            $pattern = $collection->get($pid);
            $variant = reset($pattern->getVariants());
            $resolved = $resolver->resolve($variant->getVariables(), $context);

            return $engine->render($pattern, $resolved);
        });
    }
}
