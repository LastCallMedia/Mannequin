<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Snapshot;

use LastCall\Mannequin\Core\Asset\AssetManager;
use LastCall\Mannequin\Core\Component\ComponentCollection;
use LastCall\Mannequin\Core\Component\ComponentInterface;
use LastCall\Mannequin\Core\Component\Sample;
use LastCall\Mannequin\Core\ComponentRenderer;
use LastCall\Mannequin\Core\Rendered;
use LastCall\Mannequin\Core\Snapshot\Camera;
use LastCall\Mannequin\Core\Snapshot\SnapshotFile;
use LastCall\Mannequin\Core\Ui\ManifestBuilder;
use LastCall\Mannequin\Core\Ui\UiInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Finder\SplFileInfo;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Routing\RequestContext;

class CameraTest extends TestCase
{
    private function getManifestBuilder()
    {
        $builder = $this->prophesize(ManifestBuilder::class);
        $builder
            ->generate(Argument::type(ComponentCollection::class))
            ->will(function ($args) {
                $ret = [];
                foreach ($args[0] as $component) {
                    $ret[] = $component->getId();
                }

                return $ret;
            });

        return $builder->reveal();
    }

    private function getGenerator()
    {
        $generator = $this->prophesize(UrlGeneratorInterface::class);
        $generator
            ->generate(Argument::type('string'), Argument::any(), UrlGeneratorInterface::RELATIVE_PATH)->will(function ($args) {
                $argString = '';
                foreach ($args[1] as $k => $v) {
                    $argString .= sprintf('/%s:%s', $k, $v);
                }

                return sprintf('/%s%s', $args[0], $argString);
            });

        $context = new RequestContext();
        $generator
            ->getContext()
            ->willReturn($context);

        return $generator->reveal();
    }

    private function getSampleComponent($componentId, array $sampleIds = []): ComponentInterface
    {
        $component = $this->prophesize(ComponentInterface::class);
        $component->getId()->willReturn($componentId);
        $component->getAliases()->willReturn([]);
        $component->getName()->willReturn($componentId);
        $samples = array_map(function ($id) {
            $sample = $this->prophesize(Sample::class);
            $sample->getId()->willReturn($id);
            $sample->getName()->willReturn($id);

            return $sample;
        }, $sampleIds);
        $component->getSamples()->willReturn($samples);

        return $component->reveal();
    }

    private function getRenderer()
    {
        $renderer = $this->prophesize(ComponentRenderer::class);
        $renderer
            ->render(Argument::type(ComponentCollection::class), Argument::type(ComponentInterface::class), Argument::type(Sample::class))
            ->will(function ($args) {
                $cid = $args[1]->getId();
                if ('exception' === $cid) {
                    throw new \RuntimeException('Some Exception');
                }
                $rendered = new Rendered();
                $rendered->setMarkup(sprintf('rendered %s:%s', $cid, $args[2]->getId()));

                return $rendered;
            });
        $renderer
            ->renderSource(Argument::type(ComponentInterface::class))
            ->will(function ($args) {
                return sprintf('source %s', $args[0]->getId());
            });

        return $renderer->reveal();
    }

    private function getUi(\Traversable $files = null)
    {
        $ui = $this->prophesize(UiInterface::class);
        $ui
            ->decorateRendered(Argument::type(Rendered::class))
            ->will(function ($args) {
                return sprintf('decorated %s', $args[0]->getMarkup());
            });
        $ui
            ->files()
            ->willReturn($files ?: new \ArrayIterator([]));

        return $ui->reveal();
    }

    public function testAddsAssetsToSnapshot()
    {
        $am = new AssetManager(new \ArrayIterator([
            __FILE__,
        ]), __DIR__);
        $camera = new Camera(
            $this->getManifestBuilder(),
            $this->getRenderer(),
            $this->getGenerator(),
            $this->getUi()
        );
        $files = iterator_to_array($camera->snapshot(new ComponentCollection(), $am));

        $thisFile = new SplFileInfo(__FILE__, __DIR__, basename(__FILE__));
        $expected = [
            new SnapshotFile('/manifest', '[]'),
            SnapshotFile::fromFileInfo($thisFile),
        ];
        $this->assertEquals($expected, $files);
    }

    public function testAddsRenderedComponentToSnapshot()
    {
        $component = $this->getSampleComponent('foo', ['foo']);
        $collection = new ComponentCollection([$component]);

        $camera = new Camera($this->getManifestBuilder(), $this->getRenderer(), $this->getGenerator(), $this->getUi());
        $snapshot = $camera->snapshot($collection, new AssetManager(new \ArrayIterator([]), ''));
        $expected = [
            new SnapshotFile('/manifest', '["foo"]'),
            new SnapshotFile('/component_render_source_raw/component:foo', 'source foo'),
            new SnapshotFile('/component_render/component:foo/sample:foo', 'decorated rendered foo:foo'),
            new SnapshotFile('/component_render_raw/component:foo/sample:foo', 'rendered foo:foo'),
        ];
        $this->assertEquals($expected, iterator_to_array($snapshot));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Some Exception
     */
    public function testThrowsExceptionsDuringRendering()
    {
        $component = $this->getSampleComponent('exception', ['foo']);
        $collection = new ComponentCollection([$component]);

        $camera = new Camera(
            $this->getManifestBuilder(),
            $this->getRenderer(),
            $this->getGenerator(),
            $this->getUi()
        );
        $snapshot = $camera->snapshot($collection, new AssetManager(new \ArrayIterator([]), ''));
        iterator_to_array($snapshot);
    }

    public function testCanCatchRenderExceptions()
    {
        $exception = $this->getSampleComponent('exception', ['foo']);
        $noException = $this->getSampleComponent('foo', ['foo']);
        $collection = new ComponentCollection([$exception, $noException]);

        $camera = new Camera(
            $this->getManifestBuilder(),
            $this->getRenderer(),
            $this->getGenerator(),
            $this->getUi()
        );
        $am = new AssetManager(new \ArrayIterator([]), '');
        $thrown = false;
        $snapshot = $camera->snapshot($collection, $am, function () use (&$thrown) {
            $thrown = true;
        });
        $this->assertFalse($thrown, 'Error handler is not invoked until generator is iterated.');
        iterator_to_array($snapshot);
        $this->assertTrue($thrown, 'Error handler is invoked once generator is iterated.');
    }
}
