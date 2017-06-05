<?php


namespace LastCall\Mannequin\Html\Tests\Discovery;

use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Variable\VariableSet;
use LastCall\Mannequin\Html\Discovery\HtmlDiscovery;
use LastCall\Mannequin\Html\Pattern\HtmlPattern;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Finder\Finder;
use Symfony\Component\Finder\SplFileInfo;

class HtmlDiscoveryTest extends TestCase {

  const FIXTURES_DIR = __DIR__.'/../Resources';

  private function getFixturesDir($subdir = NULL) {
    return $subdir ? __DIR__.'/../Resources/' . $subdir : __DIR__.'/../Resources';
  }

  public function testReturnsCollectionOnEmpty() {
    $finder = Finder::create()
      ->files()
      ->in($this->getFixturesDir('null'));
    $discovery = new HtmlDiscovery($finder);
    $collection = $discovery->discover();
    $this->assertInstanceOf(PatternCollection::class, $collection);
    $this->assertCount(0, $collection);
  }

  public function testDiscoversPatternsInDir() {
    $finder = Finder::create()
      ->files()
      ->name('*.html')
      ->in($this->getFixturesDir());
    $discovery = new HtmlDiscovery($finder);
    $collection = $discovery->discover();
    $this->assertInstanceOf(PatternCollection::class, $collection);
    $this->assertCount(1, $collection);
  }

  public function testSetsPropertiesOnDiscoveredPatterns() {
    $finder = Finder::create()
      ->files()
      ->name('*.html')
      ->in($this->getFixturesDir());
    $discovery = new HtmlDiscovery($finder);
    $pattern = $discovery->discover()->get('button.html');
    $this->assertInstanceOf(HtmlPattern::class, $pattern);
    $this->assertEquals('YnV0dG9uLmh0bWw=', $pattern->getId());
    $this->assertEquals(['button.html'], $pattern->getAliases());
    $this->assertInstanceOf(SplFileInfo::class, $pattern->getFile());
    $this->assertEquals('button.html', $pattern->getFile()->getRelativePathname());
  }
}