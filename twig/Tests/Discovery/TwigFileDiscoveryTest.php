<?php


namespace LastCall\Mannequin\Twig\Tests\Discovery;

use LastCall\Mannequin\Core\Metadata\MetadataFactoryInterface;
use LastCall\Mannequin\Twig\Discovery\TwigFileDiscovery;
use LastCall\Mannequin\Twig\Pattern\TwigPattern;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;

class TwigFileDiscoveryTest extends TestCase {

  const FIXTURES_DIR = __DIR__.'/../Resources';

  /**
   * @expectedException \InvalidArgumentException
   * @expectedExceptionMessage Twig loader must implement Twig_ExistsLoaderInterface
   */
  public function testConstructWithInvalidLoader1() {
    $loader = $this->prophesize(\Twig_LoaderInterface::class);
    $loader->willImplement(\Twig_SourceContextLoaderInterface::class);
    new TwigFileDiscovery($loader->reveal(), Finder::create());
  }

  /**
   * @expectedException \InvalidArgumentException
   * @expectedExceptionMessage Twig loader must implement \Twig_SourceContextLoaderInterface
   */
  public function testConstructWithInvalidLoader2() {
    $loader = $this->prophesize(\Twig_LoaderInterface::class);
    $loader->willImplement(\Twig_ExistsLoaderInterface::class);
    new TwigFileDiscovery($loader->reveal(), Finder::create());
  }

  private function discoverFixtureCollection() {
    $loader = new \Twig_Loader_Filesystem(self::FIXTURES_DIR);
    $finder = Finder::create()
      ->in([self::FIXTURES_DIR])
      ->files()
      ->name('form-input.twig');

    $discoverer = new TwigFileDiscovery($loader, $finder);
    return $discoverer->discover();
  }

  public function testSetsId() {
    $pattern = $this->discoverFixtureCollection()->get('dHdpZzovL2Zvcm0taW5wdXQudHdpZw==');
    $this->assertInstanceOf(TwigPattern::class, $pattern);
    $this->assertEquals('dHdpZzovL2Zvcm0taW5wdXQudHdpZw==', $pattern->getId());
  }

  public function testSetsAliases() {
    $pattern = $this->discoverFixtureCollection()->get('dHdpZzovL2Zvcm0taW5wdXQudHdpZw==');
    $this->assertEquals(['twig://form-input.twig'], $pattern->getAliases());
  }

  public function testSetsSource() {
    /** @var TwigPattern $pattern */
    $pattern = $this->discoverFixtureCollection()->get('dHdpZzovL2Zvcm0taW5wdXQudHdpZw==');
    $this->assertInstanceOf(\Twig_Source::class, $pattern->getSource());
    $source = $pattern->getSource();
    $this->assertEquals('form-input.twig', $source->getName());
    $this->assertEquals(realpath(self::FIXTURES_DIR .'/form-input.twig'), $source->getPath());
    $this->assertContains('<input', $source->getCode());
  }

  /**
   * @expectedException \LastCall\Mannequin\Core\Exception\UnsupportedPatternException
   * @expectedExceptionMessage Unable to load TwigFileDiscoveryTest.php
   */
  public function testThrowsExceptionOnNonLoadableFiles() {
    $loader = new \Twig_Loader_Filesystem(self::FIXTURES_DIR);
    $finder = Finder::create()
      ->in([__DIR__])
      ->files()
      ->name(basename(__FILE__));
    $discoverer = new TwigFileDiscovery($loader, $finder);
    $discoverer->discover();
  }
}