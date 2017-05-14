<?php


namespace LastCall\Patterns\Twig\Tests\Discovery;

use LastCall\Patterns\Core\Variable\VariableFactory;
use LastCall\Patterns\Twig\Discovery\TwigFileDiscovery;
use LastCall\Patterns\Twig\Pattern\TwigPattern;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use LastCall\Patterns\Core\Variable\VariableSet;
use LastCall\Patterns\Core\Variable\ScalarType;
use LastCall\Patterns\Core\Variable\ScalarFactory;

class TwigFileDiscoveryTest extends TestCase {

  const FIXTURES_DIR = __DIR__.'/../Resources';

  public function getTestCases() {
    $p1 = new TwigPattern('twig-no-metadata.twig', 'Twig no metadata', 'twig-no-metadata.twig');
    $p1->addTag('renderer', 'twig');

    $p2 = new TwigPattern('twig-with-metadata.twig', 'Twig with metadata', 'twig-with-metadata.twig');
    $p2->addTag('type', 'atom');
    $p2->addTag('renderer', 'twig');

    $p3 = new TwigPattern('twig-with-variables.twig', 'Twig with variables', 'twig-with-variables.twig', new VariableSet([
      'template_type' => new ScalarType('string', 'twig'),
      'local' => new ScalarType('boolean', TRUE),
      'global' => new ScalarType('boolean')
    ]));
    $p3->addTag('type', 'molecule');
    $p3->addTag('renderer', 'twig');
    return [
      [$p1],
      [$p2],
      [$p3]
    ];
  }

  /**
   * @dataProvider getTestCases
   */
  public function testDiscover(TwigPattern $expected) {
    $loader = new \Twig_Loader_Filesystem(self::FIXTURES_DIR);
    $twig = new \Twig_Environment($loader);
    $factory = new VariableFactory([], [new ScalarFactory()]);

    $finder = new Finder();
    $finder->in([self::FIXTURES_DIR]);
    $finder->name($expected->getFilename());

    $discoverer = new TwigFileDiscovery($twig, $finder, $factory);
    $patterns = $discoverer->discover();
    $pattern = $patterns->get($expected->getId());
    $this->assertEquals($expected->getId(), $pattern->getId());
    $this->assertEquals($expected->getName(), $pattern->getName());
    $this->assertEquals($expected->getTags(), $pattern->getTags());
    $this->assertEquals($expected->getVariables(), $pattern->getVariables());

  }
}