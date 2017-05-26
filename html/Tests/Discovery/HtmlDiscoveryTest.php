<?php


namespace LastCall\Mannequin\Html\Tests\Discovery;


use LastCall\Mannequin\Core\Event\PatternDiscoveryEvent;
use LastCall\Mannequin\Core\Metadata\MetadataFactoryInterface;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Variable\VariableSet;
use LastCall\Mannequin\Html\Discovery\HtmlDiscovery;
use LastCall\Mannequin\Html\Pattern\HtmlPattern;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class HtmlDiscoveryTest extends TestCase {

  const FIXTURES_DIR = __DIR__.'/../Resources';

  private function getFixturesDir($subdir = NULL) {
    return $subdir ? __DIR__.'/../Resources/' . $subdir : __DIR__.'/../Resources';
  }

  private function mockMetadataFactory($metadata = FALSE) {
    $metadataFactory = $this->prophesize(MetadataFactoryInterface::class);
    $patternArg = Argument::type(HtmlPattern::class);
    if(!$metadata) {
      $metadataFactory->hasMetadata($patternArg)->willReturn(FALSE);
      $metadataFactory->getMetadata($patternArg)->willThrow(new \Exception());
    }
    else {
      $metadataFactory->hasMetadata($patternArg)->willReturn(TRUE);
      $metadataFactory->getMetadata($patternArg)->willReturn($metadata);
    }
    return $metadataFactory;
  }

  public function testReturnsCollectionOnEmpty() {
    $dispatcher = new EventDispatcher();
    $finder = Finder::create()
      ->in($this->getFixturesDir('null'));
    $discovery = new HtmlDiscovery($finder, $dispatcher);
    $collection = $discovery->discover();
    $this->assertInstanceOf(PatternCollection::class, $collection);
    $this->assertCount(0, $collection);
  }

  public function testDiscoversPatternsInDir() {
    $finder = Finder::create()
      ->files()
      ->name('*.html')
      ->in($this->getFixturesDir());
    $discovery = new HtmlDiscovery($finder, new EventDispatcher());
    $collection = $discovery->discover();
    $this->assertInstanceOf(PatternCollection::class, $collection);
    $this->assertCount(1, $collection);
  }

  public function testSetsPropertiesOnDiscoveredPatterns() {
    $finder = Finder::create()
      ->files()
      ->name('*.html')
      ->in($this->getFixturesDir());
    $discovery = new HtmlDiscovery($finder, new EventDispatcher());
    $pattern = $discovery->discover()->get('html://foo.html');
    $this->assertInstanceOf(HtmlPattern::class, $pattern);
    $this->assertEquals('aHRtbDovL2Zvby5odG1s', $pattern->getId());
    $this->assertEquals(['html://foo.html'], $pattern->getAliases());
    $this->assertInstanceOf(SplFileInfo::class, $pattern->getFile());
    $this->assertEquals('foo.html', $pattern->getFile()->getRelativePathname());
  }

  public function testFiresDiscoverEvent() {
    $finder = Finder::create()
      ->files()
      ->name('*.html')
      ->in($this->getFixturesDir());
    $dispatcher = $this->prophesize(EventDispatcherInterface::class);
    $dispatcher
      ->dispatch('pattern.discover', Argument::type(PatternDiscoveryEvent::class))
      ->shouldBeCalled();
    $discovery = new HtmlDiscovery($finder, $dispatcher->reveal());
    $discovery->discover();
  }
}