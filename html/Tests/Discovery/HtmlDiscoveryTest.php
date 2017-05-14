<?php


namespace LastCall\Patterns\Html\Tests\Discovery;


use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Html\Discovery\HtmlDiscovery;
use LastCall\Patterns\Html\Pattern\HtmlPattern;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class HtmlDiscoveryTest extends TestCase {

  const FIXTURES_DIR = __DIR__.'/../Resources';

  private function getFixturesDir($subdir = NULL) {
    return $subdir ? __DIR__.'/../Resources/' . $subdir : __DIR__.'/../Resources';
  }

  public function testReturnsCollectionOnEmpty() {
    $finder = new Finder();
    $finder->in([self::FIXTURES_DIR.'/null']);
    $discovery = new HtmlDiscovery($finder);
    $collection = $discovery->discover();
    $this->assertInstanceOf(PatternCollection::class, $collection);
    $this->assertCount(0, $collection);
  }

  public function testDiscoversPatternsInDir() {
    $finder = new Finder();
    $finder->in(self::FIXTURES_DIR);
    $discovery = new HtmlDiscovery($finder);
    $collection = $discovery->discover();
    $this->assertInstanceOf(PatternCollection::class, $collection);
    $this->assertCount(1, $collection);
  }

  public function testSetsPropertiesOnDiscoveredPatterns() {
    $finder = new Finder();
    $finder->in($this->getFixturesDir());
    $discovery = new HtmlDiscovery($finder);
    $pattern = $discovery->discover()->get('foo');
    $this->assertInstanceOf(HtmlPattern::class, $pattern);
    $this->assertEquals('foo', $pattern->getId());
    $this->assertEquals('Foo', $pattern->getName());
  }

  public function testSetsFileInfoOnDiscoveredPatterns() {
    $finder = new Finder();
    $finder->in($this->getFixturesDir());
    $discovery = new HtmlDiscovery($finder);
    $pattern = $discovery->discover()->get('foo');
    $info = $pattern->getFileInfo();
    $this->assertInstanceOf(SplFileInfo::class, $info);
    $this->assertEquals('foo.html', $info->getRelativePathname());
  }

}