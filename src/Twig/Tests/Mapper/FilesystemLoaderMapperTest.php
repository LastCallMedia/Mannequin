<?php


namespace LastCall\Mannequin\Twig\Tests\Mapper;


use LastCall\Mannequin\Twig\Mapper\FilesystemLoaderMapper;
use PHPUnit\Framework\TestCase;

class FilesystemLoaderMapperTest extends TestCase {

  public function testMapsFilenameToMainNamespace() {
    $mapper = new \LastCall\Mannequin\Twig\Mapper\FilesystemLoaderMapper();
    $mapper->addPath(__DIR__);
    $this->assertEquals([basename(__FILE__)], $mapper(__FILE__));
  }

  public function testMapsFilenameToAlternateNamespace() {
    $mapper = new \LastCall\Mannequin\Twig\Mapper\FilesystemLoaderMapper();
    $mapper->addPath(__DIR__, 'alternate');
    $this->assertEquals(['@alternate/'.basename(__FILE__)], $mapper(__FILE__));
  }

  public function testMapsFilenameToAllNames() {
    $basename = basename(__FILE__);
    $mapper = new FilesystemLoaderMapper();
    $mapper->addPath(__DIR__);
    $mapper->addPath(__DIR__, 'alternate');

    $this->assertEquals([
      $basename,
      '@alternate/' . $basename
    ], $mapper(__FILE__));
  }
}