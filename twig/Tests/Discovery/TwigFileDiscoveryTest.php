<?php


namespace LastCall\Mannequin\Twig\Tests\Discovery;

use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Event\PatternEvents;
use LastCall\Mannequin\Core\Metadata\MetadataFactoryInterface;
use LastCall\Mannequin\Twig\Discovery\TwigFileDiscovery;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\Finder\Finder;

class TwigFileDiscoveryTest extends TestCase {

  const FIXTURES_DIR = __DIR__.'/../Resources';

  public function getTestCases() {
    $p1 = new TwigPattern('dHdpZzovL3R3aWctbm8tbWV0YWRhdGEudHdpZw==', ['twig://twig-no-metadata.twig'], new \Twig_Source('', 'twig-no-metadata.twig', 'twig-no-metadata.twig'));
    return [
      [$p1],
    ];
  }

  public function testDiscoversPatterns() {
    $loader = new \Twig_Loader_Filesystem(self::FIXTURES_DIR);
    $finder = Finder::create()
      ->in(self::FIXTURES_DIR)
      ->files('twig-no-metadata.twig');

    $discoverer = new TwigFileDiscovery($loader, $finder, new EventDispatcher());
    $pattern = $discoverer->discover()->get('twig://twig-no-metadata.twig');
    $this->assertEquals('dHdpZzovL3R3aWctbm8tbWV0YWRhdGEudHdpZw==', $pattern->getId());
    $this->assertEquals(['twig://twig-no-metadata.twig'], $pattern->getAliases());
  }

  public function testFiresEvent() {
    $loader = new \Twig_Loader_Filesystem(self::FIXTURES_DIR);
    $finder = Finder::create()
      ->in(self::FIXTURES_DIR)
      ->files('twig-no-metadata.twig');

    $dispatcher = $this->prophesize(EventDispatcher::class);
    $dispatcher->dispatch(PatternEvents::DISCOVER, Argument::type(PatternDiscoveryEvent::class))
      ->shouldBeCalled();
    $discoverer = new TwigFileDiscovery($loader, $finder, $dispatcher->reveal());
    $discoverer->discover();
  }

  /**
   * @dataProvider getTestCases
   */
  public function testDiscover(TwigPattern $expected) {
    $loader = new \Twig_Loader_Filesystem(self::FIXTURES_DIR);
    $finder = new Finder();
    $finder->in([self::FIXTURES_DIR]);
    $finder->name($expected->getSource()->getPath());

    $discoverer = new TwigFileDiscovery($loader, $finder, new EventDispatcher());
    $patterns = $discoverer->discover();
    $pattern = $patterns->get($expected->getId());
    $this->assertEquals($expected->getId(), $pattern->getId());
    $this->assertEquals($expected->getAliases(), $pattern->getAliases());

  }
}