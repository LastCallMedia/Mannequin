<?php

namespace LastCall\Mannequin\Twig\Iterator;

class TemplateFilenameMapper extends \IteratorIterator {

  private $paths = [];

  public function __construct(\Traversable $iterator) {
    parent::__construct($iterator);
  }

  public function addPath($path, $namespace = \Twig_Loader_Filesystem::MAIN_NAMESPACE) {
    $this->paths[$namespace][] = $path;
  }

  public function current() {
    $filename = realpath(parent::current());

    $discoveredNames = [];
    foreach($this->paths as $name => $paths) {
      foreach($paths as $path) {
        if(strpos($filename, $path) === 0) {
          $templateName = ltrim(substr($filename, strlen($path)), '/');
          $namespaceName = $name === \Twig_Loader_Filesystem::MAIN_NAMESPACE ? '' : sprintf('@%s/', $name);
          $discoveredNames[] = $namespaceName.$templateName;
        }
      }
    }

    if(!empty($discoveredNames)) {
      return $discoveredNames;
    }
    throw new \RuntimeException(sprintf('%s does not exist in any known namespace', $filename));
  }
}