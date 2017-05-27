<?php


namespace LastCall\Mannequin\Html\Tests\Extension;


use LastCall\Mannequin\Core\Extension\ExtensionInterface;
use LastCall\Mannequin\Core\Tests\Extension\ExtensionTestCase;
use LastCall\Mannequin\Html\Extension\HtmlExtension;

class HtmlExtensionTest extends ExtensionTestCase {

  public function getExtension(): ExtensionInterface {
    return new HtmlExtension();
  }
}