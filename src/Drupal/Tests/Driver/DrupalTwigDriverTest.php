<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Drupal\Tests\Driver;

use LastCall\Mannequin\Drupal\Driver\DrupalTwigDriver;
use LastCall\Mannequin\Drupal\Drupal\MannequinExtensionDiscovery;
use LastCall\Mannequin\Drupal\Tests\UsesTestDrupalRoot;
use LastCall\Mannequin\Twig\Driver\TwigDriverInterface;
use LastCall\Mannequin\Twig\Tests\Driver\DriverTestCase;

/**
 * This test is very slow because of the extension scanning.
 */
class DrupalTwigDriverTest extends DriverTestCase
{
    use UsesTestDrupalRoot;

    public static function setUpBeforeClass()
    {
        self::requireDrupalClasses();
    }

    protected function getDriver(): TwigDriverInterface
    {
        $discovery = new MannequinExtensionDiscovery($this->getDrupalRoot());

        return new DrupalTwigDriver($this->getDrupalRoot(), $discovery, [], [
            'foo' => ['core/misc'],
        ]);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage does not look like a Drupal installation
     */
    public function testCreationWithInvalidDrupalRootFails()
    {
        $discovery = new MannequinExtensionDiscovery(__DIR__);
        new DrupalTwigDriver(__DIR__, $discovery);
    }

    public function testHasTemplateNameMapper()
    {
        $mapper = parent::testHasTemplateNameMapper();
        $details = new \SplFileInfo($this->getDrupalRoot().'/core/modules/system/templates/details.html.twig');
        $this->assertEquals(['@system/details.html.twig'], $mapper($details));

        return $mapper;
    }

    /**
     * @depends testHasTemplateNameMapper
     */
    public function testCanAddAdditionalNamespaces(callable $mapper)
    {
        $bar = new \SplFileInfo($this->getDrupalRoot().'/core/misc/bar');
        $this->assertEquals(['@foo/bar'], $mapper($bar));
    }

    public function testUsesFilesystemLoader()
    {
        $discovery = new MannequinExtensionDiscovery($this->getDrupalRoot());
        $driver = new DrupalTwigDriver($this->getDrupalRoot(), $discovery);
        $loader = $driver->getTwig()->getLoader();
        $this->assertInstanceOf(\Twig_Loader_Filesystem::class, $loader, 'Without fallback extensions specified, the filesystem loader should be used directly.');
    }

    public function testUsesFallbackLoaderWhenFallbacksAreSpecified()
    {
        $discovery = new MannequinExtensionDiscovery($this->getDrupalRoot());
        $driver = new DrupalTwigDriver($this->getDrupalRoot(), $discovery, [], [], ['classy']);
        /** @var \Twig_Loader_Chain $loader */
        $loader = $driver->getTwig()->getLoader();
        $this->assertInstanceOf(\Twig_Loader_Chain::class, $loader, 'With fallback extensions, a Chain loader should be used.');
        $this->assertTrue($loader->exists('block.html.twig'));
    }
}
