<?php


namespace LastCall\Mannequin\Twig\Tests\Extension;

use LastCall\Mannequin\Core\ConfigInterface;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Tests\Extension\ExtensionTestCase;
use LastCall\Mannequin\Twig\Extension\TwigExtension;
use LastCall\Mannequin\Twig\Subscriber\InlineTwigYamlMetadataSubscriber;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcher;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class TwigExtensionTest extends ExtensionTestCase {

  public function getExtension(): ExtensionInterface {
    return new TwigExtension();
  }

  public function getDispatcherProphecy(): ObjectProphecy {
    $dispatcher = $this->prophesize(EventDispatcherInterface::class);
    $dispatcher->addSubscriber(Argument::type(InlineTwigYamlMetadataSubscriber::class))
      ->shouldBeCalled();

    return $dispatcher;
  }
}