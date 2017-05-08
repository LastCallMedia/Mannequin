<?php


namespace LastCall\Patterns\Core\Tests\Parser;


use LastCall\Patterns\Core\Parser\HtmlTemplateParser;
use LastCall\Patterns\Core\Pattern\HtmlPattern;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class HtmlParserTest extends TestCase {

  public function getSupportsTests() {
    return [
      ['/test/pattern.html', TRUE],
      ['/test/pattern.php', FALSE]
    ];
  }

  /**
   * @dataProvider getSupportsTests
   */
  public function testSupports($filename, $expected) {
    $info = new SplFileInfo($filename, $filename, $filename);
    $this->assertEquals($expected, (new HtmlTemplateParser())->supports($info));
  }

  public function getParseTests() {
    $patterns[] = new HtmlPattern('pattern', 'Pattern', '/test/pattern.html');

    $tests = [];
    foreach($patterns as $pattern) {
      $tests[] = [$pattern->getFilename(), $pattern];
    }

    return [
      ['/test/pattern.html', 'pattern', 'Pattern']
    ];
  }

  /**
   * @dataProvider getParseTests
   */
  public function testParse($filename, $expectedId, $expectedName) {
    $info = new SplFileInfo($filename, $filename, $filename);
    $pattern = (new HtmlTemplateParser())->parse($info);
    $this->assertInstanceOf(HtmlPattern::class, $pattern);
    $this->assertEquals($filename, $pattern->getFilename());
    $this->assertEquals($expectedId, $pattern->getId());
    $this->assertEquals($expectedName, $pattern->getName());
  }
}