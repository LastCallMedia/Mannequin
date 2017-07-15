<?php

namespace LastCall\Mannequin\Core\Tests\Pattern;

use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class PatternCollectionTest extends TestCase
{
    public function testIsIterator()
    {
        $collection = new PatternCollection();
        $this->assertInstanceOf('Iterator', $collection);
    }

    public function testIsCountable()
    {
        $collection = new PatternCollection();
        $this->assertInstanceOf('Countable', $collection);
        $this->assertEquals(0, $collection->count());
    }

    public function testDefaultIdName()
    {
        $collection = new PatternCollection();
        $this->assertEquals('__root__', $collection->getId());
    }

    public function testGetParent()
    {
        $collection = new PatternCollection();
        $this->assertEquals(null, $collection->getParent());
    }

    public function testIteration()
    {
        $pattern1 = $this->getPattern('foo', 'bar');
        $pattern2 = $this->getPattern('bar', 'baz');

        $collection = new PatternCollection([$pattern1, $pattern2]);
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

    private function getPattern($id, $name, array $tags = [], $aliases = [])
    {
        $pattern = $this->prophesize(PatternInterface::class);
        $pattern->getId()->willReturn($id);
        $pattern->getName()->willReturn($name);

        $pattern->getTags()->willReturn($tags);
        $pattern->hasTag(Argument::type('string'), Argument::type('string'))
            ->will(
                function ($args) use ($tags) {
                    list($type, $value) = $args;

                    return isset($tags[$type]) && $tags[$type] === $value;
                }
            );
        $pattern->getAliases()->willReturn($aliases);

        return $pattern->reveal();
    }

    public function testGet()
    {
        $pattern = $this->getPattern('foo', 'bar');
        $collection = new PatternCollection([$pattern]);
        $this->assertEquals($pattern, $collection->get('foo'));
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Unknown pattern bar
     */
    public function testGetInvalid()
    {
        $pattern = $this->getPattern('foo', 'bar');
        $collection = new PatternCollection([$pattern]);
        $this->assertEquals($pattern, $collection->get('bar'));
    }

    public function testGetByAlias()
    {
        $pattern = $this->getPattern('foo', 'bar', [], ['baz']);
        $collection = new PatternCollection([$pattern]);
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
        new PatternCollection($patterns);
    }

    /**
     * @expectedException \RuntimeException
     * @expectedExceptionMessage Duplicate pattern detected: foo
     */
    public function testCreateWithDuplicatePatterns()
    {
        $pattern1 = $this->getPattern('foo', 'bar');
        $pattern2 = $this->getPattern('foo', 'baz');
        new PatternCollection([$pattern1, $pattern2]);
    }

    public function testGetTags()
    {
        $pattern1 = $this->getPattern(
            'foo',
            'Foo',
            ['type' => 'element', 'size' => 'large', 'smell' => 'bad']
        );
        $pattern2 = $this->getPattern(
            'bar',
            'Bar',
            ['type' => 'atom', 'smell' => 'bad']
        );
        $collection = new PatternCollection([$pattern1, $pattern2]);
        $this->assertEquals(
            [
                'type' => ['element', 'atom'],
                'size' => ['large'],
                'smell' => ['bad'],
            ],
            $collection->getTags()
        );
    }

    public function testWithTag()
    {
        $pattern = $this->getPattern('foo', 'bar', ['type' => 'element']);
        $collection = new PatternCollection([$pattern]);
        $tagCollection = $collection->withTag('type', 'element');
        $this->assertEquals(1, $tagCollection->count());
        $this->assertEquals('tag:type:element', $tagCollection->getId());
        $this->assertEquals($collection, $tagCollection->getParent());
    }

    public function testWithTagEmpty()
    {
        $collection = new PatternCollection();
        $subCollection = $collection->withTag('type', 'element');
        $this->assertInstanceOf(PatternCollection::class, $subCollection);
        $this->assertCount(0, $subCollection);
    }

    public function testMergeMergesPatterns()
    {
        $pattern1 = $this->getPattern('foo', 'bar');
        $pattern2 = $this->getPattern('bar', 'baz');
        $collection1 = new PatternCollection([$pattern1]);
        $collection2 = new PatternCollection([$pattern2]);
        $merged = $collection1->merge($collection2);
        $this->assertEquals([$pattern1, $pattern2], $merged->getPatterns());
    }

    public function testMergeKeepsNameAndId()
    {
        $collection1 = new PatternCollection([], 'collection1', 'Collection 1');
        $collection2 = new PatternCollection([], 'collection2', 'Collection 2');
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
        $collection1 = new PatternCollection([$pattern1]);
        $collection2 = new PatternCollection([$pattern2]);
        $merged = $collection1->merge($collection2);
        $this->assertEquals([$pattern1, $pattern2], $merged->getPatterns());
    }
}
