<?php

namespace LastCall\Mannequin\Html\Tests\Engine;

use LastCall\Mannequin\Core\Engine\EngineInterface;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use LastCall\Mannequin\Core\Tests\Engine\RendererTestCase;
use LastCall\Mannequin\Html\Pattern\HtmlPattern;

class HtmlRendererTest extends RendererTestCase {

  public function getSupportedPattern(): PatternInterface {
    return new HtmlPattern('foo', [], new \SplFileInfo(__DIR__.'/../Resources/foo.html'));
  }

  public function getRenderer(): EngineInterface {
    return new \LastCall\Mannequin\Html\Engine\HtmlEngine(['foo'], ['bar']);
  }

  public function testRender() {
    $rendered = parent::testRender();
    $this->assertEquals(['foo'], $rendered->getStyles());
    $this->assertEquals(['bar'], $rendered->getScripts());
  }

}