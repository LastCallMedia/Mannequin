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
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class ApplicationTest extends TestCase
{
    public function testHasExpectedRoutes()
    {
        $application = new Application();
        $application->flush();
        /** @var \Symfony\Component\Routing\RouteCollection $routes */
        $routes = $application['routes'];
        $expectedRoutes = [
            'manifest',
            'pattern_render',
            'pattern_render_source_raw',
        ];
        foreach ($expectedRoutes as $name) {
            $this->assertInstanceOf('Symfony\Component\Routing\Route', $routes->get($name));
        }
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

    public function testServesManifest()
    {
        $application = new Application([
            'config_file' => __DIR__.'/Resources/bare-config.php',
        ]);
        $request = Request::create('/manifest.json');
        $response = $application->handle($request);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals('application/json', $response->headers->get('Content-Type'));
    }

    public function testServesRender()
    {
        $this->markTestIncomplete('This test does not work yet.  Fix 500 being thrown in missing pattern.');
        $application = new Application([
            'config_file' => __DIR__.'/Resources/bare-config.php',
            'debug' => true,
        ]);
        $request = Request::create('/m-render/abc/def.html');
        $response = $application->handle($request);
        $this->assertEquals(404, $response->getStatusCode());
    }
}
