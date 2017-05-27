<?php


namespace LastCall\Mannequin\Core\Tests;


use LastCall\Mannequin\Core\Config;
use LastCall\Mannequin\Core\ConfigInterface;
use LastCall\Mannequin\Core\Discovery\ChainDiscovery;
use LastCall\Mannequin\Core\Discovery\DiscoveryInterface;
use LastCall\Mannequin\Core\Extension\CoreExtension;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Pattern\PatternCollection;
use LastCall\Mannequin\Core\Render\DelegatingRenderer;
use LastCall\Mannequin\Core\Variable\ResolverInterface;
use LastCall\Mannequin\Core\Variable\SetResolver;
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

  public function testUsesExtensionVariableResolvers() {
    $resolver = $this->prophesize(ResolverInterface::class);
    $resolver->resolves('foo')->willReturn(TRUE);

    $extension = $this->getMockExtension();
    $extension->getVariableResolvers()
      ->willReturn([$resolver])
      ->shouldBeCalled();

    $extension->getVariableResolvers()->shouldBeCalled();
    $config = new Config();
    $config->addExtension($extension->reveal());
    $this->assertTrue($config->getVariableResolver()->resolves('foo'));
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
    $this->assertInstanceOf(SetResolver::class, $config->getVariableResolver());
  }
}