<?php


namespace LastCall\Mannequin\Twig;

/**
 * This class is used as a wrapper for an iterator that returns absolute
 * paths to templates back to the relative path by which the template is
 * known to the twig loader.
 *
 * It can be used with namespacing by calling setPaths() with the namespaced
 * paths.
 */
class TemplateFilenameIterator extends \IteratorIterator {

  private $paths = [];

  public function __construct(\Traversable $iterator, array $paths = []) {
    parent::__construct($iterator);
    $this->setPaths($paths);
    $this->rewind();
  }

  public function setPaths(array $paths, string $namespace = \Twig_Loader_Filesystem::MAIN_NAMESPACE) {
    $this->paths[$namespace] = [];
    foreach($paths as $path) {
      $this->addPath($path, $namespace);
    }
  }

  public function addPath($path, string $namespace = \Twig_Loader_Filesystem::MAIN_NAMESPACE) {
    $this->paths[$namespace][] = $path;
  }

  public function current() {
    $file = parent::current();
    return $this->mapFilenameToNamespacedNames($file);
  }

  private function mapFilenameToNamespacedNames(string $filename) {
    foreach($this->paths as $name => $paths) {
      foreach($paths as $path) {
        if(strpos($filename, $path) === 0) {
          $templateName = ltrim(substr($filename, strlen($path)), '/');
          $namespaceName = $name === \Twig_Loader_Filesystem::MAIN_NAMESPACE ? '' : sprintf('@%s/', $name);
          return $namespaceName.$templateName;
        }
      }
    }
    throw new \InvalidArgumentException(sprintf('%s does not exist in any known namespace', $filename));
  }

  public function addPathsFromTwigLoader(\Twig_Loader_Filesystem $loader) {
    foreach($loader->getNamespaces() as $namespace) {
      foreach($loader->getPaths($namespace) as $path) {
        $this->addPath($path, $namespace);
      }
    }
  }


}