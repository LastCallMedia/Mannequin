<?php


namespace LastCall\Patterns\Core\Tests;


use LastCall\Patterns\Core\Config;
use LastCall\Patterns\Core\ConfigInterface;
use LastCall\Patterns\Core\Discovery\ChainDiscovery;
use LastCall\Patterns\Core\Discovery\DiscoveryInterface;
use LastCall\Patterns\Core\Extension\CoreExtension;
use LastCall\Patterns\Core\Extension\ExtensionInterface;
use LastCall\Patterns\Core\Pattern\PatternCollection;
use LastCall\Patterns\Core\Render\DelegatingRenderer;
use LastCall\Patterns\Core\Variable\ScalarFactory;
use LastCall\Patterns\Core\Variable\VariableFactory;
use LastCall\Patterns\Core\Variable\VariableFactoryInterface;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;

class ConfigTest extends TestCase {

  public function testSetsConfigWhenExtensionsAreUsed() {
    $extension = $this->prophesize(ExtensionInterface::class);
    $config = new Config();
    $extension->setConfig($config)->shouldBeCalled();
    $config->addExtension($extension->reveal());
    $config->getExtensions();
  }

  public function testHasDiscovery() {
    $config = new Config();
    $this->assertInstanceOf(ChainDiscovery::class, $config['discovery']);
  }

  private function getMockExtension() {
    $extension = $this->prophesize(ExtensionInterface::class);
    $extension->setConfig(Argument::type(ConfigInterface::class))->will(function() {});
    return $extension;
  }

  public function testUsesExtensionDiscoverers() {
    $discoverer = $this->prophesize(DiscoveryInterface::class);
    $discoverer->discover()
      ->willReturn(new PatternCollection())
      ->shouldBeCalled();
    $extension = $this->getMockExtension();
    $extension->getDiscoverers()->willReturn([$discoverer]);
    $config = new Config();
    $config->addExtension($extension->reveal());
    $config->getCollection();
  }

  public function testUsesExtensionVariableFactories() {
    $factory = $this->prophesize(VariableFactoryInterface::class);
    $factory->getTypes()->willReturn(['foo']);

    $extension = $this->getMockExtension();
    $extension->getVariableFactories()
      ->willReturn([$factory])
      ->shouldBeCalled();

    $extension->getVariableFactories()->shouldBeCalled();
    $config = new Config();
    $config->addExtension($extension->reveal());
    $this->assertTrue($config->getVariableFactory()->hasType('foo'));
  }

  public function testHasCoreExtension() {
    $config = new Config();
    $extensions = $config->getExtensions();
    $this->assertCount(1, $extensions);
    $this->assertInstanceOf(CoreExtension::class, reset($extensions));
  }

  public function testHasRenderer() {
    $config = new Config();
    $this->assertInstanceOf(DelegatingRenderer::class, $config->getRenderer());
  }

  public function testHasVariableFactory() {
    $config = new Config();
    $this->assertInstanceOf(VariableFactory::class, $config->getVariableFactory());
  }
}