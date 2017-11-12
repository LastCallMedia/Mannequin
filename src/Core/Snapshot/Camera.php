<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Snapshot;

use LastCall\Mannequin\Core\Asset\AssetManagerInterface;
use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Component\Sample;
use LastCall\Mannequin\Core\ComponentRenderer;
use LastCall\Mannequin\Core\Ui\ManifestBuilder;
use LastCall\Mannequin\Core\Ui\UiInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

/**
 * Snapshots a collection with all of its assets.
 *
 * Returns a snapshot based on a generator to avoid memory issues associated
 * with rendering many components and loading large assets.
 */
class Camera implements CameraInterface
{
    private $manifestBuilder;
    private $renderer;
    private $generator;
    private $ui;

    public function __construct(ManifestBuilder $manifestBuilder, ComponentRenderer $renderer, UrlGeneratorInterface $generator, UiInterface $ui, LoggerInterface $logger = null)
    {
        $this->manifestBuilder = $manifestBuilder;
        $this->renderer = $renderer;
        $this->generator = $generator;
        $this->ui = $ui;
        $this->logger = $logger ?: new NullLogger();
    }

    public function snapshot(ComponentCollection $collection, AssetManagerInterface $manager, callable $errorHandler = null): Snapshot
    {
        return new Snapshot($this->doSnapshot($collection, $manager, $errorHandler));
    }

    private function doSnapshot(ComponentCollection $collection, AssetManagerInterface $manager, callable $errHandler = null): \Traversable
    {
        yield $this->getManifest($collection);

        foreach ($this->ui->files() as $file) {
            yield $this->getAsset($file);
        }

        foreach ($collection as $component) {
            try {
                yield $this->getComponentSource($component);
                foreach ($component->getSamples() as $sample) {
                    // Reset the context every time.
                    // @todo: Find a more robust way of managing the path context.
                    $this->generator->getContext()->setPathInfo('/');
                    yield $this->getRenderedDecorated($collection, $component, $sample);
                    yield $this->getRenderedRaw($collection, $component, $sample);
                }
            } catch (\RuntimeException $e) {
                if ($errHandler) {
                    $errHandler($e, $component);
                } else {
                    throw $e;
                }
            }
        }
        foreach ($manager as $asset) {
            yield $this->getAsset($asset);
        }
    }

    private function getManifest(ComponentCollection $collection): SnapshotFile
    {
        $this->logger->debug('Snapshotting manifest');

        return new SnapshotFile(
            $this->generator->generate('manifest', [], UrlGeneratorInterface::RELATIVE_PATH),
            json_encode($this->manifestBuilder->generate($collection))
        );
    }

    private function getComponentSource(ComponentInterface $component): SnapshotFile
    {
        $this->logger->debug(sprintf('Snapshotting source for %s', $component->getName()));

        return new SnapshotFile(
            $this->generator->generate('component_render_source_raw', ['component' => $component->getId()], UrlGeneratorInterface::RELATIVE_PATH),
            $this->renderer->renderSource($component)
        );
    }

    private function getRenderedRaw(ComponentCollection $collection, ComponentInterface $component, Sample $sample): SnapshotFile
    {
        $this->logger->debug(sprintf('Snapshotting raw source for %s:%s', $component->getName(), $sample->getName()));
        $path = $this->generator->generate(
            'component_render_raw',
            ['component' => $component->getId(), 'sample' => $sample->getId()],
            UrlGeneratorInterface::RELATIVE_PATH
        );
        $rendered = $this->renderer->render($collection, $component, $sample);

        return new SnapshotFile(
            $path,
            $rendered->getMarkup()
        );
    }

    private function getRenderedDecorated(ComponentCollection $collection, ComponentInterface $component, Sample $sample): SnapshotFile
    {
        $this->logger->debug(sprintf('Snapshotting rendered for %s:%s', $component->getName(), $sample->getName()));
        $renderPath = $this->generator->generate(
            'component_render',
            ['component' => $component->getId(), 'sample' => $sample->getId()],
            UrlGeneratorInterface::RELATIVE_PATH
        );
        $rendered = $this->renderer->render($collection, $component, $sample);

        return new SnapshotFile(
            $renderPath,
            $this->ui->decorateRendered($rendered)
        );
    }

    private function getAsset(SplFileInfo $file)
    {
        $this->logger->debug(sprintf('Snapshotting asset %s', $file->getRelativePathname()));

        return SnapshotFile::fromFileInfo($file);
    }
}
