<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core;

use Assetic\AssetWriter;
use Assetic\Factory\AssetFactory;
use Assetic\Asset\AssetCollection;
use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Pattern\PatternVariant;
use LastCall\Mannequin\Core\Variable\VariableResolver;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PatternRenderer
{
    private $urlGenerator;
    private $resolver;
    private $assetFactory;
    private $engine;

    public function __construct(EngineInterface $engine, UrlGeneratorInterface $generator, VariableResolver $resolver, AssetFactory $assetFactory)
    {
        $this->resolver = $resolver;
        $this->urlGenerator = $generator;
        $this->assetFactory = $assetFactory;
        $this->engine = $engine;
    }

    public function render(PatternCollection $collection, PatternInterface $pattern, PatternVariant $variant): Rendered
    {
        $generator = $this->urlGenerator;
        $assets = new AssetCollection();
        $resolved = $this->resolver->resolve($variant->getVariables(), [
            'collection' => $collection,
            'resolver' => $this->resolver,
            'engine' => $this->engine,
            'pattern' => $pattern,
            'variant' => $variant,
            'assets' => $assets,
            'generator' => $generator,
        ]);

        $rendered = $this->engine->render($pattern, $resolved);
        if ($css = $rendered->getCss()) {
            $css = $this->assetFactory->createAsset($css, [], [
                'output' => 'css/*.css',
            ]);
            $cssUrl = $generator->generate(
                'static',
                ['name' => $css->getTargetPath()],
                UrlGeneratorInterface::RELATIVE_PATH
            );
            $rendered->setCss([$cssUrl]);
            $rendered->getAssets()->add($css);
        }

        if ($js = $rendered->getJs()) {
            $js = $this->assetFactory->createAsset($js, [], [
                'output' => 'js/*.js',
            ]);
            $jsUrl = $generator->generate(
                'static',
                ['name' => $js->getTargetPath()],
                UrlGeneratorInterface::RELATIVE_PATH
            );
            $rendered->setJs([$jsUrl]);
            $rendered->getAssets()->add($js);
        }
        foreach ($assets->all() as $asset) {
            $rendered->getAssets()->add($asset);
        }

        return $rendered;
    }

    public function renderSource(PatternInterface $pattern): string
    {
        return $this->engine->renderSource($pattern);
    }

    public function writeAssets(Rendered $rendered, string $assetDirectory)
    {
        $writer = new AssetWriter($assetDirectory);
        foreach ($rendered->getAssets()->all() as $asset) {
            $writer->writeAsset($asset);
        }
    }
}
