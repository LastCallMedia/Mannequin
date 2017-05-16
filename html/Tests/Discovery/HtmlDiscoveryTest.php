<?php


namespace LastCall\Mannequin\Html\Tests\Discovery;


use LastCall\Mannequin\Core\Metadata\MetadataFactoryInterface;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Variable\VariableSet;
use LastCall\Mannequin\Html\Discovery\HtmlDiscovery;
use LastCall\Mannequin\Html\Pattern\HtmlPattern;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
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
    $metadataFactory = $this->mockMetadataFactory();
    $finder = new Finder();
    $finder->in([self::FIXTURES_DIR.'/null']);
    $discovery = new HtmlDiscovery($finder, $metadataFactory->reveal());
    $collection = $discovery->discover();
    $this->assertInstanceOf(PatternCollection::class, $collection);
    $this->assertCount(0, $collection);
  }

  public function testDiscoversPatternsInDir() {
    $metadataFactory = $this->mockMetadataFactory();
    $finder = new Finder();
    $finder
      ->in(self::FIXTURES_DIR)
      ->name('*.html')
      ->files();
    $discovery = new HtmlDiscovery($finder, $metadataFactory->reveal());
    $collection = $discovery->discover();
    $this->assertInstanceOf(PatternCollection::class, $collection);
    $this->assertCount(1, $collection);
  }

  public function testSetsPropertiesOnDiscoveredPatterns() {
    $metadataFactory = $this->mockMetadataFactory([
      'name' => 'Foo',
      'tags' => [],
      'variables' => new VariableSet(),
    ]);
    $finder = new Finder();
    $finder->in($this->getFixturesDir());
    $discovery = new HtmlDiscovery($finder, $metadataFactory->reveal());
    $pattern = $discovery->discover()->get('html://foo.html');
    $this->assertInstanceOf(HtmlPattern::class, $pattern);
    $this->assertEquals('html://foo.html', $pattern->getId());
    $this->assertEquals('Foo', $pattern->getName());
  }

  public function testSetsFileInfoOnDiscoveredPatterns() {
    $metadataFactory = $this->mockMetadataFactory();
    $finder = new Finder();
    $finder->in($this->getFixturesDir());
    $discovery = new HtmlDiscovery($finder, $metadataFactory->reveal());
    $pattern = $discovery->discover()->get('html://foo.html');
    $info = $pattern->getFile();
    $this->assertInstanceOf(SplFileInfo::class, $info);
    $this->assertEquals('foo.html', $info->getRelativePathname());
  }

}