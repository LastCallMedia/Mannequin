<?php


namespace LastCall\Patterns\Core\Tests\Metadata;


use LastCall\Patterns\Core\Metadata\ChainMetadataFactory;
use LastCall\Patterns\Core\Metadata\MetadataFactoryInterface;
use LastCall\Patterns\Core\Pattern\PatternInterface;
use LastCall\Patterns\Core\Variable\VariableSet;
use PHPUnit\Framework\TestCase;

class ChainMetadataFactoryTest extends TestCase {

  public function testHasMetadata() {
    $pattern = $this->prophesize(PatternInterface::class);
    $f1 = $this->prophesize(MetadataFactoryInterface::class);
    $f1->hasMetadata($pattern)->willReturn(FALSE);

    $f2 = $this->prophesize(MetadataFactoryInterface::class);
    $f2->hasMetadata($pattern)->willReturn(TRUE);

    $negativeFactory = new ChainMetadataFactory([$f1->reveal()]);
    $this->assertFalse($negativeFactory->hasMetadata($pattern->reveal()));

    $positiveFactory = new ChainMetadataFactory([$f1->reveal(), $f2->reveal()]);
    $this->assertTrue($positiveFactory->hasMetadata($pattern->reveal()));
  }

  public function testGetMetadata() {
    $pattern = $this->prophesize(PatternInterface::class);

    $f1 = $this->prophesize(MetadataFactoryInterface::class);
    $f1->hasMetadata($pattern)->willReturn(TRUE);
    $f1->getMetadata($pattern)->willReturn([
      'name' => 'foo',
      'tags' => ['foo' => 'bar'],
      'variables' => new VariableSet(),
    ]);

    $f2 = $this->prophesize(MetadataFactoryInterface::class);
    $f2->hasMetadata($pattern)->willReturn(TRUE);
    $f2->getMetadata($pattern)->willReturn([
      'name' => 'bar',
      'tags' => ['bar' => 'baz'],
      'variables' => new VariableSet(),
    ]);

    $factory = new ChainMetadataFactory([$f1->reveal(), $f2->reveal()]);
    $metadata = $factory->getMetadata($pattern->reveal());
    $this->assertEquals([
      'name' => 'bar',
      'tags' => ['foo' => 'bar', 'bar' => 'baz'],
      'variables' => new VariableSet(),
    ], $metadata);
  }
}