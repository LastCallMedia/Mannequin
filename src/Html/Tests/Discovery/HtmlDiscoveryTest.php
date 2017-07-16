<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Html\Tests\Discovery;

use LastCall\Mannequin\Core\Discovery\IdEncoder;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Html\Discovery\HtmlDiscovery;
use LastCall\Mannequin\Html\Pattern\HtmlPattern;
use PHPUnit\Framework\TestCase;

class HtmlDiscoveryTest extends TestCase
{
    use IdEncoder;

    public function testReturnsCollectionOnEmpty()
    {
        $discovery = new HtmlDiscovery(new \ArrayIterator([]));
        $collection = $discovery->discover();
        $this->assertInstanceOf(PatternCollection::class, $collection);
        $this->assertCount(0, $collection);
    }

    public function getCreatesPatternTests()
    {
        return [
            [['foo'], $this->encodeId('foo'), ['foo'], 'foo'],
            [[['foo', 'bar']], $this->encodeId('foo'), ['foo', 'bar'], 'foo'],
            [
                [new \SplFileInfo(__FILE__)],
                $this->encodeId(__FILE__),
                [__FILE__],
                __FILE__,
            ],
        ];
    }

    /**
     * @dataProvider getCreatesPatternTests
     */
    public function testCreatesPattern(
        $input,
        $expectedId,
        $expectedAliases,
        $expectedPathname
    ) {
        $discovery = new HtmlDiscovery(new \ArrayIterator($input));
        $collection = $discovery->discover();
        $this->assertInstanceOf(PatternCollection::class, $collection);
        $pattern = $collection->get($expectedId);
        $this->assertInstanceOf(HtmlPattern::class, $pattern);
        $this->assertEquals($expectedId, $pattern->getId());
        $this->assertEquals($expectedAliases, $pattern->getAliases());
        $this->assertInstanceOf(\SplFileInfo::class, $pattern->getFile());
        $this->assertEquals(
            $expectedPathname,
            $pattern->getFile()->getPathname()
        );
    }
}
