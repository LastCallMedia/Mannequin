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

use LastCall\Mannequin\Twig\TwigLoaderIterator;
use PHPUnit\Framework\TestCase;

class TwigLoaderIteratorTest extends TestCase
{
    public function testSearchesMainNamespaceWithWildcard()
    {
        $loader = new \Twig_Loader_Filesystem(['Resources'], __DIR__);
        $iterator = new TwigLoaderIterator($loader, __DIR__, ['*.twig']);
        $this->assertContains('form-input.twig', $iterator);
    }

    public function testSearchesAltNamespaceWithNsWildcard()
    {
        $loader = new \Twig_Loader_Filesystem(['Discovery'], __DIR__);
        $loader->setPaths(['Resources'], 'test');
        $iterator = new TwigLoaderIterator($loader, __DIR__, ['@*/*.twig']);
        $this->assertContains('@test/form-input.twig', $iterator);
    }

    public function testOnlySearchesMainNamespaceWithWildcard()
    {
        $loader = new \Twig_Loader_Filesystem(['Discovery'], __DIR__);
        $loader->setPaths(['Resources'], 'test');
        $iterator = new TwigLoaderIterator($loader, __DIR__, ['*.twig']);
        $this->assertEmpty(iterator_to_array($iterator));
    }

    public function testFilenameGlobNarrowsResultsWithoutNsGlob()
    {
        $loader = new \Twig_Loader_Filesystem(['Resources'], __DIR__);
        $iterator = new TwigLoaderIterator($loader, __DIR__, ['form-input.twig']);
        $this->assertContains('form-input.twig', $iterator);
        $this->assertCount(1, $iterator);
    }

    public function testFilenameGlobNarrowsResultsWithNsGlob()
    {
        $loader = new \Twig_Loader_Filesystem(['Discovery'], __DIR__);
        $loader->setPaths(['Resources'], 'test');
        $iterator = new TwigLoaderIterator($loader, __DIR__, ['@test/form-input.twig']);
        $this->assertContains('@test/form-input.twig', $iterator);
        $this->assertCount(1, $iterator);
    }
}
