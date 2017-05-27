<?php

namespace LastCall\Mannequin\Html\Tests\Render;

use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Render\RendererInterface;
use LastCall\Mannequin\Core\Tests\Render\RendererTestCase;
use LastCall\Mannequin\Html\Pattern\HtmlPattern;
use LastCall\Mannequin\Html\Render\HtmlRenderer;

class HtmlRendererTest extends RendererTestCase {

  public function getSupportedPattern(): PatternInterface {
    return new HtmlPattern('foo', [], new \SplFileInfo(__DIR__.'/../Resources/foo.html'));
  }

  public function getRenderer(): RendererInterface {
    return new HtmlRenderer(['foo'], ['bar']);
  }

  public function testRender() {
    $rendered = parent::testRender();
    $this->assertEquals(['foo'], $rendered->getStyles());
    $this->assertEquals(['bar'], $rendered->getScripts());
  }

}