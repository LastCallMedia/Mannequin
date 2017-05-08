<?php


namespace LastCall\Patterns\Twig\Tests\Parser;


use LastCall\Patterns\Core\Variable\ScalarType;
use LastCall\Patterns\Core\Variable\VariableFactory;
use LastCall\Patterns\Core\Variable\VariableSet;
use LastCall\Patterns\Twig\Parser\TwigParser;
use LastCall\Patterns\Twig\Pattern\TwigPattern;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\SplFileInfo;

class TwigParserTest extends TestCase {

  const TEMPLATE_DIR = __DIR__.'/../Resources';

  public function getSupportsTests() {
    return [
      ['twig-no-metadata.twig', TRUE],
      ['pattern.html', FALSE],
    ];
  }
  /**
   * @dataProvider getSupportsTests
   */
  public function testSupports($filename, $expected) {
    $twig = new \Twig_Environment(new \Twig_Loader_Filesystem([self::TEMPLATE_DIR]));
    $parser = new TwigParser($twig, new VariableFactory());
    $info = new SplFileInfo(self::TEMPLATE_DIR.DIRECTORY_SEPARATOR.$filename, $filename, $filename);
    $this->assertEquals($expected, $parser->supports($info));
  }

  public function testSupportsNonexistent() {
    $twig = new \Twig_Environment(new \Twig_Loader_Filesystem([self::TEMPLATE_DIR]));
    $parser = new TwigParser($twig, new VariableFactory());
    $info = new SplFileInfo(self::TEMPLATE_DIR . '/nonexistent.twig', 'nonexistent.twig', 'nonexistent.twig');
    $this->assertFalse($parser->supports($info));
  }

  public function getParseTests() {
    $p1 = new TwigPattern('twig-no-metadata.twig', 'Twig no metadata', 'twig-no-metadata.twig');

    $p2 = new TwigPattern('twig-with-metadata.twig', 'Twig with metadata', 'twig-with-metadata.twig');
    $p2->addTag('type', 'molecule');

    $p3 = new TwigPattern('twig-with-variables.twig', 'Twig with variables', 'twig-with-variables.twig', new VariableSet([
      'template_type' => new ScalarType('string', 'twig'),
      'local' => new ScalarType('boolean', TRUE),
      'global' => new ScalarType('boolean')
    ]));
    return [
      [$p1],
      [$p2],
      [$p3]
    ];
  }

  /**
   * @dataProvider getParseTests
   */
  public function testParseBasic(TwigPattern $expected) {
    $loader = new \Twig_Loader_Filesystem([self::TEMPLATE_DIR]);
    $twig = new \Twig_Environment($loader);
    $factory = new VariableFactory([ScalarType::class]);
    $parser = new TwigParser($twig, $factory);
    $file = new SplFileInfo(self::TEMPLATE_DIR . DIRECTORY_SEPARATOR . $expected->getFilename(), $expected->getFilename(), $expected->getFilename());
    $pattern = $parser->parse($file);
    $this->assertEquals($expected->getId(), $pattern->getId());
    $this->assertEquals($expected->getName(), $pattern->getName());
    $this->assertEquals($expected->getTags(), $pattern->getTags());
    $this->assertEquals($expected->getVariables(), $pattern->getVariables());
  }
}