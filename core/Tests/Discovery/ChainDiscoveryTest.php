<?php


namespace LastCall\Patterns\Core\Tests\Discovery;


use LastCall\Patterns\Core\Discovery\ChainDiscovery;
use LastCall\Patterns\Core\Discovery\DiscoveryInterface;
use LastCall\Patterns\Core\Discovery\ExplicitDiscovery;
use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\Pattern\PatternInterface;
use PHPUnit\Framework\TestCase;

class ChainDiscoveryTest extends TestCase {

  /**
   * @expectedException \InvalidArgumentException
   * @expectedExceptionMessage Discoverer must implement LastCall\Patterns\Core\Discovery\DiscoveryInterface
   */
  public function testInvalidDiscoverer() {
    $discoverer = new \stdClass();
    new ChainDiscovery([$discoverer]);
  }

  public function testCallsDiscoverers() {
    $discoverer = $this->prophesize(DiscoveryInterface::class);
    $discoverer->discover()
      ->shouldBeCalled();

    $chain = new ChainDiscovery([$discoverer->reveal()]);
    $chain->discover();
  }

  public function testMergesCollection() {
    $pattern1Mock = $this->prophesize(PatternInterface::class);
    $pattern1Mock->getId()->willReturn('pattern1');
    $pattern1 = $pattern1Mock->reveal();

    $pattern2Mock = $this->prophesize(PatternInterface::class);
    $pattern2Mock->getId()->willReturn('pattern2');
    $pattern2 = $pattern2Mock->reveal();

    $discoverer1 = new ExplicitDiscovery(new PatternCollection([$pattern1]));
    $discoverer2 = new ExplicitDiscovery(new PatternCollection([$pattern2]));

    $chain = new ChainDiscovery([$discoverer1, $discoverer2]);
    $merged = $chain->discover();
    $this->assertEquals([$pattern1, $pattern2], $merged->getPatterns());
  }
}