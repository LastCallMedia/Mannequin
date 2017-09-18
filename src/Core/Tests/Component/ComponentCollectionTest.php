<?php

/*
 * This file is part of Mannequin.
 *
 * (c) 2017 Last Call Media, Rob Bayliss <rob@lastcallmedia.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LastCall\Mannequin\Core\Tests\Component;

use LastCall\Mannequin\Core\Component\ComponentCollection;
use PHPUnit\Framework\TestCase;

class ComponentCollectionTest extends TestCase
{
    public function testIsIterator()
    {
        $collection = new ComponentCollection();
        $this->assertInstanceOf('Iterator', $collection);
    }

    public function testIsCountable()
    {
        $collection = new ComponentCollection();
        $this->assertInstanceOf('Countable', $collection);
        $this->assertEquals(0, $collection->count());
    }

    public function testDefaultIdName()
    {
        $collection = new ComponentCollection();
        $this->assertEquals('__root__', $collection->getId());
    }

    public function testGetParent()
    {
        $collection = new \LastCall\Mannequin\Core\Component\ComponentCollection();
        $this->assertEquals(null, $collection->getParent());
    }

    public function testIteration()
    {
        $pattern1 = $this->getPattern('foo', 'bar');
        $pattern2 = $this->getPattern('bar', 'baz');

        $collection = new \LastCall\Mannequin\Core\Component\ComponentCollection([$pattern1, $pattern2]);
        $patterns = [];
        foreach ($collection as $pattern) {
            $patterns[] = $pattern;
        }
        $this->assertEquals([$pattern1, $pattern2], $patterns);
        $collection->rewind();

        $patterns = [];
        foreach ($collection as $pattern) {
            $patterns[] = $pattern;
        }
        $this->assertEquals([$pattern1, $pattern2], $patterns);
    }

    private function getPattern($id, $name, $aliases = [])
    {
        $pattern = $this->prophesize(
            \LastCall\Mannequin\Core\Component\ComponentInterface::class);
        $pattern->getId()->willReturn($id);
        $pattern->getName()->willReturn($name);
        $pattern->getAliases()->willReturn($aliases);

        return $pattern->reveal();
    }

    public function testGet()
    {
        $pattern = $this->getPattern('foo', 'bar');
        $collection = new ComponentCollection([$pattern]);
        $this->assertEquals($pattern, $collection->get('foo'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unknown pattern bar
     */
    public function testGetInvalid()
    {
        $pattern = $this->getPattern('foo', 'bar');
        $collection = new ComponentCollection([$pattern]);
        $this->assertEquals($pattern, $collection->get('bar'));
    }

    public function testGetByAlias()
    {
        $pattern = $this->getPattern('foo', 'bar', ['baz']);
        $collection = new ComponentCollection([$pattern]);
        $this->assertEquals($pattern, $collection->get('baz'));
    }

    public function getInvalidPatterns()
    {
        return [
            [['foo']],
            [[new \stdClass()]],
        ];
    }

    /**
     * @dataProvider getInvalidPatterns
     * @expectedException \RuntimeException
     */
    public function testCreateWithInvalidPatterns(array $patterns)
    {
        new ComponentCollection($patterns);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Duplicate pattern detected: foo
     */
    public function testCreateWithDuplicatePatterns()
    {
        $pattern1 = $this->getPattern('foo', 'bar');
        $pattern2 = $this->getPattern('foo', 'baz');
        new ComponentCollection([$pattern1, $pattern2]);
    }

    public function testMergeMergesPatterns()
    {
        $pattern1 = $this->getPattern('foo', 'bar');
        $pattern2 = $this->getPattern('bar', 'baz');
        $collection1 = new ComponentCollection([$pattern1]);
        $collection2 = new ComponentCollection([$pattern2]);
        $merged = $collection1->merge($collection2);
        $this->assertEquals([$pattern1, $pattern2], $merged->getPatterns());
    }

    public function testMergeKeepsNameAndId()
    {
        $collection1 = new ComponentCollection([], 'collection1', 'Collection 1');
        $collection2 = new ComponentCollection([], 'collection2', 'Collection 2');
        $merged = $collection1->merge($collection2);
        $this->assertEquals('collection1', $merged->getId());
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Merging these collections would result in the
     *   following duplicate patterns: foo
     */
    public function testMergeMergesSamePatterns()
    {
        $pattern1 = $this->getPattern('foo', 'bar');
        $pattern2 = $this->getPattern('foo', 'baz');
        $collection1 = new ComponentCollection([$pattern1]);
        $collection2 = new ComponentCollection([$pattern2]);
        $merged = $collection1->merge($collection2);
        $this->assertEquals([$pattern1, $pattern2], $merged->getPatterns());
    }
}
