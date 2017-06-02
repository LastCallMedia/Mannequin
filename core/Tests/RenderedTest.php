<?php

namespace LastCall\Mannequin\Core\Tests;

use LastCall\Mannequin\Core\Rendered;
use PHPUnit\Framework\TestCase;

class RenderedTest extends TestCase {

  public function testMarkup() {
    $rendered = new Rendered();
    $rendered->setMarkup('foo');
    $this->assertEquals('foo', $rendered->getMarkup());
    $this->assertEquals('foo', (string) $rendered);
  }

  public function testStyles() {
    $rendered = new Rendered();
    $rendered->setStyles(['foo']);
    $rendered->addStyles(['bar']);
    $this->assertEquals(['foo', 'bar'], $rendered->getStyles());
  }

  public function testScripts() {
    $rendered = new Rendered();
    $rendered->setScripts(['foo']);
    $rendered->addScripts(['bar']);
    $this->assertEquals(['foo', 'bar'], $rendered->getScripts());
  }

}