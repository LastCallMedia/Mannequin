<?php


namespace LastCall\Mannequin\Core\Tests\Discovery;


use LastCall\Mannequin\Core\Discovery\ChainDiscovery;
use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Discovery\ExplicitDiscovery;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Pattern\PatternInterface;
use PHPUnit\Framework\TestCase;

class ChainDiscoveryTest extends TestCase {

  /**
   * @expectedException \InvalidArgumentException
   * @expectedExceptionMessage Discoverer must implement LastCall\Mannequin\Core\Discovery\DiscoveryInterface
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
    $pattern1Mock->getAliases()->willReturn(['pattern/1']);
    $pattern1 = $pattern1Mock->reveal();

    $pattern2Mock = $this->prophesize(PatternInterface::class);
    $pattern2Mock->getId()->willReturn('pattern2');
    $pattern2Mock->getAliases()->willReturn(['pattern/2']);
    $pattern2 = $pattern2Mock->reveal();

    $discoverer1 = new ExplicitDiscovery(new PatternCollection([$pattern1]));
    $discoverer2 = new ExplicitDiscovery(new PatternCollection([$pattern2]));

    $chain = new ChainDiscovery([$discoverer1, $discoverer2]);
    $merged = $chain->discover();
    $this->assertEquals([$pattern1, $pattern2], $merged->getPatterns());
  }
}