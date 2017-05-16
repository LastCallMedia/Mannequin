<?php


namespace LastCall\Mannequin\Core\Tests\Metadata;


use LastCall\Mannequin\Core\Metadata\ChainMetadataFactory;
use LastCall\Mannequin\Core\Metadata\MetadataFactoryInterface;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Variable\VariableSet;
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
      'tags' => ['foo' => 'bar', 'type' => 'atom'],
      'variables' => new VariableSet(),
    ]);

    $f2 = $this->prophesize(MetadataFactoryInterface::class);
    $f2->hasMetadata($pattern)->willReturn(TRUE);
    $f2->getMetadata($pattern)->willReturn([
      'name' => 'bar',
      'tags' => ['bar' => 'baz', 'type' => 'molecule'],
      'variables' => new VariableSet(),
    ]);

    $factory = new ChainMetadataFactory([$f1->reveal(), $f2->reveal()]);
    $metadata = $factory->getMetadata($pattern->reveal());
    $this->assertEquals([
      'name' => 'bar',
      'tags' => ['foo' => 'bar', 'bar' => 'baz', 'type' => 'molecule'],
      'variables' => new VariableSet(),
    ], $metadata);
  }
}