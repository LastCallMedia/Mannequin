<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\ExpressionLanguage;

use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\ComponentRenderer;
use LastCall\Mannequin\Core\Rendered;
use Symfony\Component\ExpressionLanguage\ExpressionFunction;
use Symfony\Component\ExpressionLanguage\ExpressionFunctionProviderInterface;

class CoreExpressionLanguageProvider implements ExpressionFunctionProviderInterface
{
    public function getFunctions()
    {
        return array(
            new ExpressionFunction('rendered', function ($value) {
                return sprintf('(new LastCall\\Mannequin\\Core\\Rendered())->setMarkup(%s)', $value);
            }, function ($variables, $value) {
                return (new Rendered())->setMarkup($value);
            }),
            new ExpressionFunction('asset', function ($arg) {
                return sprintf('$mannequin->getAssetPackage()->getUrl(%s)', $arg);
            }, function ($variables, $arg) {
                return $variables['mannequin']->getAssetPackage()->getUrl($arg);
            }),
            new ExpressionFunction('sample', function ($arg) {
                return sprintf(
                    'LastCall\Mannequin\Core\ExpressionLanguage::renderSample($collection, $mannequin->getRenderer(), %s)',
                    $arg
                );
            }, function ($variables, $arg) {
                return self::renderSample(
                    $variables['collection'],
                    $variables['mannequin']->getRenderer(),
                    $arg
                );
            }),
        );
    }

    public static function renderSample(ComponentCollection $collection, ComponentRenderer $renderer, $spec)
    {
        // @todo: Strengthen regex once ID requirements are set.
        if (1 !== mb_substr_count($spec, '#') || !preg_match('/.+#.+/', $spec)) {
            throw new \RuntimeException(sprintf('Invalid sample specification: %s', $spec));
        }
        list($componentId, $sampleId) = explode('#', $spec);
        $component = $collection->get($componentId);
        $sample = $component->getSample($sampleId);

        return $renderer->render($collection, $component, $sample);
    }
}
