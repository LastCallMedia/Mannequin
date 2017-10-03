<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests;

use LastCall\Mannequin\Core\Mannequin;
use LastCall\Mannequin\Core\Console\Application as ConsoleApplication;
use LastCall\Mannequin\Core\Console\Command\SnapshotCommand;
use LastCall\Mannequin\Core\Console\Command\StartCommand;
use LastCall\Mannequin\Core\Ui\Controller\ManifestController;
use LastCall\Mannequin\Core\Ui\Controller\RenderController;
use LastCall\Mannequin\Core\Ui\Controller\StaticFileController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class MannequinTest extends TestCase
{
    public function testHasExpectedRoutes()
    {
        $application = new Mannequin();
        $application->flush();
        /** @var \Symfony\Component\Routing\RouteCollection $routes */
        $routes = $application['routes'];
        $this->assertEquals('controller.static:indexAction', $routes->get('index')->getDefault('_controller'));
        $this->assertEquals('controller.manifest:getManifestAction', $routes->get('manifest')->getDefault('_controller'));
        $this->assertEquals('controller.render:renderAction', $routes->get('component_render')->getDefault('_controller'));
        $this->assertEquals('controller.render:renderSourceAction', $routes->get('component_render_source_raw')->getDefault('_controller'));
        $this->assertEquals('controller.render:renderRawAction', $routes->get('component_render_raw')->getDefault('_controller'));
        $this->assertEquals('controller.static:staticAction', $routes->get('static')->getDefault('_controller'));
    }

    public function testHasConsole()
    {
        $application = new Mannequin([
            'config_file' => __DIR__.'/Resources/bare-config.php',
            'autoload_path' => __FILE__,
        ]);
        $console = $application->getConsole();
        $this->assertInstanceOf(ConsoleApplication::class, $console);
        $this->assertInstanceOf(SnapshotCommand::class, $console->get('snapshot'));
        $this->assertInstanceOf(StartCommand::class, $console->get('start'));
    }

    public function testHasManifestController()
    {
        $application = new Mannequin([
            'config_file' => __DIR__.'/Resources/bare-config.php',
        ]);
        $this->assertInstanceOf(ManifestController::class, $application['controller.manifest']);
    }

    public function testHasRenderController()
    {
        $application = new Mannequin([
            'config_file' => __DIR__.'/Resources/bare-config.php',
        ]);
        $this->assertInstanceOf(RenderController::class, $application['controller.render']);
    }

    public function testHasStaticController()
    {
        $application = new Mannequin([
            'config_file' => __DIR__.'/Resources/bare-config.php',
        ]);
        $this->assertInstanceOf(StaticFileController::class, $application['controller.static']);
    }

    public function testResolvesConfig()
    {
        $application = new Mannequin([
            'config_file' => __DIR__.'/Resources/bare-config.php',
        ]);
        $this->assertEquals('bare-config', $application['config']['name']);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Expected config in foo, but the file does not exist
     */
    public function testThrowsForNonexistentConfigFile()
    {
        $application = new Mannequin([
            'config_file' => 'foo',
        ]);
        $application['config'];
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Config was not returned from
     */
    public function testThrowsForNonReturningConfigFile()
    {
        $application = new Mannequin([
            'config_file' => __DIR__.'/Resources/nonreturning-config.php',
        ]);
        $application['config'];
    }

    public function testHasCacheDirectory()
    {
        $application = new Mannequin([
            'config_file' => 'foo',
        ]);
        $this->assertEquals(sys_get_temp_dir().'/mannequin/'.md5('foo'), $application->getCacheDir());
    }

    public function testHasCache()
    {
        $application = new Mannequin([
            'config_file' => 'foo',
        ]);
        $this->assertEquals(
            new FilesystemAdapter('', 0, $application->getCacheDir().'/cache'),
            $application->getCache()
        );
    }
}
