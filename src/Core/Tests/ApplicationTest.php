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

use LastCall\Mannequin\Core\Application;
use LastCall\Mannequin\Core\Console\Application as ConsoleApplication;
use LastCall\Mannequin\Core\Console\Command\RenderCommand;
use LastCall\Mannequin\Core\Console\Command\ServerCommand;
use LastCall\Mannequin\Core\Ui\Controller\ManifestController;
use LastCall\Mannequin\Core\Ui\Controller\RenderController;
use LastCall\Mannequin\Core\Ui\Controller\UiController;
use PHPUnit\Framework\TestCase;

class ApplicationTest extends TestCase
{
    public function testHasExpectedRoutes()
    {
        $application = new Application();
        $application->flush();
        /** @var \Symfony\Component\Routing\RouteCollection $routes */
        $routes = $application['routes'];
        $this->assertEquals('controller.manifest:getManifestAction', $routes->get('manifest')->getDefault('_controller'));
        $this->assertEquals('controller.render:renderAction', $routes->get('pattern_render')->getDefault('_controller'));
        $this->assertEquals('controller.render:renderSourceAction', $routes->get('pattern_render_source_raw')->getDefault('_controller'));
        $this->assertEquals('controller.render:renderRawAction', $routes->get('pattern_render_raw')->getDefault('_controller'));
        $this->assertEquals('controller.ui:staticAction', $routes->get('static')->getDefault('_controller'));
    }

    public function testHasConsole()
    {
        $application = new Application([
            'config_file' => __DIR__.'/Resources/bare-config.php',
            'autoload_path' => __FILE__,
        ]);
        $console = $application->getConsole();
        $this->assertInstanceOf(ConsoleApplication::class, $console);
        $this->assertInstanceOf(RenderCommand::class, $console->get('render'));
        $this->assertInstanceOf(ServerCommand::class, $console->get('server'));
    }

    public function testHasManifestController()
    {
        $application = new Application([
            'config_file' => __DIR__.'/Resources/bare-config.php',
        ]);
        $this->assertInstanceOf(ManifestController::class, $application['controller.manifest']);
    }

    public function testHasRenderController()
    {
        $application = new Application([
            'config_file' => __DIR__.'/Resources/bare-config.php',
        ]);
        $this->assertInstanceOf(RenderController::class, $application['controller.render']);
    }

    public function testHasUiController()
    {
        $application = new Application([
            'config_file' => __DIR__.'/Resources/bare-config.php',
        ]);
        $this->assertInstanceOf(UiController::class, $application['controller.ui']);
    }

    public function testResolvesConfig()
    {
        $application = new Application([
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
        $application = new Application([
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
        $application = new Application([
            'config_file' => __DIR__.'/Resources/nonreturning-config.php',
        ]);
        $application['config'];
    }
}
