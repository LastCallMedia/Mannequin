<?php

namespace LastCall\Mannequin\Twig\Mapper;

class TemplateFileMapperIterator extends \IteratorIterator {

  private $paths = [];
  private $resolver;

  public function __construct(\Traversable $iterator, callable $resolver) {
    parent::__construct($iterator);
    $this->resolver = $resolver;
  }

  public function addPath($path, $namespace = \Twig_Loader_Filesystem::MAIN_NAMESPACE) {
    $this->paths[$namespace][] = $path;
  }

  public function current() {
    $resolver = $this->resolver;
    return $resolver(parent::current());
  }
}