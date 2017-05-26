<?php


namespace LastCall\Mannequin\Core\Tests\Extension;


use LastCall\Mannequin\Core\ConfigInterface;
use LastCall\Mannequin\Core\Extension\CoreExtension;
use LastCall\Mannequin\Core\Extension\ExtensionInterface;

class CoreExtensionTest extends ExtensionTestCase {

  public function getExtension(): ExtensionInterface {
    return new CoreExtension();
  }

  public function getConfig() {
    $config = $this->prophesize(ConfigInterface::class);
    return $config->reveal();
  }
}