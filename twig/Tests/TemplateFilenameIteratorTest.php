<?php


namespace LastCall\Mannequin\Twig\Tests;


use LastCall\Mannequin\Twig\TemplateFilenameIterator;
use PHPUnit\Framework\TestCase;

class TemplateFilenameIteratorTest extends TestCase {

  public function getMappingTests() {
    return [
      ['/foo/foo.html', [], 'foo.html'],
    ];
  }

  public function testMapsToMainNamespace() {
    $iterator = new \ArrayIterator(['/foo/foo.html']);
    $templateIterator = new TemplateFilenameIterator($iterator, ['/foo']);
    $this->assertEquals('foo.html', $templateIterator->current());
  }

  public function testMapsToOtherNamespace() {
    $iterator = new \ArrayIterator(['/bar/foo.html']);
    $templateIterator = new TemplateFilenameIterator($iterator, ['/foo']);
    $templateIterator->setPaths(['/bar'], 'bar');
    $this->assertEquals('@bar/foo.html', $templateIterator->current());
  }

  public function testTrimsSeparators() {
    $iterator = new \ArrayIterator(['/bar/foo.html']);
    $templateIterator = new TemplateFilenameIterator($iterator, ['/foo']);
    $templateIterator->setPaths(['/bar/'], 'bar');
    $this->assertEquals('@bar/foo.html', $templateIterator->current());
  }

  public function testAcceptsSplFileInfo() {
    $iterator = new \ArrayIterator([new \SplFileInfo('/foo/foo.html')]);
    $templateIterator = new TemplateFilenameIterator($iterator, ['/foo']);
    $this->assertEquals('foo.html', $templateIterator->current());
  }
}