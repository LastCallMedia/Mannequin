<?php


namespace LastCall\Mannequin\Twig\Tests\Extension;

use LastCall\Mannequin\Core\ConfigInterface;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Tests\Extension\ExtensionTestCase;
use LastCall\Mannequin\Twig\Extension\TwigExtension;
use Symfony\Component\EventDispatcher\EventDispatcher;

class TwigExtensionTest extends ExtensionTestCase {

  public function getExtension(): ExtensionInterface {
    return new TwigExtension();
  }

}