<?php


namespace LastCall\Patterns\Twig\Tests\Parser;


use LastCall\Patterns\Twig\Parser\TwigParser;
use LastCall\Patterns\Twig\Pattern\TwigPattern;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class TwigParserTest extends TestCase {

  public function getSupportsTests() {
    return [
      [__DIR__.'/../Resources/pattern.twig', TRUE],
      [__DIR__.'/../Resources/pattern.html', FALSE],
    ];
  }
  /**
   * @dataProvider getSupportsTests
   */
  public function testSupports($filename, $expected) {
    $twig = new \Twig_Environment(new \Twig_Loader_Array());
    $parser = new TwigParser($twig);
    $info = new \SplFileInfo($filename);
    $this->assertEquals($expected, $parser->supports($info));
  }

  public function getParseTests() {
    $p1 = new TwigPattern('with-metadata.twig', 'Twig template with metadata');
    return [
      [__DIR__.'/../Resources/with-metadata.twig', $p1]
    ];
  }

  /**
   * @dataProvider getParseTests
   */
  public function testParseBasic($filename, $expectedPattern) {
    $loader = new \Twig_Loader_Filesystem([__DIR__.'/../Resources']);
    $twig = new \Twig_Environment($loader);
    $parser = new TwigParser($twig);
    new SplFileInfo();
    $pattern = $parser->parse(new \SplFileInfo($filename));
    $this->assertEquals($expectedPattern->getId(), $pattern->getId());
    $this->assertEquals($expectedPattern->getName(), $pattern->getName());
  }
}