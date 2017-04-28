<?php

namespace LastCall\Patterns\Cli\Writer;

use LastCall\Patterns\Core\Render\RenderedInterface;
use Symfony\Component\Filesystem\Filesystem;

class FileWriter {

  public function __construct(string $dir) {
    $this->dir = $dir;
    $this->filesystem = new Filesystem();
  }


  public function prepare() {
    if(!$this->filesystem->exists($this->dir)) {
      $this->filesystem->mkdir($this->dir);
    }
  }

  public function write(RenderedInterface $rendered) {
    $name = sprintf('%s%s%s.html', $this->dir, DIRECTORY_SEPARATOR, $rendered->getId());
    print $name;
    $this->filesystem->dumpFile($name, $rendered->getMarkup());
  }

  public function finalize() {
    // No-op yet.
  }
}