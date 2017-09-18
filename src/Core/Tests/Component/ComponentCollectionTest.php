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
        $component1 = $this->getComponent('foo', 'bar');
        $component2 = $this->getComponent('bar', 'baz');

        $collection = new \LastCall\Mannequin\Core\Component\ComponentCollection([$component1, $component2]);
        $components = [];
        foreach ($collection as $component) {
            $components[] = $component;
        }
        $this->assertEquals([$component1, $component2], $components);
        $collection->rewind();

        $components = [];
        foreach ($collection as $component) {
            $components[] = $component;
        }
        $this->assertEquals([$component1, $component2], $components);
    }

    private function getComponent($id, $name, $aliases = [])
    {
        $component = $this->prophesize(
            \LastCall\Mannequin\Core\Component\ComponentInterface::class);
        $component->getId()->willReturn($id);
        $component->getName()->willReturn($name);
        $component->getAliases()->willReturn($aliases);

        return $component->reveal();
    }

    public function testGet()
    {
        $component = $this->getComponent('foo', 'bar');
        $collection = new ComponentCollection([$component]);
        $this->assertEquals($component, $collection->get('foo'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unknown component bar
     */
    public function testGetInvalid()
    {
        $component = $this->getComponent('foo', 'bar');
        $collection = new ComponentCollection([$component]);
        $this->assertEquals($component, $collection->get('bar'));
    }

    public function testGetByAlias()
    {
        $component = $this->getComponent('foo', 'bar', ['baz']);
        $collection = new ComponentCollection([$component]);
        $this->assertEquals($component, $collection->get('baz'));
    }

    public function getInvalidComponents()
    {
        return [
            [['foo']],
            [[new \stdClass()]],
        ];
    }

    /**
     * @dataProvider getInvalidComponents
     * @expectedException \RuntimeException
     */
    public function testCreateWithInvalidComponents(array $components)
    {
        new ComponentCollection($components);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Duplicate component detected: foo
     */
    public function testCreateWithDuplicateComponents()
    {
        $component1 = $this->getComponent('foo', 'bar');
        $component2 = $this->getComponent('foo', 'baz');
        new ComponentCollection([$component1, $component2]);
    }

    public function testMergeMergesComponents()
    {
        $component1 = $this->getComponent('foo', 'bar');
        $component2 = $this->getComponent('bar', 'baz');
        $collection1 = new ComponentCollection([$component1]);
        $collection2 = new ComponentCollection([$component2]);
        $merged = $collection1->merge($collection2);
        $this->assertEquals([$component1, $component2], $merged->getComponents());
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
     *   following duplicate components: foo
     */
    public function testMergeMergesSameComponents()
    {
        $component1 = $this->getComponent('foo', 'bar');
        $component2 = $this->getComponent('foo', 'baz');
        $collection1 = new ComponentCollection([$component1]);
        $collection2 = new ComponentCollection([$component2]);
        $merged = $collection1->merge($collection2);
        $this->assertEquals([$component1, $component2], $merged->getComponents());
    }
}
