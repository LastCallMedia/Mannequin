<?php


namespace LastCall\Mannequin\Core\Ui;

use Symfony\Component\Filesystem\Filesystem;

class FileWriter {
  private $dir;
  private $fs;

  public function __construct($dir) {
    $this->dir = $dir;
    $this->fs = new Filesystem();
  }

  public function raw($path, $contents) {
    $this->fs->dumpFile(sprintf('%s/%s', $this->dir, $path), $contents);
  }

  public function copy($src, $dest) {
    if(is_file($src)) {
      return $this->fs->copy($src, sprintf('%s/%s', $this->dir, $dest));
    }
    elseif(is_dir($src)) {
      return $this->fs->mirror($src, sprintf('%s/%s', $this->dir, $dest));
    }
    throw new \RuntimeException(sprintf('Source file does not exist: %s', $src));
  }
}