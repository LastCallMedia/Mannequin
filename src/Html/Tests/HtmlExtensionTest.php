<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Html\Tests;

use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Tests\Extension\ExtensionTestCase;
use LastCall\Mannequin\Html\Discovery\HtmlDiscovery;
use LastCall\Mannequin\Html\HtmlExtension;
use Symfony\Component\Finder\Finder;

class HtmlExtensionTest extends ExtensionTestCase
{
    public function getExtension(): ExtensionInterface
    {
        return new HtmlExtension();
    }

    public function testCanBeCreatedWithFilesArray()
    {
        $extension = new HtmlExtension([
            'files' => glob(__DIR__.'/Resources/*.html'),
            'root' => __DIR__,
        ]);
        /** @var HtmlDiscovery $discoverer */
        foreach ($extension->getDiscoverers() as $discoverer) {
            $this->assertInstanceOf(HtmlDiscovery::class, $discoverer);
            $this->assertTrue($discoverer->discover()->has('Resources/button.html'));
        }
    }

    public function testCanBeCreatedWithFinder()
    {
        $extension = new HtmlExtension([
            'files' => Finder::create()->in(__DIR__.'/Resources')->name('*.html'),
            'root' => __DIR__,
        ]);
        /** @var HtmlDiscovery $discoverer */
        foreach ($extension->getDiscoverers() as $discoverer) {
            $this->assertInstanceOf(HtmlDiscovery::class, $discoverer);
            $this->assertTrue($discoverer->discover()->has('Resources/button.html'));
        }
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage HtmlExtension 'files' option must be set to an iterable object.
     */
    public function testThrowsExceptionOnInvalidFilesConfig()
    {
        new HtmlExtension(['files' => 'foo']);
    }

    /**
     * @expectedException \InvalidArgumentException
     * @expectedExceptionMessage HtmlExtension 'root' option must be set to a valid directory.
     */
    public function testThrowsExceptionOnInvalidRoot()
    {
        new HtmlExtension(['root' => __DIR__.'/nonexistant']);
    }
}
