<?php


namespace LastCall\Patterns\Twig\Tests\Parser;


use LastCall\Patterns\Twig\Parser\TwigParser;
use LastCall\Patterns\Twig\Pattern\TwigPattern;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class TwigParserTest extends TestCase {

  const TEMPLATE_DIR = __DIR__.'/../Resources';

  public function getSupportsTests() {
    return [
      ['pattern.twig', TRUE],
      ['pattern.html', FALSE],
    ];
  }
  /**
   * @dataProvider getSupportsTests
   */
  public function testSupports($filename, $expected) {
    $twig = new \Twig_Environment(new \Twig_Loader_Filesystem([self::TEMPLATE_DIR]));
    $parser = new TwigParser($twig);
    $info = new SplFileInfo(self::TEMPLATE_DIR.DIRECTORY_SEPARATOR.$filename, $filename, $filename);
    $this->assertEquals($expected, $parser->supports($info));
  }

  public function testSupportsNonexistent() {
    $twig = new \Twig_Environment(new \Twig_Loader_Filesystem([self::TEMPLATE_DIR]));
    $parser = new TwigParser($twig);
    $info = new SplFileInfo(self::TEMPLATE_DIR . '/nonexistent.twig', 'nonexistent.twig', 'nonexistent.twig');
    $this->assertFalse($parser->supports($info));
  }

  public function getParseTests() {
    $p1 = new TwigPattern('with-metadata.twig', 'Template with metadata', 'with-metadata.twig');
    $p1->addTag('type', 'molecule');

    $p2 = new TwigPattern('no-metadata.twig', 'No metadata', 'no-metadata.twig');
    return [
      [$p1],
      [$p2]
    ];
  }

  /**
   * @dataProvider getParseTests
   */
  public function testParseBasic(TwigPattern $expected) {
    $loader = new \Twig_Loader_Filesystem([self::TEMPLATE_DIR]);
    $twig = new \Twig_Environment($loader);
    $parser = new TwigParser($twig);
    $file = new SplFileInfo(self::TEMPLATE_DIR . DIRECTORY_SEPARATOR . $expected->getFilename(), $expected->getFilename(), $expected->getFilename());
    $pattern = $parser->parse($file);
    $this->assertEquals($expected->getId(), $pattern->getId());
    $this->assertEquals($expected->getName(), $pattern->getName());
    $this->assertEquals($expected->getTags(), $pattern->getTags());
  }
}