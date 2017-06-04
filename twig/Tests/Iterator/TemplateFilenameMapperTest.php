<?php


namespace LastCall\Mannequin\Twig\Tests\Iterator;


use LastCall\Mannequin\Twig\Iterator\TemplateFilenameMapper;
use PHPUnit\Framework\TestCase;

class TemplateFilenameMapperTest extends TestCase {

  public function testMapsFilenameToMainNamespace() {
    $iterator = new \ArrayIterator([__FILE__]);
    $mapper = new TemplateFilenameMapper($iterator);
    $mapper->addPath(__DIR__);
    $this->assertEquals([[basename(__FILE__)]], iterator_to_array($mapper));
  }

  public function testMapsFilenameToAlternateNamespace() {
    $iterator = new \ArrayIterator([__FILE__]);
    $mapper = new TemplateFilenameMapper($iterator);
    $mapper->addPath(__DIR__, 'alternate');
    $this->assertEquals([['@alternate/'.basename(__FILE__)]], iterator_to_array($mapper));
  }

  public function testMapsFilenameToAllNames() {
    $basename = basename(__FILE__);
    $iterator = new \ArrayIterator([__FILE__]);
    $mapper = new TemplateFilenameMapper($iterator);
    $mapper->addPath(__DIR__);
    $mapper->addPath(__DIR__, 'alternate');
    $this->assertEquals([
      [$basename, '@alternate/'.$basename]
    ], iterator_to_array($mapper));
  }
}