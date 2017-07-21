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
    private function getFixturesDir()
    {
        return __DIR__.'/Resources';
    }

    public function testSearchesMainNamespaceWithWildcard()
    {
        $loader = new \Twig_Loader_Filesystem(['Resources'], __DIR__);
        $iterator = new TwigLoaderIterator($loader, __DIR__, ['*.twig']);
        $this->assertIteratorContains([['form-input.twig']], $iterator);
    }

    public function testSearchesAltNamespaceWithNsWildcard()
    {
        $loader = new \Twig_Loader_Filesystem(['Discovery'], __DIR__);
        $loader->setPaths(['Resources'], 'test');
        $iterator = new TwigLoaderIterator($loader, __DIR__, ['@*/*.twig']);
        $this->assertIteratorContains([['@test/form-input.twig']], $iterator);
    }

    public function testOnlySearchesMainNamespaceWithWildcard()
    {
        $loader = new \Twig_Loader_Filesystem(['Discovery'], __DIR__);
        $loader->setPaths(['Resources'], 'test');
        $iterator = new TwigLoaderIterator($loader, __DIR__, ['*.twig']);
        $this->assertEmpty(iterator_to_array($iterator));
    }

    private function assertIteratorContains(array $expected, $iterator)
    {
        $items = iterator_to_array($iterator);
        $this->assertArraySubset($expected, array_values($items));
    }
}
