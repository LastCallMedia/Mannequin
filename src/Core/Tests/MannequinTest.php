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

use LastCall\Mannequin\Core\Config\ConfigInterface;
use LastCall\Mannequin\Core\Console\Command\DebugCommand;
use LastCall\Mannequin\Core\Mannequin;
use LastCall\Mannequin\Core\Console\Command\SnapshotCommand;
use LastCall\Mannequin\Core\Console\Command\StartCommand;
use LastCall\Mannequin\Core\MannequinConfig;
use LastCall\Mannequin\Core\Ui\Controller\ManifestController;
use LastCall\Mannequin\Core\Ui\Controller\RenderController;
use LastCall\Mannequin\Core\Ui\Controller\StaticFileController;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

class MannequinTest extends TestCase
{
    public function testHasExpectedRoutes()
    {
        $application = new Mannequin(new MannequinConfig());
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

    public function testHasCommands()
    {
        $application = new Mannequin(new MannequinConfig());
        $commands = $application['commands'];
        $names = [];
        foreach ($commands as $command) {
            $names[$command->getName()] = get_class($command);
        }
        $this->assertEquals([
            'start' => StartCommand::class,
            'snapshot' => SnapshotCommand::class,
            'debug' => DebugCommand::class,
        ], $names);
    }

    public function testHasManifestController()
    {
        $application = new Mannequin(new MannequinConfig());
        $this->assertInstanceOf(ManifestController::class, $application['controller.manifest']);
    }

    public function testHasRenderController()
    {
        $application = new Mannequin(new MannequinConfig());
        $this->assertInstanceOf(RenderController::class, $application['controller.render']);
    }

    public function testHasStaticController()
    {
        $application = new Mannequin(new MannequinConfig());
        $this->assertInstanceOf(StaticFileController::class, $application['controller.static']);
    }

    public function testHasConfig()
    {
        $config = new MannequinConfig();
        $application = new Mannequin($config);
        $this->assertSame($config, $application->getConfig());
    }

    public function testHasCacheDirectory()
    {
        $config = $this->prophesize(ConfigInterface::class);
        $config->getCid()->willReturn('foo');
        $application = new Mannequin($config->reveal());
        $this->assertEquals(sys_get_temp_dir().'/mannequin/foo', $application->getCacheDir());
    }

    public function testHasCache()
    {
        $config = $this->prophesize(ConfigInterface::class);
        $config->getCid()->willReturn('foo');
        $application = new Mannequin($config->reveal());
        $this->assertEquals(
            new FilesystemAdapter('', 0, $application->getCacheDir().'/cache'),
            $application->getCache()
        );
    }
}
