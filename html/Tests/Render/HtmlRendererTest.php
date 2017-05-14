<?php


namespace LastCall\Patterns\Html\Tests\Render;


use LastCall\Patterns\Html\Pattern\HtmlPattern;
use LastCall\Patterns\Html\Render\HtmlRenderer;
use PHPUnit\Framework\TestCase;

class HtmlRendererTest extends TestCase {

  public function testRendersPattern() {
    $filename = __DIR__.'/../Resources/foo.html';
    $renderer = new HtmlRenderer(['foostyle'], ['fooscript']);
    $pattern = new HtmlPattern('foo', 'bar', new \SplFileInfo($filename));
    $rendered = $renderer->render($pattern);
    $this->assertEquals(['fooscript'], $rendered->getScripts());
    $this->assertEquals(['foostyle'], $rendered->getStyles());
    $this->assertEquals(file_get_contents($filename), $rendered->getMarkup());
    $this->assertEquals($pattern, $rendered->getPattern());
  }
}