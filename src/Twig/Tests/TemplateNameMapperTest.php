<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Twig\Tests;

use LastCall\Mannequin\Twig\TemplateNameMapper;
use PHPUnit\Framework\TestCase;

class TemplateNameMapperTest extends TestCase
{
    public function testIsCallable()
    {
        $mapper = new TemplateNameMapper(__DIR__);
        $this->assertInternalType('callable', $mapper);
    }

    public function testDoesNotAlterStrings()
    {
        $mapper = new TemplateNameMapper(__DIR__);
        $this->assertEquals('foo', $mapper('foo'));
    }

    public function testAltersFileInfos()
    {
        $fileinfo = new \SplFileInfo(__FILE__);
        $mapper = new TemplateNameMapper(__DIR__);
        $this->assertEquals([basename(__FILE__)], $mapper($fileinfo));
    }

    public function testMatchesOnRoot()
    {
        $mapper = new TemplateNameMapper(__DIR__);
        $name = $mapper->getTemplateNamesForFilename(__FILE__);
        $this->assertEquals([basename(__FILE__)], $name);
    }

    public function testMatchesOnRootInSubdirectory()
    {
        $mapper = new TemplateNameMapper(dirname(__DIR__));
        $name = $mapper->getTemplateNamesForFilename(__FILE__);
        $this->assertEquals([basename(__DIR__).'/'.basename(__FILE__)], $name);
    }

    public function testMatchesInNamespacedPath()
    {
        $mapper = new TemplateNameMapper(__DIR__.'/foo');
        $mapper->addNamespace('@test', __DIR__);
        $name = $mapper->getTemplateNamesForFilename(__FILE__);
        $this->assertEquals(['@test/'.basename(__FILE__)], $name);
    }

    public function testMatchesInMultipleNamespaces()
    {
        $mapper = new TemplateNameMapper(dirname(__DIR__));
        $mapper->addNamespace('@test', __DIR__);
        $names = $mapper->getTemplateNamesForFilename(__FILE__);
        $this->assertEquals([
            basename(__DIR__).'/'.basename(__FILE__),
            '@test/'.basename(__FILE__),
        ], $names);
    }
}
