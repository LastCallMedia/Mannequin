<?php


namespace LastCall\Mannequin\Core\Tests\Extension;


use LastCall\Mannequin\Core\ConfigInterface;
use LastCall\Mannequin\Core\Extension\CoreExtension;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Subscriber\LastChanceNameSubscriber;
use LastCall\Mannequin\Core\Subscriber\NestedPatternVariableSubscriber;
use LastCall\Mannequin\Core\Subscriber\YamlFileMetadataSubscriber;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class CoreExtensionTest extends ExtensionTestCase {

  public function getExtension(): ExtensionInterface {
    return new CoreExtension();
  }

  public function getConfig(): ConfigInterface {
    $config = $this->prophesize(ConfigInterface::class);
    return $config->reveal();
  }

  protected function getDispatcherProphecy(): ObjectProphecy {
    $dispatcher = $this->prophesize(EventDispatcherInterface::class);
    $dispatcher->addSubscriber(Argument::type(YamlFileMetadataSubscriber::class))->shouldBeCalled();
    $dispatcher->addSubscriber(Argument::type(NestedPatternVariableSubscriber::class))->shouldBeCalled();
    $dispatcher->addSubscriber(Argument::type(LastChanceNameSubscriber::class))->shouldBeCalled();
    return $dispatcher;
  }
}