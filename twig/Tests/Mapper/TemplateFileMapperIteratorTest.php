<?php


namespace LastCall\Mannequin\Twig\Tests\Mapper;


use LastCall\Mannequin\Twig\Mapper\TemplateFileMapperIterator;
use PHPUnit\Framework\TestCase;

class TemplateFileMapperIteratorTest extends TestCase {

  public function testInvokesCallback() {
    $iterator = new \ArrayIterator([__FILE__]);
    $callback = function($filename) {
      return $filename . '.foo';
    };
    $mapper = new TemplateFileMapperIterator($iterator, $callback);
    $this->assertEquals([
      __FILE__.'.foo',
    ], iterator_to_array($mapper));
  }

  public function testPassesThroughSplFileInfo() {
    $fileInfo = new \SplFileInfo(__FILE__);
    $iterator = new \ArrayIterator([$fileInfo]);
    $callback = function($passedFileInfo) use ($fileInfo) {
      $this->assertSame($fileInfo, $passedFileInfo);
      return 'foo';
    };
    $mapper = new TemplateFileMapperIterator($iterator, $callback);
    $this->assertEquals([
      'foo',
    ], iterator_to_array($mapper));
  }
}