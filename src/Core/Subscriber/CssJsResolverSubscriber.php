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

use Assetic\Factory\AssetFactory;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Event\RenderEvent;
use LastCall\Mannequin\Core\Rendered;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CssJsResolverSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $generator;

    public static function getSubscribedEvents()
    {
        return [
            PatternEvents::POST_RENDER => [
                ['bubbleFromVariables'],
                ['resolve'],
            ],
        ];
    }

    public function __construct(AssetFactory $factory, UrlGeneratorInterface $generator)
    {
        $this->factory = $factory;
        $this->generator = $generator;
    }

    public function bubbleFromVariables(RenderEvent $event)
    {
        $rendered = $event->getRendered();
        $css = $rendered->getCss();
        $js = $rendered->getJs();
        $this->recurseVars($event->getVariables(), function (Rendered $child) use (&$css, &$js) {
            $css = array_merge($css, $child->getCss());
            $js = array_merge($js, $child->getJs());
        });
        $rendered->setCss($css);
        $rendered->setJs($js);
    }

    public function resolve(RenderEvent $event)
    {
        // Only go through the resolving process if this is
        // the root render.  Other assets should be bubbled
        // up to us.
        if ($event->isRoot()) {
            $rendered = $event->getRendered();
            if ($css = $rendered->getCss()) {
                $asset = $this->factory->createAsset($css, [], [
                    'output' => 'css/*.css',
                ]);
                $url = $this->generator->generate(
                    'static',
                    ['name' => $asset->getTargetPath()],
                    UrlGeneratorInterface::RELATIVE_PATH);
                $rendered->setCss([$url]);
                $rendered->getAssets()->add($asset);
            }
            if ($js = $rendered->getJs()) {
                $asset = $this->factory->createAsset($js, [], [
                    'output' => 'js/*.js',
                ]);
                $url = $this->generator->generate(
                    'static',
                    ['name' => $asset->getTargetPath()],
                    UrlGeneratorInterface::RELATIVE_PATH);
                $rendered->setJs([$url]);
                $rendered->getAssets()->add($asset);
            }
        }
    }

    private function recurseVars(array $variables, $cb)
    {
        foreach ($variables as $variable) {
            if (is_array($variable)) {
                $this->recurseVars($variable, $cb);
            } elseif ($variable instanceof Rendered) {
                $cb($variable);
            }
        }
    }
}
