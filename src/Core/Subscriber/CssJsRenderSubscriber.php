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
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CssJsRenderSubscriber implements EventSubscriberInterface
{
    private $factory;
    private $generator;

    public static function getSubscribedEvents()
    {
        return [
            PatternEvents::POST_RENDER => [['resolveCSS'], ['resolveJs']],
        ];
    }

    public function __construct(AssetFactory $factory, UrlGeneratorInterface $generator)
    {
        $this->factory = $factory;
        $this->generator = $generator;
    }

    public function resolveCss(RenderEvent $event)
    {
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
    }

    public function resolveJs(RenderEvent $event)
    {
        $rendered = $event->getRendered();
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
