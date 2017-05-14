<?php


namespace LastCall\Patterns\Core\Tests;


use LastCall\Patterns\Core\Config;
use LastCall\Patterns\Core\Discovery\ChainDiscovery;
use LastCall\Patterns\Core\Extension\CoreExtension;
use LastCall\Patterns\Core\Render\DelegatingRenderer;
use LastCall\Patterns\Core\Variable\VariableFactory;
use PHPUnit\Framework\TestCase;

class ConfigTest extends TestCase {

  public function testHasDiscovery() {
    $config = new Config();
    $this->assertInstanceOf(ChainDiscovery::class, $config['discovery']);
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