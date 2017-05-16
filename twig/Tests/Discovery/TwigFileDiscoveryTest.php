<?php


namespace LastCall\Mannequin\Twig\Tests\Discovery;

use LastCall\Mannequin\Core\Metadata\MetadataFactoryInterface;
use LastCall\Mannequin\Core\Variable\VariableFactory;
use LastCall\Mannequin\Twig\Discovery\TwigFileDiscovery;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\Finder\Finder;
use LastCall\Mannequin\Core\Variable\ScalarFactory;

class TwigFileDiscoveryTest extends TestCase {

  const FIXTURES_DIR = __DIR__.'/../Resources';

  public function getTestCases() {
    $p1 = new TwigPattern('twig://twig-no-metadata.twig', new \Twig_Source('', 'twig-no-metadata.twig', 'twig-no-metadata.twig'));
    return [
      [$p1],
    ];
  }

  /**
   * @dataProvider getTestCases
   */
  public function testDiscover(TwigPattern $expected) {
    $loader = new \Twig_Loader_Filesystem(self::FIXTURES_DIR);
    $factory = new VariableFactory([], [new ScalarFactory()]);
    $metadata = $this->prophesize(MetadataFactoryInterface::class);
    $metadata->hasMetadata(Argument::type(TwigPattern::class))->willReturn(FALSE);

    $finder = new Finder();
    $finder->in([self::FIXTURES_DIR]);
    $finder->name($expected->getSource()->getPath());

    $discoverer = new TwigFileDiscovery($loader, $finder, $metadata->reveal());
    $patterns = $discoverer->discover();
    $pattern = $patterns->get($expected->getId());
    $this->assertEquals($expected->getId(), $pattern->getId());
    $this->assertEquals($expected->getName(), $pattern->getName());
    $this->assertEquals($expected->getTags(), $pattern->getTags());
    $this->assertEquals($expected->getVariables(), $pattern->getVariables());

  }
}