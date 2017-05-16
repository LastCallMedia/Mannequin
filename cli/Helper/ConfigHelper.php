<?php


namespace LastCall\Mannequin\Cli\Helper;


use LastCall\Mannequin\Core\ConfigInterface;
use Symfony\Component\Console\Helper\HelperInterface;
use Symfony\Component\Console\Helper\HelperSet;

class ConfigHelper implements HelperInterface {

  private $helperSet;

  public function getName() {
    return 'patterns_config';
  }

  public function setHelperSet(HelperSet $helperSet = NULL) {
    $this->helperSet = $helperSet;
  }

  public function getHelperSet() {
    return $this->helperSet;
  }

  public function getConfig($file) {
    if(is_file($file)) {
      $config = include $file;
      if($config && $config instanceof ConfigInterface) {
        return $config;
      }
      throw new \RuntimeException('Config was not returned or not an instance of Config.');
    }
    throw new \RuntimeException(sprintf('Expected config in %s, but the file does not exist.', $file));
  }
}