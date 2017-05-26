<?php


namespace LastCall\Mannequin\Core\Tests\Extension;


use LastCall\Mannequin\Core\ConfigInterface;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Variable\SetResolver;
use PHPUnit\Framework\TestCase;
use Prophecy\Argument;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;

abstract class ExtensionTestCase extends TestCase {

  abstract public function getExtension(): ExtensionInterface;

  public function getConfig() {
    $config = $this->prophesize(ConfigInterface::class);
    $config->getCacheDir()->willReturn('');
    $config->getDispatcher()->willReturn(new EventDispatcher());
    $config->getVariableResolver()->willReturn(new SetResolver());
    $config->getStyles()->willReturn([]);
    $config->getScripts()->willReturn([]);
    return $config->reveal();
  }

  public function testAttachDispatcher() {
    $extension = $this->getExtension();
    $extension->setConfig($this->getConfig());
    $dispatcher = $this->prophesize(EventDispatcherInterface::class);
    $dispatcher->addSubscriber(Argument::type(EventSubscriberInterface::class))->willReturn(NULL);
    $extension->attachToDispatcher($dispatcher->reveal());

  }

  public function testGetRenderers() {
    $extension = $this->getExtension();
    $extension->setConfig($this->getConfig());
    $this->assertTrue(is_array($extension->getRenderers()));
  }

  public function testHasVariableResolvers() {
    $extension = $this->getExtension();
    $extension->setConfig($this->getConfig());
    $this->assertTrue(is_array($extension->getVariableResolvers()));
  }

  public function testHasDiscoverers() {
    $extension = $this->getExtension();
    $extension->setConfig($this->getConfig());
    $this->assertTrue(is_array($extension->getDiscoverers()));
  }

}