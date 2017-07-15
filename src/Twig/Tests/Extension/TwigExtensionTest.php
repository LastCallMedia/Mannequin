<?php


namespace LastCall\Mannequin\Twig\Tests\Extension;

use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Tests\Extension\ExtensionTestCase;
use LastCall\Mannequin\Twig\Extension\TwigExtension;
use LastCall\Mannequin\Twig\Subscriber\InlineTwigYamlMetadataSubscriber;
use LastCall\Mannequin\Twig\Subscriber\TwigIncludeSubscriber;
use Prophecy\Argument;
use Prophecy\Prophecy\ObjectProphecy;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Finder\Finder;

class TwigExtensionTest extends ExtensionTestCase
{

    public function getExtension(): ExtensionInterface
    {
        return new TwigExtension(
            [
                'finder' => Finder::create()->in(__DIR__),
            ]
        );
    }

    public function getDispatcherProphecy(): ObjectProphecy
    {
        $dispatcher = $this->prophesize(EventDispatcherInterface::class);
        $dispatcher->addSubscriber(
            Argument::type(InlineTwigYamlMetadataSubscriber::class)
        )
            ->shouldBeCalled();
        $dispatcher->addSubscriber(Argument::type(TwigIncludeSubscriber::class))
            ->shouldBeCalled();

        return $dispatcher;
    }
}